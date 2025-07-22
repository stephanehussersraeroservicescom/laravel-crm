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
        Schema::create('product_roots', function (Blueprint $table) {
            $table->string('root_code', 10)->primary();
            $table->string('root_name', 100);
            $table->string('part_number_prefix')->nullable()
                ->comment('Prefix to use when building part numbers (e.g., ULFRB for ULFRB900)');
            $table->integer('moq_ly')->default(1);
            $table->string('lead_time_weeks', 20)->nullable();
            $table->boolean('has_ink_resist')->default(false);
            $table->boolean('is_bio')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_roots');
    }
};