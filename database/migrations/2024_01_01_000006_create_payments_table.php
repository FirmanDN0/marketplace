<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('IDR');
            $table->enum('payment_method', ['midtrans', 'stripe', 'manual', 'balance'])->default('midtrans');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('payment_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('event', 100);
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('payment_id');
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->enum('type', ['payment', 'earning', 'refund', 'withdrawal', 'fee', 'adjustment']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->string('description');
            $table->string('reference_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payments');
    }
};
