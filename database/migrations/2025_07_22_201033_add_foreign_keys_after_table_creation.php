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
        // Add foreign key constraints that depend on tables created later in the migration order
        
        // Add airline foreign key to quotes table
        if (Schema::hasTable('airlines') && Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->foreign('airline_id')->references('id')->on('airlines')->onDelete('set null');
            });
        }
        
        // Add foreign keys to contract_prices table
        if (Schema::hasTable('airlines') && Schema::hasTable('product_roots') && Schema::hasTable('contract_prices')) {
            Schema::table('contract_prices', function (Blueprint $table) {
                $table->foreign('airline_id')->references('id')->on('airlines')->onDelete('cascade');
                $table->foreign('root_code')->references('root_code')->on('product_roots')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['airline_id']);
        });
        
        Schema::table('contract_prices', function (Blueprint $table) {
            $table->dropForeign(['airline_id']);
            $table->dropForeign(['root_code']);
        });
    }
};