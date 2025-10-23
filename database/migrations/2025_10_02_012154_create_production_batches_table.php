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
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id('batch_id'); // Primary key: batch_id
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('restrict');
            $table->string('batch_number')->unique();
            $table->integer('quantity_produced');
            $table->date('production_date');
            $table->timestamp('produced_at')->nullable();
            $table->date('expiration_date')->nullable();
            $table->foreignId('baker_id')->constrained('employees', 'emp_id')->onDelete('restrict'); // Baker who produced
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};
