<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe user to a plan.
     */
    public function subscribe(User $user, SubscriptionPlan $plan): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan) {
            // Check if already subscribed
            $existing = $this->getCurrentSubscription($user);
            if ($existing && $existing->isActive()) {
                throw new \Exception('User already has an active subscription.');
            }

            // Create subscription (pending until payment)
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Create order for payment system
            $order = Order::create([
                'order_number' => 'SUB-' . strtoupper(uniqid()),
                'customer_id' => $user->id,
                'provider_id' => 1, // System/admin
                'service_id' => null,
                'price' => $plan->price,
                'status' => 'pending_payment',
            ]);

            // Initiate payment via existing PaymentService
            $payment = app(PaymentService::class)->initiate($order, 'midtrans');

            // Link payment to subscription
            $subscription->update(['payment_id' => $payment->id]);

            // Clear subscription cache
            Cache::forget("subscription:{$user->id}");

            return $subscription;
        });
    }

    /**
     * Cancel subscription (ends at current period end).
     */
    public function cancel(UserSubscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Clear cache
            Cache::forget("subscription:{$subscription->user_id}");

            // Send notification
            NotificationService::send(
                $subscription->user_id,
                'subscription_cancelled',
                'Subscription Cancelled',
                "Your {$subscription->plan->name} subscription has been cancelled. Access will continue until " . $subscription->current_period_end->format('d M Y') . ".",
                ['subscription_id' => $subscription->id],
                route('customer.subscription')
            );
        });
    }

    /**
     * Upgrade subscription to a new plan (immediate).
     */
    public function upgrade(UserSubscription $subscription, SubscriptionPlan $newPlan): UserSubscription
    {
        return DB::transaction(function () use ($subscription, $newPlan) {
            if (!$subscription->canUpgrade($newPlan)) {
                throw new \Exception('Cannot upgrade to a lower tier plan.');
            }

            // Calculate prorated amount
            $daysRemaining = $subscription->daysRemaining();
            $daysInMonth = 30;
            $proratedCredit = ($subscription->plan->price / $daysInMonth) * $daysRemaining;
            $newAmount = max(0, $newPlan->price - $proratedCredit);

            // Create new subscription
            $newSubscription = UserSubscription::create([
                'user_id' => $subscription->user_id,
                'plan_id' => $newPlan->id,
                'status' => 'pending',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Create order for upgrade payment
            $order = Order::create([
                'order_number' => 'SUB-UPGRADE-' . strtoupper(uniqid()),
                'customer_id' => $subscription->user_id,
                'provider_id' => 1,
                'service_id' => null,
                'price' => $newAmount,
                'status' => 'pending_payment',
            ]);

            // Initiate payment
            $payment = app(PaymentService::class)->initiate($order, 'midtrans');
            $newSubscription->update(['payment_id' => $payment->id]);

            // Cancel old subscription
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Clear cache
            Cache::forget("subscription:{$subscription->user_id}");

            return $newSubscription;
        });
    }

    /**
     * Activate subscription after successful payment.
     */
    public function activate(UserSubscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Create subscription payment record
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'payment_id' => $subscription->payment_id,
                'amount' => $subscription->plan->price,
                'billing_period_start' => $subscription->current_period_start,
                'billing_period_end' => $subscription->current_period_end,
                'status' => 'success',
            ]);

            // Clear cache
            Cache::forget("subscription:{$subscription->user_id}");

            // Send notification
            NotificationService::send(
                $subscription->user_id,
                'subscription_activated',
                'Subscription Activated',
                "Your {$subscription->plan->name} subscription is now active! Enjoy premium features.",
                ['subscription_id' => $subscription->id],
                route('customer.subscription')
            );
        });
    }

    /**
     * Get current active subscription for user.
     */
    public function getCurrentSubscription(User $user): ?UserSubscription
    {
        return Cache::remember("subscription:{$user->id}", 1800, function () use ($user) {
            return UserSubscription::with('plan')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('current_period_end', '>', now())
                ->first();
        });
    }

    /**
     * Check if user can access a video resolution.
     */
    public function canAccessResolution(User $user, string $resolution): bool
    {
        $subscription = $this->getCurrentSubscription($user);

        // No subscription = FREE tier (720p max)
        if (!$subscription) {
            $maxResolution = '720p';
        } else {
            $maxResolution = $subscription->plan->max_resolution;
        }

        $resolutions = ['360p', '480p', '720p', '1080p', '4k'];
        $maxIndex = array_search($maxResolution, $resolutions);
        $requestedIndex = array_search($resolution, $resolutions);

        return $requestedIndex !== false && $requestedIndex <= $maxIndex;
    }

    /**
     * Check if user has upload quota remaining.
     */
    public function hasUploadQuota(User $user): bool
    {
        $subscription = $this->getCurrentSubscription($user);

        // No subscription = FREE tier (5 uploads/month)
        if (!$subscription) {
            $maxUploads = 5;
        } else {
            $maxUploads = $subscription->plan->max_uploads_per_month;

            // NULL means unlimited
            if (is_null($maxUploads)) {
                return true;
            }
        }

        // Count uploads this month
        $uploadsThisMonth = Video::where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return $uploadsThisMonth < $maxUploads;
    }

    /**
     * Get upload quota info for user.
     */
    public function getUploadQuota(User $user): array
    {
        $subscription = $this->getCurrentSubscription($user);

        if (!$subscription) {
            $maxUploads = 5;
        } else {
            $maxUploads = $subscription->plan->max_uploads_per_month;
        }

        $uploadsThisMonth = Video::where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'used' => $uploadsThisMonth,
            'max' => $maxUploads,
            'remaining' => is_null($maxUploads) ? 'Unlimited' : max(0, $maxUploads - $uploadsThisMonth),
            'unlimited' => is_null($maxUploads),
        ];
    }

    /**
     * Expire subscriptions past their end date.
     */
    public function expireSubscriptions(): int
    {
        $expired = UserSubscription::where('status', 'active')
            ->where('current_period_end', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);

            // Clear cache
            Cache::forget("subscription:{$subscription->user_id}");

            // Send notification
            NotificationService::send(
                $subscription->user_id,
                'subscription_expired',
                'Subscription Expired',
                "Your {$subscription->plan->name} subscription has expired. Renew to continue enjoying premium features.",
                ['subscription_id' => $subscription->id],
                route('customer.subscription.plans')
            );
        }

        return $expired->count();
    }

    /**
     * Send renewal reminders for expiring subscriptions.
     */
    public function sendRenewalReminders(): int
    {
        $expiringSoon = UserSubscription::where('status', 'active')
            ->whereBetween('current_period_end', [now(), now()->addDays(3)])
            ->get();

        foreach ($expiringSoon as $subscription) {
            NotificationService::send(
                $subscription->user_id,
                'subscription_expiring',
                'Subscription Expiring Soon',
                "Your {$subscription->plan->name} subscription expires in {$subscription->daysRemaining()} days. Renew now to avoid interruption.",
                ['subscription_id' => $subscription->id],
                route('customer.subscription')
            );
        }

        return $expiringSoon->count();
    }
}
