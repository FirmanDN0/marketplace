<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Email/username atau password salah.'])->withInput();
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['login' => 'Akun kamu tidak aktif.']);
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:100',
            'username'             => 'required|string|max:50|unique:users|alpha_dash',
            'email'                => 'required|email|unique:users',
            'password'             => ['required', 'confirmed', Password::min(8)],
            'role'                 => 'required|in:customer,provider',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Harap centang verifikasi reCAPTCHA.',
        ]);

        // Verify reCAPTCHA with Google
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ])->json();

        if (empty($recaptchaResponse['success'])) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'status'   => 'active',
        ]);

        UserProfile::create(['user_id' => $user->id]);

        Auth::login($user);

        EmailVerificationController::sendVerificationEmail($user);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        if ($user->isProvider() && !$user->hasCompletedOnboarding()) {
            return redirect()->route('provider.onboarding.show', $user->provider_setup_step + 1);
        }

        return match ($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'provider' => redirect()->route('provider.dashboard'),
            default    => redirect()->route('customer.dashboard'),
        };
    }
}
