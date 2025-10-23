<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('cash_given', 12, 2)->nullable()->after('total_amount');
            $table->decimal('change', 12, 2)->nullable()->after('cash_given');
        });
    }
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['cash_given', 'change']);
        });
    }
};
