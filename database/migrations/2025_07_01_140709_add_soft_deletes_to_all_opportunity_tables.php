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
        Schema::table('vertical_surfaces', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('panels', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('covers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vertical_surfaces', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('panels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('covers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
