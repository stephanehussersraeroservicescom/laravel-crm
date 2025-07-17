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
            $table->foreignId('main_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->enum('role', ['Commercial', 'Project Management', 'Design', 'Certification', 'Manufacturing', 'Subcontractor']);
            
            // Opportunity ID will be constrained later after opportunities table is created
            $table->unsignedBigInteger('opportunity_id')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Final unique constraint based on opportunity (not project)
            $table->unique(['opportunity_id', 'main_subcontractor_id', 'role'], 'opportunity_main_sub_role_unique');
            
            // Performance indexes
            $table->index(['opportunity_id']);
            $table->index(['opportunity_id', 'main_subcontractor_id'], 'pst_opp_main_idx');
            $table->index(['main_subcontractor_id'], 'pst_main_sub_idx');
            $table->index('deleted_at');
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
