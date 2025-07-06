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
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            // Drop the old unique constraint based on project
            $table->dropUnique('project_main_sub_role_unique');
            
            // Add new unique constraint based on opportunity
            $table->unique(['opportunity_id', 'main_subcontractor_id', 'role'], 'opportunity_main_sub_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('opportunity_main_sub_role_unique');
            
            // Restore the old constraint (though this may fail if project_id is not populated)
            $table->unique(['project_id', 'main_subcontractor_id', 'role'], 'project_main_sub_role_unique');
        });
    }
};