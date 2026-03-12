<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('provider_setup_step')->default(0)->after('status');
        });

        // Mark existing providers as already completed onboarding
        DB::table('users')->where('role', 'provider')->update(['provider_setup_step' => 3]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('provider_setup_step');
        });
    }
};
