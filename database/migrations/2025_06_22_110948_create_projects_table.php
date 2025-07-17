<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('airline_id')->constrained('airlines');
            $table->foreignId('aircraft_type_id')->nullable()->constrained();
            $table->integer('number_of_aircraft')->nullable();
            $table->foreignId('design_status_id')->nullable()->constrained('statuses');
            $table->foreignId('commercial_status_id')->nullable()->constrained('statuses');
            $table->foreignId('owner_id')->constrained('users'); // Project owner (required)
            $table->text('comment')->nullable();
            
            // Forecasting fields - moved from opportunities to project level
            $table->enum('linefit_retrofit', ['linefit', 'retrofit'])->nullable()->comment('Project type: linefit or retrofit');
            $table->integer('project_lifecycle_duration')->default(3)->comment('Project duration in years (1-10)');
            $table->json('distribution_pattern')->nullable()->comment('Year-by-year completion distribution pattern');
            $table->integer('expected_start_year')->nullable()->comment('Expected year when project revenue starts');
            $table->integer('expected_close_year')->nullable()->comment('Expected year when project revenue ends');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('linefit_retrofit');
            $table->index('expected_start_year');
            $table->index('expected_close_year');
            $table->index(['expected_start_year', 'expected_close_year']);
            $table->index(['airline_id', 'aircraft_type_id']);
            $table->index(['design_status_id']);
            $table->index(['commercial_status_id']);
            $table->index(['owner_id']);
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }

};
