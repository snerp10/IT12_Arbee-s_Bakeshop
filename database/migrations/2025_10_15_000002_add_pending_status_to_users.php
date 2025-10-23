<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
