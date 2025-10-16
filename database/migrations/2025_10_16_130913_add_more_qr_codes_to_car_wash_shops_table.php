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
        Schema::table('car_wash_shops', function (Blueprint $table) {
            $table->string('qr_code2')->nullable()->after('qr_code');
            $table->string('qr_code3')->nullable()->after('qr_code2');
            $table->string('qr_code4')->nullable()->after('qr_code3');
            $table->string('qr_code5')->nullable()->after('qr_code4');
        });
    }

    public function down(): void
    {
        Schema::table('car_wash_shops', function (Blueprint $table) {
            $table->dropColumn(['qr_code2', 'qr_code3', 'qr_code4', 'qr_code5']);
        });
    }
};
