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
        Schema::create('aircraft_seat_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained()->cascadeOnDelete();
            $table->foreignId('aircraft_type_id')->constrained()->cascadeOnDelete();
            $table->string('version', 50)->default('Standard')->comment('Aircraft version (e.g., Standard, High-Density, Long-Range)');
            
            // Individual cabin class seat counts
            $table->integer('first_class_seats')->default(0);
            $table->integer('business_class_seats')->default(0);
            $table->integer('premium_economy_seats')->default(0);
            $table->integer('economy_seats')->default(0);
            
            // Total seats (calculated from individual classes)
            $table->integer('total_seats')->default(0);
            
            $table->json('seat_map_data')->nullable()->comment('Detailed seat configuration data');
            $table->string('data_source')->default('manual')->comment('Source of the data (manual, seatguru, airline_website, etc.)');
            $table->decimal('confidence_score', 3, 2)->default(1.0)->comment('Data confidence score 0-1');
            $table->timestamp('last_verified_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Unique constraint for airline + aircraft_type + version
            $table->unique(['airline_id', 'aircraft_type_id', 'version'], 'unique_aircraft_config');
            
            // Indexes
            $table->index(['airline_id', 'aircraft_type_id'], 'idx_aircraft_seat_configs_lookup');
            $table->index('confidence_score');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_seat_configurations');
    }
};
