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
        // Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('airline_id')->constrained('airlines');
            $table->foreignId('aircraft_type_id')->nullable()->constrained();
            $table->integer('number_of_aircraft')->nullable();
            $table->foreignId('design_status_id')->nullable()->constrained('statuses');
            $table->foreignId('commercial_status_id')->nullable()->constrained('statuses');
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Contacts table
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontractor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Vertical surfaces table
        Schema::create('vertical_surfaces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Covers table
        Schema::create('covers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Panels table
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panels');
        Schema::dropIfExists('covers');
        Schema::dropIfExists('vertical_surfaces');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('projects');
    }
};
