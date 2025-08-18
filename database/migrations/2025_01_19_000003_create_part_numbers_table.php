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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_numbers');
    }
};
