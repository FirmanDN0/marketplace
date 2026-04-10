<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        if (auth()->user()->email_verified_at) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request, string $token)
    {
        $record = DB::table('email_verification_tokens')
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('login')->withErrors(['email' => 'Link verifikasi tidak valid.']);
        }

        // Check expiry (24 hours)
        if (now()->diffInHours($record->created_at) > 24) {
            DB::table('email_verification_tokens')->where('token', $token)->delete();
            return redirect()->route('verification.notice')
                ->with('error', 'Link verifikasi sudah expired. Kirim ulang.');
        }

        $user = User::where('email', $record->email)->first();
        if ($user && !$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        DB::table('email_verification_tokens')->where('email', $record->email)->delete();

        if (auth()->check()) {
            return redirect()->route('home')->with('success', 'Email berhasil diverifikasi!');
        }

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function resend(Request $request)
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Email sudah diverifikasi.');
        }

        static::sendVerificationEmail($user);

        return back()->with('status', 'Link verifikasi baru telah dikirim ke email kamu.');
    }

    /**
     * Send verification email to a user.
     */
    public static function sendVerificationEmail(User $user): void
    {
        // Delete old tokens
        DB::table('email_verification_tokens')->where('email', $user->email)->delete();

        $token = Str::random(64);

        DB::table('email_verification_tokens')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        $verifyUrl = route('verification.verify', ['token' => $token]);

        Mail::send('emails.verify-email', [
            'user'      => $user,
            'verifyUrl' => $verifyUrl,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Verifikasi Email - ServeMix');
        });
    }
}
