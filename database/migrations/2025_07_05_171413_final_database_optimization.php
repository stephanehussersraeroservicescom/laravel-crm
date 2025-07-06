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

        // Remove project_id from project_subcontractor_teams if it exists (optimization)
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            if (Schema::hasColumn('project_subcontractor_teams', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
        });

        // Drop redundant tables
        Schema::dropIfExists('project_opportunity');
        Schema::dropIfExists('opportunity_subcontractor');

        // Ensure proper indexing on main tables
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
        });

        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
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

        // Note: subcontractors table doesn't have specialization/certification_level columns
        // Schema::table('subcontractors', function (Blueprint $table) {
        //     $table->index(['specialization'], 'subcontractors_spec_idx');
        //     $table->index(['certification_level'], 'subcontractors_cert_idx');
        // });

        // Add database-level constraints for business rules
        DB::statement('ALTER TABLE opportunities ADD CONSTRAINT check_probability_range CHECK (probability >= 0 AND probability <= 100)');
        DB::statement('ALTER TABLE opportunities ADD CONSTRAINT check_potential_value_positive CHECK (potential_value >= 0)');
        
        // Fix airlines table - convert account_executive string to foreign key
        if (Schema::hasColumn('airlines', 'account_executive')) {
            Schema::table('airlines', function (Blueprint $table) {
                $table->foreignId('account_executive_id')->nullable()->constrained('users')->nullOnDelete()->after('account_executive');
            });
            
            // Update existing data if possible (convert string names to user IDs)
            // This would require custom logic based on your data
            
            Schema::table('airlines', function (Blueprint $table) {
                $table->dropColumn('account_executive');
            });
        }
        
        // Add audit columns to projects table
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('comment');
            }
            if (!Schema::hasColumn('projects', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
            }
            if (!Schema::hasColumn('projects', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_by');
            }
        });
        
        // Add audit columns to opportunities table
        Schema::table('opportunities', function (Blueprint $table) {
            if (!Schema::hasColumn('opportunities', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('assigned_to');
            }
            if (!Schema::hasColumn('opportunities', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_by');
            }
        });
        
        // Add unique constraints for business logic
        Schema::table('contacts', function (Blueprint $table) {
            if (!$this->constraintExists('contacts', 'unique_contact_email_per_subcontractor')) {
                $table->unique(['subcontractor_id', 'email'], 'unique_contact_email_per_subcontractor');
            }
        });
        
        // Add soft delete performance indexes
        $softDeleteTables = ['projects', 'opportunities', 'project_subcontractor_teams', 'contacts'];
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
    
    /**
     * Check if a constraint exists on a table
     */
    private function constraintExists(string $table, string $constraint): bool
    {
        try {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
            return array_key_exists($constraint, $indexes);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove constraints
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_probability_range');
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_potential_value_positive');
        
        // Remove audit columns from projects table
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('projects', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
            if (Schema::hasColumn('projects', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
                $table->dropColumn('deleted_by');
            }
        });
        
        // Remove audit columns from opportunities table
        Schema::table('opportunities', function (Blueprint $table) {
            if (Schema::hasColumn('opportunities', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
            if (Schema::hasColumn('opportunities', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
                $table->dropColumn('deleted_by');
            }
        });
        
        // Restore account_executive as string
        Schema::table('airlines', function (Blueprint $table) {
            $table->string('account_executive')->nullable()->after('region');
            if (Schema::hasColumn('airlines', 'account_executive_id')) {
                $table->dropForeign(['account_executive_id']);
                $table->dropColumn('account_executive_id');
            }
        });

        // Recreate redundant tables
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

        // Re-add project_id to project_subcontractor_teams
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Remove opportunity enums and indexes
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'type']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['project_id', 'cabin_class']);
            $table->dropIndex(['probability']);
            $table->dropIndex(['status', 'type']);
            
            $table->string('type')->change();
            $table->string('cabin_class')->nullable()->change();
            $table->string('status')->change();
        });
    }
};