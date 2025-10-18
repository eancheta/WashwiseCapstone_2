<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum column
        DB::statement("ALTER TABLE car_wash_shops MODIFY COLUMN status ENUM('Open', 'Closed') NOT NULL DEFAULT 'Closed'");
    }

    public function down(): void
    {
        // Revert back to old values if you roll back
        DB::statement("ALTER TABLE car_wash_shops MODIFY COLUMN status ENUM('open', 'closed') NOT NULL DEFAULT 'closed'");
    }
};
