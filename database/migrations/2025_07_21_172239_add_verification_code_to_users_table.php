<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the verification_code column to users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Remove the verification_code column from users table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};

