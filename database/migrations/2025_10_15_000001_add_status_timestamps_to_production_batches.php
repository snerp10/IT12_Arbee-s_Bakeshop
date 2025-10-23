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
        Schema::table('production_batches', function (Blueprint $table) {
            $table->timestamp('pending_at')->nullable()->after('status');
            $table->timestamp('in_progress_at')->nullable()->after('pending_at');
            $table->timestamp('completed_at')->nullable()->after('in_progress_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropColumn(['pending_at', 'in_progress_at', 'completed_at']);
        });
    }
};
