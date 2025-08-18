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
            $table->string('root_name', 100);
            $table->string('part_number_prefix')->nullable()
                ->comment('Prefix to use when building part numbers (e.g., ULFRB for ULFRB900)');
            $table->integer('moq_ly')->default(1);
            $table->string('lead_time_weeks', 20)->nullable();
            $table->boolean('has_ink_resist')->default(false);
            $table->boolean('is_bio')->default(false);
            $table->decimal('price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('root_code');
            $table->index(['root_code', 'has_ink_resist', 'is_bio']);
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