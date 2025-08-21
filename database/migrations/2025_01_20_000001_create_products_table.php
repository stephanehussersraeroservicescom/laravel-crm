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
            $table->id();
            $table->string('part_number')->unique();
            $table->string('root_code', 20);
            $table->string('color_name');
            $table->string('color_code', 10)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('moq')->default(1);
            $table->enum('uom', ['LY', 'UNIT'])->default('LY');
            $table->string('lead_time_weeks', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key to product_classes
            $table->foreign('root_code')->references('root_code')->on('product_classes')->onDelete('cascade');
            
            // Indexes
            $table->index('part_number');
            $table->index('root_code');
            $table->index('color_name');
            $table->index('is_active');
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