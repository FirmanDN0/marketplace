<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProviderOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->isProvider() && !$user->hasCompletedOnboarding()) {
            // Allow access to onboarding routes and logout
            if ($request->routeIs('provider.onboarding.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            $nextStep = $user->provider_setup_step + 1;
            return redirect()->route('provider.onboarding.show', $nextStep)
                ->with('info', 'Lengkapi profil provider kamu terlebih dahulu.');
        }

        return $next($request);
    }
}
