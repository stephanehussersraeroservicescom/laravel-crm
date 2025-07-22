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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_code', 50)->nullable();
            $table->string('contact_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('payment_terms')->nullable();
            $table->boolean('is_subcontractor')->default(false);
            $table->boolean('has_blanket_po')->default(false);
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->string('account_manager')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for searches and lookups
            $table->index('company_name');
            $table->index('contact_name');
            $table->index('company_code');
            $table->index('account_manager');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};