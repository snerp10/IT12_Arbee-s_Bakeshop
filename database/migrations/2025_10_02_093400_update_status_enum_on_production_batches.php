<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'pending' to enum and set default to 'pending'
        DB::statement("ALTER TABLE production_batches MODIFY status ENUM('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert to previous enum without 'pending' and default to 'in_progress'
        DB::statement("ALTER TABLE production_batches MODIFY status ENUM('in_progress','completed','cancelled') NOT NULL DEFAULT 'in_progress'");
    }
};
