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
        // Update opportunities table with proper project_id FK and enums
        Schema::table('opportunities', function (Blueprint $table) {
            // Add project_id FK if it doesn't exist
            if (!Schema::hasColumn('opportunities', 'project_id')) {
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            }
            
            // Add proper enum constraints
            $table->enum('type', ['vertical', 'panels', 'covers', 'others'])->default('others')->change();
            $table->enum('cabin_class', ['first_class', 'business_class', 'premium_economy', 'economy'])->nullable()->change();
            $table->enum('status', ['active', 'inactive', 'pending', 'completed', 'cancelled'])->default('active')->change();
            
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
            if (!$this->hasIndex('projects', 'projects_airline_disclosed_index')) {
                $table->index(['airline_disclosed']);
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
    }

    private function hasIndex(string $table, string $index): bool
    {
        $indexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->toArray();
        
        return in_array($index, $indexes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove constraints
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_probability_range');
        DB::statement('ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS check_potential_value_positive');

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