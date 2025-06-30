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
            $table->string('opportunity_type')->nullable()->after('role'); // vertical_surfaces, panels, covers
            $table->unsignedBigInteger('opportunity_id')->nullable()->after('opportunity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_subcontractor_teams', function (Blueprint $table) {
            $table->dropColumn(['opportunity_type', 'opportunity_id']);
        });
    }
};
