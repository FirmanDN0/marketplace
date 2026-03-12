<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['bank_transfer', 'paypal', 'gopay', 'dana'])->default('bank_transfer');
            $table->json('account_details');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('provider_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
