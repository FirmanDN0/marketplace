<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('opened_by')->constrained('users')->onDelete('restrict');
            $table->string('reason', 200);
            $table->text('description');
            $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('opened_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
