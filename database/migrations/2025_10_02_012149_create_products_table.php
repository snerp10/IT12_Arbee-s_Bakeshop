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
        Schema::create('products', function (Blueprint $table) {
            $table->id('prod_id'); // Primary key: prod_id
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('product_categories', 'category_id')->onDelete('restrict');
            $table->decimal('price', 10, 2);
            $table->string('unit'); // pieces, kg, liter, etc.
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->boolean('is_available')->default(true);
            $table->integer('shelf_life')->nullable(); // in days
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
