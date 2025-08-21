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
            $table->unsignedBigInteger('parent_quote_id')->nullable();
            $table->integer('revision_number')->default(0);
            $table->text('revision_reason')->nullable();
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
            $table->string('primary_pricing_source')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('parent_quote_id')->references('id')->on('quotes')->onDelete('set null');
            
            // Indexes
            $table->index('quote_number');
            $table->index('status');
            $table->index('date_entry');
            $table->index(['customer_type', 'customer_id']);
            $table->index('parent_quote_id');
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