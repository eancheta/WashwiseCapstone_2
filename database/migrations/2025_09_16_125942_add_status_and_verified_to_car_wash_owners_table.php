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
        Schema::table('car_wash_owners', function (Blueprint $table) {
            $table->boolean('is_verified')->default(0)->after('verification_code');
            $table->string('status')->default('pending')->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_wash_owners', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'status']);
        });
    }
};
