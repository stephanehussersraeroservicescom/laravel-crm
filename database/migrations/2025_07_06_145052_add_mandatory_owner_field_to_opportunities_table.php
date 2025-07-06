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
        Schema::table('opportunities', function (Blueprint $table) {
            // Add mandatory owner field to opportunities table
            // Owner represents who owns/manages the opportunity (different from assigned_to)
            $table->string('owner')->after('assigned_to')->comment('Opportunity owner - responsible for the opportunity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Remove the owner field
            $table->dropColumn('owner');
        });
    }
};