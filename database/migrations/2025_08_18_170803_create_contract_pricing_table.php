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
        Schema::create('contract_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('part_number_id')->nullable()->constrained('part_numbers');
            $table->foreignId('product_class_id')->nullable()->constrained('product_classes');
            $table->string('contract_number')->nullable();
            $table->decimal('contracted_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('minimum_quantity')->default(0);
            $table->integer('maximum_quantity')->nullable();
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['customer_id', 'is_active']);
            $table->index(['part_number_id', 'is_active']);
            $table->index(['product_class_id', 'is_active']);
            $table->index(['effective_date', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_pricing');
    }
};
