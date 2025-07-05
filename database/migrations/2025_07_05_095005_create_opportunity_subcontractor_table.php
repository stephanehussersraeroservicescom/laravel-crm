<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_subcontractor', function (Blueprint $table) {
            $table->id();
            // Use NO ACTION - let application handle soft deletes through model boot methods
            $table->foreignId('opportunity_id')->constrained()->noActionOnDelete();
            $table->foreignId('subcontractor_id')->constrained()->noActionOnDelete();
            $table->enum('role', ['lead', 'supporting', 'consultant'])->default('supporting');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Pivot table supports soft deletes
            
            $table->unique(['opportunity_id', 'subcontractor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_subcontractor');
    }
};