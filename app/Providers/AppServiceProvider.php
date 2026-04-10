<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config as MidtransConfig;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            if ($user && $user->role === 'admin') {
                $view->with('adminPendingCounts', [
                    'disputes'    => \App\Models\Dispute::whereIn('status', ['open', 'under_review'])->count(),
                    'withdrawals' => \App\Models\WithdrawRequest::where('status', 'pending')->count(),
                    'services'    => \App\Models\Service::where('status', 'pending')->count(),
                    'cs'          => \App\Models\CsConversation::where('status', 'human')->count(),
                ]);
            }
        });
    }
}
