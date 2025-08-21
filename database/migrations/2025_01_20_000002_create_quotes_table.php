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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            
            // Polymorphic customer relationship
            $table->string('customer_type')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            
            $table->string('quote_number')->nullable()->unique();
            $table->string('salesperson_code', 10)->nullable();
            $table->date('date_entry');
            $table->date('date_valid');
            $table->string('shipping_terms')->default('Ex Works Dallas Texas');
            $table->string('payment_terms')->default('Pro Forma');
            $table->string('lead_time_weeks')->nullable();
            $table->text('introduction_text')->nullable();
            $table->text('terms_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('comments')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('quote_number');
            $table->index('status');
            $table->index('date_entry');
            $table->index(['customer_type', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};