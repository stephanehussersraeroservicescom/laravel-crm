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
        Schema::table('projects', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['airline_id']);
            
            // Re-add the foreign key with cascade delete
            $table->foreign('airline_id')
                  ->references('id')
                  ->on('airlines')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the cascade delete constraint
            $table->dropForeign(['airline_id']);
            
            // Re-add the original foreign key without cascade
            $table->foreign('airline_id')
                  ->references('id')
                  ->on('airlines');
        });
    }
};
