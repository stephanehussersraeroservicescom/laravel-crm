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
        Schema::create('stocked_products', function (Blueprint $table) {
            $table->id();
            $table->string('full_part_number', 50)->unique();
            $table->string('root_code', 10)->nullable();
            $table->string('series_code', 10)->nullable();
            $table->string('color_code', 20)->nullable();
            $table->string('treatment_suffix', 10)->nullable();
            $table->boolean('is_exotic')->default(false); // For non-standard part numbers
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index only, no foreign key since root_code is not unique in product_classes
            
            // Indexes
            $table->index('full_part_number');
            $table->index(['root_code', 'series_code', 'color_code']);
            $table->index('is_exotic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocked_products');
    }
};