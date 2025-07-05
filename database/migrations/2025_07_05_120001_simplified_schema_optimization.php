<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop redundant tables
        Schema::dropIfExists('project_opportunity');
        Schema::dropIfExists('opportunity_subcontractor');
        
        // 2. Create supporting subcontractors pivot table for teams
        if (!Schema::hasTable('project_team_supporters')) {
            Schema::create('project_team_supporters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('project_subcontractor_teams')->cascadeOnDelete();
                $table->foreignId('supporting_subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
                
                $table->unique(['team_id', 'supporting_subcontractor_id'], 'team_supporter_unique');
                $table->index(['team_id', 'deleted_at']);
            });
        }
        
        // 3. Add GDPR compliance columns to contacts
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('contacts', 'consent_given_at')) {
                    $table->timestamp('consent_given_at')->nullable();
                }
                if (!Schema::hasColumn('contacts', 'consent_withdrawn_at')) {
                    $table->timestamp('consent_withdrawn_at')->nullable();
                }
                if (!Schema::hasColumn('contacts', 'marketing_consent')) {
                    $table->boolean('marketing_consent')->default(false);
                }
                if (!Schema::hasColumn('contacts', 'data_processing_notes')) {
                    $table->text('data_processing_notes')->nullable();
                }
            });
        }
        
        // 4. Create encrypted data table for sensitive opportunity data
        if (!Schema::hasTable('opportunity_encrypted_data')) {
            Schema::create('opportunity_encrypted_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
                $table->text('encrypted_financial_data')->nullable();
                $table->text('encrypted_confidential_notes')->nullable();
                $table->timestamps();
                
                $table->unique('opportunity_id');
            });
        }
    }

    public function down(): void
    {
        // Drop new tables
        Schema::dropIfExists('opportunity_encrypted_data');
        Schema::dropIfExists('project_team_supporters');
        
        // Remove GDPR columns
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn(['consent_given_at', 'consent_withdrawn_at', 'marketing_consent', 'data_processing_notes']);
            });
        }
    }
};