<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class CustomerBalanceSeeder extends Seeder
{
    /**
     * Isi saldo semua customer yang ada.
     * Jalankan dengan: php artisan db:seed --class=CustomerBalanceSeeder
     */
    public function run(): void
    {
        $amount = 500_000.00; // saldo yang di-top-up

        $customers = User::where('role', 'customer')->get();

        foreach ($customers as $customer) {
            $profile = UserProfile::firstOrCreate(
                ['user_id' => $customer->id],
                ['balance' => 0, 'total_spent' => 0]
            );

            $balanceBefore = $profile->balance;
            $profile->increment('balance', $amount);

            // Catat ke tabel transactions sebagai jejak
            Transaction::create([
                'user_id'        => $customer->id,
                'type'           => 'topup',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => 'Saldo awal (seed)',
                'reference_id'   => 'SEED-' . strtoupper(uniqid()),
            ]);

            $this->command->info("✔ {$customer->name} — saldo ditambah Rp " . number_format($amount, 0, ',', '.'));
        }
    }
}
