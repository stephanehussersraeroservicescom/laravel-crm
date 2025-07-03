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
        // Vertical surface material pivot table
        Schema::create('vertical_surface_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vertical_surface_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Cover material pivot table
        Schema::create('cover_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Panel material pivot table
        Schema::create('panel_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Subcontractor to subcontractor relationship table
        Schema::create('subcontractor_subcontractor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->foreignId('child_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->string('relationship_type')->nullable(); // e.g., 'subsidiary', 'partner', etc.
            $table->timestamps();

            $table->unique(['parent_subcontractor_id', 'child_subcontractor_id'], 'subcontractor_relationship_unique');
        });

        // Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('subcontractor_subcontractor');
        Schema::dropIfExists('panel_material');
        Schema::dropIfExists('cover_material');
        Schema::dropIfExists('vertical_surface_material');
    }
};
