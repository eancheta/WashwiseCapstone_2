<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_wash_shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->string('address');
            $table->unsignedTinyInteger('district');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('services_offered')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')
                  ->references('id')
                  ->on('car_wash_owners')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_wash_shops');
    }
};
