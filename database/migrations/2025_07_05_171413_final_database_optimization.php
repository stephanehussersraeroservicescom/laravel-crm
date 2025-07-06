<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add performance indexes to opportunities table
        Schema::table('opportunities', function (Blueprint $table) {
            // Add proper indexes if they don't exist
            if (!$this->hasIndex('opportunities', 'opportunities_project_id_type_index')) {
                $table->index(['project_id', 'type']);
            }
            if (!$this->hasIndex('opportunities', 'opportunities_project_id_status_index')) {
                $table->index(['project_id', 'status']);
            }
            if (!$this->hasIndex('opportunities', 'opportunities_project_id_cabin_class_index')) {
                $table->index(['project_id', 'cabin_class']);
            }
            if (!$this->hasIndex('opportunities', 'opportunities_probability_index')) {
                $table->index(['probability']);
            }
            if (!$this->hasIndex('opportunities', 'opportunities_status_type_index')) {
                $table->index(['status', 'type']);
            }
        });

        // Drop redundant tables that were replaced by direct relationships
        Schema::dropIfExists('project_opportunity');
        Schema::dropIfExists('opportunity_subcontractor');

        // Ensure proper indexing on main tables for performance
        Schema::table('projects', function (Blueprint $table) {
            if (!$this->hasIndex('projects', 'projects_airline_id_aircraft_type_id_index')) {
                $table->index(['airline_id', 'aircraft_type_id']);
            }
            if (!$this->hasIndex('projects', 'projects_design_status_id_index')) {
                $table->index(['design_status_id']);
            }
            if (!$this->hasIndex('projects', 'projects_commercial_status_id_index')) {
                $table->index(['commercial_status_id']);
            }
            if (!$this->hasIndex('projects', 'projects_owner_id_index')) {
                $table->index(['owner_id']);
            }
        });

        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            // Add foreign key constraint to opportunities table (created after this table)
            try {
                $table->foreign('opportunity_id')->references('id')->on('opportunities')->nullOnDelete();
            } catch (\Exception $e) {
                // Foreign key may already exist, continue
            }
            
            if (!$this->hasIndex('project_subcontractor_teams', 'pst_opp_main_idx')) {
                $table->index(['opportunity_id', 'main_subcontractor_id'], 'pst_opp_main_idx');
            }
            if (!$this->hasIndex('project_subcontractor_teams', 'pst_main_sub_idx')) {
                $table->index(['main_subcontractor_id'], 'pst_main_sub_idx');
            }
        });

        Schema::table('contacts', function (Blueprint $table) {
            if (!$this->hasIndex('contacts', 'contacts_sub_role_idx')) {
                $table->index(['subcontractor_id', 'role'], 'contacts_sub_role_idx');
            }
            if (!$this->hasIndex('contacts', 'contacts_email_idx')) {
                $table->index(['email'], 'contacts_email_idx');
            }
        });

        Schema::table('airlines', function (Blueprint $table) {
            if (!$this->hasIndex('airlines', 'airlines_account_executive_id_index')) {
                $table->index(['account_executive_id']);
            }
            if (!$this->hasIndex('airlines', 'airlines_region_index')) {
                $table->index(['region']);
            }
        });

        // Add database-level constraints for business rules
        try {
            DB::statement('ALTER TABLE opportunities ADD CONSTRAINT check_probability_range CHECK (probability >= 0 AND probability <= 100)');
        } catch (\Exception $e) {
            // Constraint may already exist
        }
        
        try {
            DB::statement('ALTER TABLE opportunities ADD CONSTRAINT check_potential_value_positive CHECK (potential_value >= 0)');
        } catch (\Exception $e) {
            // Constraint may already exist
        }
        
        // Add soft delete performance indexes
        $softDeleteTables = ['projects', 'opportunities', 'project_subcontractor_teams', 'contacts', 'airlines', 'subcontractors'];
        foreach ($softDeleteTables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $tableSchema) use ($table) {
                    if (!$this->hasIndex($table, "idx_{$table}_deleted_at")) {
                        $tableSchema->index('deleted_at', "idx_{$table}_deleted_at");
                    }
                });
            }
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        $indexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->toArray();
        
        return in_array($index, $indexes);
    }
    
    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND CONSTRAINT_NAME != 'PRIMARY'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME')->toArray();
        
        return in_array($foreignKey, $foreignKeys);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove constraints
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_probability_range');
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_potential_value_positive');
        
        // Recreate redundant tables (though these may not be needed anymore)
        Schema::create('project_opportunity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('opportunity_subcontractor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcontractor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Remove performance indexes
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'type']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['project_id', 'cabin_class']);
            $table->dropIndex(['probability']);
            $table->dropIndex(['status', 'type']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['airline_id', 'aircraft_type_id']);
            $table->dropIndex(['design_status_id']);
            $table->dropIndex(['commercial_status_id']);
            $table->dropIndex(['owner_id']);
        });

        // Remove soft delete indexes
        $softDeleteTables = ['projects', 'opportunities', 'project_subcontractor_teams', 'contacts', 'airlines', 'subcontractors'];
        foreach ($softDeleteTables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $tableSchema) use ($table) {
                    $tableSchema->dropIndex("idx_{$table}_deleted_at");
                });
            }
        }
    }
};