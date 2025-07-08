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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            
            // Direct relationship to project (one-to-many)
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // Required classifications as per requirements
            $table->enum('type', ['vertical', 'panels', 'covers', 'others'])->default('others');
            $table->enum('cabin_class', ['first_class', 'business_class', 'premium_economy', 'economy']);
            
            // Core opportunity fields
            $table->integer('probability')->unsigned()->nullable()->comment('Percentage 0-100');
            $table->decimal('potential_value', 15, 2)->nullable()->comment('Potential value in currency');
            $table->string('status')->default('draft');
            
            // Aircraft seat configuration fields
            $table->decimal('price_per_linear_yard', 10, 2)->nullable()->comment('Price per linear yard of material');
            $table->decimal('linear_yards_per_seat', 8, 2)->nullable()->comment('Linear yards required per seat');
            $table->integer('seats_in_opportunity')->nullable()->comment('Number of seats in this opportunity');
            $table->foreignId('aircraft_seat_config_id')->nullable()->constrained('aircraft_seat_configurations')->nullOnDelete();
            
            // Opportunity-specific forecasting fields (timeline data is now at project level)
            $table->json('revenue_distribution')->nullable()->comment('Year-by-year revenue distribution as JSON');
            $table->json('volume_by_year')->nullable()->comment('Year-by-year volume projections');
            $table->text('forecasting_notes')->nullable()->comment('Additional forecasting notes and assumptions');
            
            // Relationships - using nullOnDelete for soft delete compatibility
            $table->foreignId('certification_status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Content fields
            $table->string('phy_path')->nullable();
            $table->text('comments')->nullable();
            $table->string('name')->nullable(); // For 'others' type opportunities
            $table->text('description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['type', 'cabin_class']);
            $table->index(['status', 'probability']);
            $table->index(['assigned_to', 'status']);
            
            // Forecasting indexes (removed project-level forecasting indexes)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
