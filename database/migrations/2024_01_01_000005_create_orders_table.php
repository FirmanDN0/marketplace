<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('provider_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->foreignId('package_id')->constrained('service_packages')->onDelete('restrict');
            $table->decimal('price', 12, 2);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('provider_earning', 12, 2)->default(0);
            $table->enum('status', [
                'pending_payment','paid','in_progress',
                'delivered','completed','cancelled','disputed'
            ])->default('pending_payment');
            $table->timestamp('delivery_deadline')->nullable();
            $table->text('notes')->nullable();
            $table->string('delivery_file')->nullable();
            $table->text('delivery_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('provider_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
