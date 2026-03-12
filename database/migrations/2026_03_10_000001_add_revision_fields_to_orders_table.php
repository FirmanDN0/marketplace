<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('revision_count')->default(0)->after('delivery_file');
            $table->text('revision_message')->nullable()->after('revision_count');
            $table->timestamp('revision_requested_at')->nullable()->after('revision_message');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['revision_count', 'revision_message', 'revision_requested_at']);
        });
    }
};
