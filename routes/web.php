<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\ServiceController as AdminService;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\DisputeController as AdminDispute;
use App\Http\Controllers\Admin\WithdrawController as AdminWithdraw;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ReviewController as AdminReview;
use App\Http\Controllers\Admin\CustomerServiceController as AdminCS;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboard;
use App\Http\Controllers\Provider\ServiceController as ProviderService;
use App\Http\Controllers\Provider\OrderController as ProviderOrder;
use App\Http\Controllers\Provider\WithdrawController as ProviderWithdraw;
use App\Http\Controllers\Provider\ReviewController as ProviderReview;
use App\Http\Controllers\Provider\OnboardingController as ProviderOnboarding;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\Customer\ReviewController as CustomerReview;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProviderProfileController;

// ─── Public ───────────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');

// ─── Auth ─────────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:3,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Email Verification ───────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend')->middleware('throttle:3,1');
});
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

// --- Midtrans Webhooks (no auth, called by Midtrans server) ---
Route::post('/topup/notification', [TopUpController::class, 'notification'])->name('topup.notification');
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// ─── Authenticated (shared) ────────────────────────────────────────────────────

Route::middleware(['auth', 'active', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/start', [MessageController::class, 'startOrFind'])->name('messages.start');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send')->middleware('throttle:30,1');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/sync-counts', [NotificationController::class, 'syncCounts'])->name('notifications.sync-counts');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.all-read');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{service_id}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Wallet & Top-Up (all authenticated users)
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/withdraw', [WalletController::class, 'withdrawCreate'])->name('wallet.withdraw.create');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdrawStore'])->name('wallet.withdraw.store')->middleware('throttle:5,1');
    Route::get('/topup', [TopUpController::class, 'create'])->name('wallet.topup.create');
    Route::post('/topup', [TopUpController::class, 'store'])->name('wallet.topup.store')->middleware('throttle:10,1');
    Route::get('/topup/history', [TopUpController::class, 'history'])->name('wallet.topup.history');
    Route::get('/topup/{topUp}/finish', [TopUpController::class, 'finish'])->name('wallet.topup.finish');

    // Customer Service (all authenticated users)
    Route::get('/customer-service', [CustomerServiceController::class, 'index'])->name('customer-service.index');
    Route::get('/customer-service/start', [CustomerServiceController::class, 'start'])->name('customer-service.start');
    Route::get('/customer-service/{conversation}', [CustomerServiceController::class, 'show'])->name('customer-service.show');
    Route::post('/customer-service/{conversation}/messages', [CustomerServiceController::class, 'sendMessage'])->name('customer-service.message')->middleware('throttle:20,1');
    Route::post('/customer-service/{conversation}/escalate', [CustomerServiceController::class, 'escalate'])->name('customer-service.escalate');

    // Payment
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/wallet', [PaymentController::class, 'payWithWallet'])->name('payment.wallet');
    Route::get('/payment/{order}/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{order}/failed', [PaymentController::class, 'failed'])->name('payment.failed');
});

