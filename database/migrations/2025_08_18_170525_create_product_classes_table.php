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
        Schema::create('product_classes', function (Blueprint $table) {
            $table->id();
            $table->string('root_code', 20);
            $table->string('product_name');
            $table->text('description');
            $table->integer('moq')->default(5);
            $table->boolean('has_ink_resist')->default(false);
            $table->boolean('is_bio')->default(false);
            $table->decimal('standard_price', 10, 2);
            $table->string('roll_width')->default('54"');
            $table->string('roll_length')->default('33 LY');
            $table->integer('lead_time_weeks')->default(12);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('root_code');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_classes');
    }
};