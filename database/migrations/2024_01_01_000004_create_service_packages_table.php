<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->enum('package_type', ['basic', 'standard', 'premium']);
            $table->string('name', 100);
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('delivery_days');
            $table->smallInteger('revisions')->default(1)->comment('-1 means unlimited');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'package_type']);
            $table->index('service_id');
        });

        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_cover')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
        Schema::dropIfExists('service_packages');
    }
};
