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
        $table->text('decline_reason')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('car_wash_owners', function (Blueprint $table) {
        $table->dropColumn('decline_reason');
    });
}
};
