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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('restrict');
            $table->enum('transaction_type', ['stock_in', 'stock_out', 'adjustment']);
            $table->integer('quantity'); // the amount moved (positive integer)
            $table->integer('previous_stock');
            $table->integer('current_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
