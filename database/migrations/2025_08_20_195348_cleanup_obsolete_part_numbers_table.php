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
        // Step 1: Drop the foreign key constraint from quote_lines
        Schema::table('quote_lines', function (Blueprint $table) {
            // Drop the foreign key if it exists
            $table->dropForeign(['part_number_id']);
        });
        
        // Step 2: Drop the part_number_id column from quote_lines
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn('part_number_id');
        });
        
        // Step 3: Drop the obsolete part_numbers table
        // This table was superseded by the products table which has active implementation
        Schema::dropIfExists('part_numbers');
        
        // Also drop the product_templates table if it exists (appears unused)
        Schema::dropIfExists('product_templates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate part_numbers table
        Schema::create('part_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_class_id')->constrained('product_classes');
            $table->string('part_number', 50)->unique();
            $table->string('color_code', 20);
            $table->string('color_name');
            $table->string('pattern')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->integer('stock_yards')->default(0);
            $table->integer('reserved_yards')->default(0);
            $table->boolean('is_standard')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->date('available_from')->nullable();
            $table->date('discontinued_date')->nullable();
            $table->timestamps();
            
            $table->index('product_class_id');
            $table->index('part_number');
            $table->index('color_code');
            $table->index('is_standard');
            $table->index('is_discontinued');
        });
        
        // Re-add part_number_id column to quote_lines
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->foreignId('part_number_id')->nullable()->after('quote_id')
                  ->constrained('part_numbers')->onDelete('set null');
            $table->index('part_number_id');
        });
    }
};