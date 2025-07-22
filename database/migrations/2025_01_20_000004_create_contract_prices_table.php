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
        Schema::create('contract_prices', function (Blueprint $table) {
            $table->id();
            $table->string('customer_identifier')->nullable(); // company or contact name
            $table->string('part_number')->nullable(); // null means applies to all parts for this root/customer
            $table->string('root_code', 10)->nullable(); // null means applies to all products for this customer
            $table->unsignedBigInteger('airline_id')->nullable();
            $table->integer('contract_price'); // in cents
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for fast lookups
            $table->index('customer_identifier');
            $table->index('part_number');
            $table->index('root_code');
            $table->index('airline_id');
            $table->index(['valid_from', 'valid_to']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_prices');
    }
};