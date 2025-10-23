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
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id('inventory_id'); // Primary key: inventory_id
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('restrict');
            $table->integer('quantity');
            $table->integer('reorder_level')->default(0);
            $table->timestamp('last_counted_at')->nullable();
            $table->foreignId('batch_id')->nullable()->constrained('production_batches', 'batch_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
