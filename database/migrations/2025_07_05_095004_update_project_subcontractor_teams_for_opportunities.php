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
            // Drop the old polymorphic columns
            $table->dropColumn(['opportunity_type', 'opportunity_id']);
        });
        
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            // Add direct foreign key to opportunities table
            $table->foreignId('opportunity_id')->nullable()->constrained('opportunities')->nullOnDelete();
            
            // Add index for better performance
            $table->index(['project_id', 'opportunity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['opportunity_id']);
            $table->dropColumn('opportunity_id');
            
            // Restore the polymorphic columns
            $table->string('opportunity_type')->nullable();
            $table->unsignedBigInteger('opportunity_id')->nullable();
            
            $table->index(['opportunity_type', 'opportunity_id']);
        });
    }
};
