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
        // Airlines table
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                
            $table->enum('region', [
                'North America', 'South America', 'Europe', 'Asia', 'Middle East', 'Africa', 'Oceania'
            ]); 
            $table->string('account_executive')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Aircraft types table
        Schema::create('aircraft_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Statuses table
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Materials table
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Subcontractors table
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractors');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('aircraft_types');
        Schema::dropIfExists('airlines');
    }
};
