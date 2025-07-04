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
        // Create project_subcontractor_teams table for main subcontractors
        Schema::create('project_subcontractor_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('main_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->enum('role', ['Commercial', 'Project Management', 'Design', 'Certification', 'Manufacturing', 'Subcontractor']);
            $table->string('opportunity_type')->nullable(); // vertical_surfaces, panels, covers
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['project_id', 'main_subcontractor_id', 'role'], 'project_main_sub_role_unique');
        });
        
        // Create pivot table for supporting subcontractors
        Schema::create('project_team_supporters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('project_subcontractor_teams')->cascadeOnDelete();
            $table->foreignId('supporting_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['team_id', 'supporting_subcontractor_id'], 'team_supporter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_team_supporters');
        Schema::dropIfExists('project_subcontractor_teams');
    }
};