// ─── Admin ────────────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUser::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUser::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUser::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/status', [AdminUser::class, 'updateStatus'])->name('users.status');

    // Categories
    Route::get('/categories', [AdminCategory::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategory::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategory::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategory::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategory::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategory::class, 'destroy'])->name('categories.destroy');

    // Services
    Route::get('/services', [AdminService::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [AdminService::class, 'show'])->name('services.show');
    Route::patch('/services/{service}/status', [AdminService::class, 'updateStatus'])->name('services.status');
    Route::delete('/services/{service}', [AdminService::class, 'destroy'])->name('services.destroy');

    // Orders
    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [AdminOrder::class, 'cancel'])->name('orders.cancel');

    // Disputes
    Route::get('/disputes', [AdminDispute::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{dispute}', [AdminDispute::class, 'show'])->name('disputes.show');
    Route::post('/disputes/{dispute}/resolve', [AdminDispute::class, 'resolve'])->name('disputes.resolve');

    // Withdrawals
    Route::get('/withdrawals', [AdminWithdraw::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/{withdrawRequest}/approve', [AdminWithdraw::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawRequest}/reject', [AdminWithdraw::class, 'reject'])->name('withdrawals.reject');

    // Reports
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports');

    // Reviews
    Route::get('/reviews', [AdminReview::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/toggle', [AdminReview::class, 'toggleVisibility'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [AdminReview::class, 'destroy'])->name('reviews.destroy');

    // Customer Service Management
    Route::get('/customer-service', [AdminCS::class, 'index'])->name('customer-service.index');
    Route::get('/customer-service/{conversation}', [AdminCS::class, 'show'])->name('customer-service.show');
    Route::post('/customer-service/{conversation}/messages', [AdminCS::class, 'sendMessage'])->name('customer-service.message');
    Route::post('/customer-service/{conversation}/close', [AdminCS::class, 'close'])->name('customer-service.close');
    Route::post('/customer-service/{conversation}/reopen', [AdminCS::class, 'reopen'])->name('customer-service.reopen');
});

// ─── Provider ─────────────────────────────────────────────────────────────────

// Onboarding routes (no onboarding middleware — these ARE the onboarding)
Route::prefix('provider/onboarding')->name('provider.onboarding.')->middleware(['auth', 'verified', 'role:provider'])->group(function () {
    Route::get('/step/{step}', [ProviderOnboarding::class, 'show'])->name('show');
    Route::post('/step/{step}', [ProviderOnboarding::class, 'save'])->name('save');
    Route::get('/complete', [ProviderOnboarding::class, 'complete'])->name('complete');
});

Route::prefix('provider')->name('provider.')->middleware(['auth', 'active', 'verified', 'role:provider', 'provider.onboarding'])->group(function () {
    Route::get('/', [ProviderDashboard::class, 'index'])->name('dashboard');

    // Services
    Route::get('/services', [ProviderService::class, 'index'])->name('services.index');
    Route::get('/services/create', [ProviderService::class, 'create'])->name('services.create');
    Route::post('/services', [ProviderService::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ProviderService::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ProviderService::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ProviderService::class, 'update'])->name('services.update');
    Route::post('/services/{service}/toggle', [ProviderService::class, 'toggleStatus'])->name('services.toggle');
    Route::delete('/services/{service}', [ProviderService::class, 'destroy'])->name('services.destroy');

    // Orders
    Route::get('/orders', [ProviderOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ProviderOrder::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/deliver', [ProviderOrder::class, 'deliver'])->name('orders.deliver');
    Route::patch('/orders/{order}/cancel', [ProviderOrder::class, 'cancel'])->name('orders.cancel');

    // Reviews
    Route::post('/reviews/{review}/reply', [ProviderReview::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}/reply', [ProviderReview::class, 'deleteReply'])->name('reviews.reply.delete');

    // Withdrawals
    Route::get('/withdraw', [ProviderWithdraw::class, 'index'])->name('withdraw.index');
    Route::get('/withdraw/create', [ProviderWithdraw::class, 'create'])->name('withdraw.create');
    Route::post('/withdraw', [ProviderWithdraw::class, 'store'])->name('withdraw.store')->middleware('throttle:5,1');
});

// ─── Customer ─────────────────────────────────────────────────────────────────

Route::prefix('customer')->name('customer.')->middleware(['auth', 'active', 'verified', 'role:customer'])->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [CustomerOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [CustomerOrder::class, 'create'])->name('orders.create');
    Route::post('/orders', [CustomerOrder::class, 'store'])->name('orders.store')->middleware('throttle:10,1');
    Route::get('/orders/{order}', [CustomerOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/requirements', [CustomerOrder::class, 'submitRequirements'])->name('orders.requirements');
    Route::patch('/orders/{order}/accept', [CustomerOrder::class, 'accept'])->name('orders.accept');
    Route::patch('/orders/{order}/revision', [CustomerOrder::class, 'requestRevision'])->name('orders.revision');
    Route::patch('/orders/{order}/cancel', [CustomerOrder::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/dispute', [CustomerOrder::class, 'dispute'])->name('orders.dispute');

    // Reviews
    Route::get('/orders/{order}/review', [CustomerReview::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review', [CustomerReview::class, 'store'])->name('reviews.store');
});

// ─── Public Provider Profile ──────────────────────────────────────────────────
Route::get('/provider/{username}', [ProviderProfileController::class, 'show'])->name('provider.profile');
