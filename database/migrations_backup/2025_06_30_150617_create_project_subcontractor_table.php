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
        Schema::create('project_subcontractor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('main_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->foreignId('supporting_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->string('role')->nullable(); // e.g., 'primary', 'support', 'supplier'
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['project_id', 'main_subcontractor_id', 'supporting_subcontractor_id'], 'project_subcontractor_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subcontractor');
    }
};
