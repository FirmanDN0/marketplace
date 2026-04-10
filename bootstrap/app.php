<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role'                => \App\Http\Middleware\RoleMiddleware::class,
            'active'              => \App\Http\Middleware\EnsureUserIsActive::class,
            'verified'            => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'provider.onboarding' => \App\Http\Middleware\EnsureProviderOnboarding::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'topup/notification',
            'payment/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
