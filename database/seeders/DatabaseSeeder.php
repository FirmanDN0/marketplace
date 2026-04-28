<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payments')->truncate();
        DB::table('payment_logs')->truncate();
        DB::table('transactions')->truncate();
        DB::table('reviews')->truncate();
        DB::table('orders')->truncate();
        DB::table('service_packages')->truncate();
        DB::table('services')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now      = now();
        $password = Hash::make('password');

        // ── Admin ──────────────────────────────────────────────────────────────
        User::create([
            'name'              => 'Super Admin',
            'username'          => 'admin',
            'email'             => 'admin@marketplace.com',
            'password'          => $password,
            'role'              => 'admin',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);

        // ── Provider ───────────────────────────────────────────────────────────
        $provider = User::create([
            'name'                => 'Provider Demo',
            'username'            => 'provider',
            'email'               => 'provider@marketplace.com',
            'password'            => $password,
            'role'                => 'provider',
            'status'              => 'active',
            'provider_setup_step' => 3,
            'email_verified_at'   => $now,
        ]);

        UserProfile::create([
            'user_id'          => $provider->id,
            'bio'              => 'Profesional berpengalaman di bidang pengembangan web dan desain digital.',
            'skills'           => ['Laravel', 'Vue.js', 'UI/UX Design'],
            'country'          => 'Indonesia',
            'city'             => 'Jakarta',
            'languages'        => ['Bahasa Indonesia', 'English'],
            'experience_years' => 5,
            'hourly_rate'      => 100,
            'balance'          => 0,
            'total_earned'     => 0,
        ]);

        // ── Customer ───────────────────────────────────────────────────────────
        $customer = User::create([
            'name'              => 'Customer Demo',
            'username'          => 'customer',
            'email'             => 'customer@marketplace.com',
            'password'          => $password,
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);

        UserProfile::create([
            'user_id' => $customer->id,
            'balance' => 5000000,
            'country' => 'Indonesia',
        ]);

        // ── Categories ─────────────────────────────────────────────────────────
        $this->call(CategorySeeder::class);
    }
}
