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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->enum('list_type', ['NF', 'FR', 'Commercial', 'standard']);
            $table->string('root_code', 10);
            $table->decimal('price_ly', 10, 2); // Price per Linear Yard
            $table->integer('moq_ly')->default(1); // Minimum Order Quantity in LY
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('imported_from')->nullable(); // Track source file
            $table->timestamps();
            
            // Foreign key
            $table->foreign('root_code')->references('root_code')->on('product_roots')->onDelete('cascade');
            
            // Indexes
            $table->index(['list_type', 'root_code', 'is_active']);
            $table->index(['effective_date', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};