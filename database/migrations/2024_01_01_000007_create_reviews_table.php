<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('restrict');
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('provider_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->text('provider_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('provider_id');
            $table->index('service_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
