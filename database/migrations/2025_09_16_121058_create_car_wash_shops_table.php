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
        Schema::create('car_wash_shops', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->string('address');
            $table->unsignedTinyInteger('district');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('services_offered')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();

            // If you want foreign key relation with an "owners" table
            // $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_wash_shops');
    }
};
