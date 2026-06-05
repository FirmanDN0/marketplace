<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('custom_offer_id')->nullable()->constrained('custom_offers')->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable()->change();
            $table->foreignId('custom_offer_id')->nullable()->constrained('custom_offers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['custom_offer_id']);
            $table->dropColumn('custom_offer_id');
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['custom_offer_id']);
            $table->dropColumn('custom_offer_id');
        });
    }
};
