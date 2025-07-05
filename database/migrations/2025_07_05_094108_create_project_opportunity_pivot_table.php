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
        Schema::create('project_opportunity', function (Blueprint $table) {
            $table->id();
            // Use NO ACTION - let application handle soft deletes through model boot methods
            $table->foreignId('project_id')->constrained()->noActionOnDelete();
            $table->foreignId('opportunity_id')->constrained()->noActionOnDelete();
            $table->timestamps();
            $table->softDeletes(); // Pivot table supports soft deletes
            
            $table->unique(['project_id', 'opportunity_id']);
            $table->index(['project_id', 'deleted_at']);
            $table->index(['opportunity_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_opportunity');
    }
};
