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
        // Drop the stocked_products table - no longer needed as we're not managing inventory
        // Products that need shorter lead times or lower MOQ can have those set directly
        // in the products or product_classes tables
        Schema::dropIfExists('stocked_products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table if rolling back
        Schema::create('stocked_products', function (Blueprint $table) {
            $table->id();
            $table->string('full_part_number')->unique();
            $table->string('root_code', 10);
            $table->boolean('is_exotic')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('full_part_number');
            $table->index('root_code');
            $table->index('is_exotic');
        });
    }
};