<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user    = auth()->user();
        $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);
        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'username'         => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'bio'              => 'nullable|string|max:1000',
            'phone'            => 'nullable|string|max:20',
            'country'          => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'website'          => 'nullable|url|max:255',
            'skills'           => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'hourly_rate'      => 'nullable|numeric|min:0',
        ]);

        $user->update([
            'name'     => $data['name'],
            'username' => $data['username'],
        ]);

        $profileData = [
            'bio'              => $data['bio'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'country'          => $data['country'] ?? null,
            'city'             => $data['city'] ?? null,
            'website'          => $data['website'] ?? null,
            'skills'           => $data['skills'] ? array_filter(array_map('trim', explode(',', $data['skills']))) : null,
            'experience_years' => $data['experience_years'] ?? 0,
            'hourly_rate'      => $data['hourly_rate'] ?? null,
        ];

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Avatar updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8',
        ]);

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['delete_password' => 'Password salah.']);
        }

        // Prevent deletion if user has active orders
        $activeOrders = $user->isProvider()
            ? $user->ordersAsProvider()->whereIn('status', ['paid', 'in_progress', 'delivered'])->count()
            : $user->ordersAsCustomer()->whereIn('status', ['paid', 'in_progress', 'delivered'])->count();

        if ($activeOrders > 0) {
            return back()->withErrors(['delete_password' => 'Tidak bisa hapus akun karena masih ada order aktif.']);
        }

        // Delete avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->profile()?->delete();
        $user->delete();

        return redirect()->route('home')->with('success', 'Akun berhasil dihapus.');
    }
}
