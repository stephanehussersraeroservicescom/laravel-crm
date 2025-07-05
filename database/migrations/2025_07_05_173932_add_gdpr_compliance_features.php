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
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        
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
