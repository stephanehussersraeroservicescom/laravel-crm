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
        // Add GDPR compliance columns to contacts
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
        
        // Create encrypted data table for sensitive opportunity data if not exists
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop encrypted data table
        Schema::dropIfExists('opportunity_encrypted_data');
        
        // Remove GDPR columns from contacts
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn([
                    'consent_given_at', 
                    'consent_withdrawn_at', 
                    'marketing_consent', 
                    'data_processing_notes'
                ]);
            });
        }
    }
};
