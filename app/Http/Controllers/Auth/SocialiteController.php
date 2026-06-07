<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google details if they don't exist
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
                Auth::login($user);
            } else {
                // Generate a unique username
                $baseUsername = Str::slug($googleUser->getName() ?? 'user');
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                // Create a new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'username' => $username,
                    'password' => Hash::make(Str::password(16)), // Random secure password
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'email_verified_at' => now(), // Assume verified if from Google
                    'role' => 'customer',
                    'status' => 'active',
                ]);

                // Create default user profile
                $user->profile()->create();

                Auth::login($user);
            }

            // Redirect based on role
            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login menggunakan Google gagal. Silakan coba lagi. (' . $e->getMessage() . ')');
        }
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isProvider()) {
            if (!$user->hasCompletedOnboarding()) {
                return redirect()->route('provider.onboarding.show', ['step' => 1]);
            }
            return redirect()->route('provider.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
}
