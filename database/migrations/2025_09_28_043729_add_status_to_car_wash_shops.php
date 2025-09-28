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
            $table->enum('status', ['open', 'closed'])->default('open')->after('services_offered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_wash_shops', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
