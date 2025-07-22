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
        Schema::create('quote_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->string('part_number');
            $table->string('root_code', 10)->nullable();
            $table->string('series_code', 10)->nullable();
            $table->string('color_code', 20)->nullable();
            $table->string('treatment_suffix', 10)->nullable();
            $table->boolean('is_exotic')->default(false);
            $table->string('base_part_number')->nullable();
            $table->text('description');
            $table->integer('quantity');
            $table->enum('unit', ['LY', 'UNIT']); // Only LY and UNIT as per your requirement
            $table->integer('standard_price'); // in cents
            $table->integer('final_price'); // in cents
            $table->enum('pricing_source', ['standard', 'contract', 'manual'])->default('standard');
            $table->integer('moq')->default(1); // Minimum Order Quantity
            $table->string('lead_time')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['quote_id', 'sort_order']);
            $table->index('root_code');
            $table->index('part_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_lines');
    }
};