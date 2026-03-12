<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    private const TOTAL_STEPS = 3;

    public function show(int $step)
    {
        $user = auth()->user();

        if ($step < 1 || $step > self::TOTAL_STEPS) {
            return redirect()->route('provider.onboarding.show', 1);
        }

        // Prevent skipping steps
        if ($step > $user->provider_setup_step + 1) {
            return redirect()->route('provider.onboarding.show', $user->provider_setup_step + 1);
        }

        $user->load('profile');

        return view("provider.onboarding.step{$step}", compact('user'));
    }

    public function save(Request $request, int $step)
    {
        $user = auth()->user();

        match ($step) {
            1 => $this->saveStep1($request, $user),
            2 => $this->saveStep2($request, $user),
            3 => $this->saveStep3($request, $user),
            default => null,
        };

        // Advance step if needed
        if ($user->provider_setup_step < $step) {
            $user->update(['provider_setup_step' => $step]);
        }

        if ($step >= self::TOTAL_STEPS) {
            return redirect()->route('provider.onboarding.complete');
        }

        return redirect()->route('provider.onboarding.show', $step + 1);
    }

    public function complete()
    {
        $user = auth()->user();

        if ($user->provider_setup_step < self::TOTAL_STEPS) {
            return redirect()->route('provider.onboarding.show', $user->provider_setup_step + 1);
        }

        return view('provider.onboarding.complete', compact('user'));
    }

    // -----------------------------------------------------------------------

    private function saveStep1(Request $request, $user): void
    {
        $data = $request->validate([
            'bio'    => 'required|string|min:20|max:1000',
            'phone'  => 'required|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'bio.required'    => 'Bio wajib diisi.',
            'bio.min'         => 'Bio minimal 20 karakter.',
            'phone.required'  => 'Nomor HP wajib diisi.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->profile->update([
            'bio'   => $data['bio'],
            'phone' => $data['phone'],
        ]);
    }

    private function saveStep2(Request $request, $user): void
    {
        $data = $request->validate([
            'skills'           => 'required|array|min:1|max:10',
            'skills.*'         => 'string|max:50',
            'languages'        => 'required|array|min:1',
            'languages.*'      => 'string|max:50',
            'experience_years' => 'required|integer|min:0|max:50',
        ], [
            'skills.required'           => 'Pilih minimal 1 keahlian.',
            'languages.required'        => 'Pilih minimal 1 bahasa.',
            'experience_years.required' => 'Lama pengalaman wajib diisi.',
        ]);

        $user->profile->update([
            'skills'           => $data['skills'],
            'languages'        => $data['languages'],
            'experience_years' => $data['experience_years'],
        ]);
    }

    private function saveStep3(Request $request, $user): void
    {
        $data = $request->validate([
            'country'     => 'required|string|max:100',
            'city'        => 'required|string|max:100',
            'website'     => 'nullable|url|max:255',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999999',
        ], [
            'country.required' => 'Negara wajib diisi.',
            'city.required'    => 'Kota wajib diisi.',
            'website.url'      => 'Format URL website tidak valid.',
        ]);

        $user->profile->update([
            'country'     => $data['country'],
            'city'        => $data['city'],
            'website'     => $data['website'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
        ]);
    }
}
