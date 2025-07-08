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
            $table->string('cabin_class'); // first_class, business_class, premium_economy, economy
            $table->integer('total_seats')->nullable();
            $table->json('seat_map_data')->nullable()->comment('Detailed seat configuration data');
            $table->string('data_source')->nullable()->comment('Source of the data (manual, seatguru, airline_website, etc.)');
            $table->decimal('confidence_score', 3, 2)->default(0)->comment('Data confidence score 0-1');
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['airline_id', 'aircraft_type_id', 'cabin_class'], 'idx_aircraft_seat_configs_lookup');
            $table->index('confidence_score');
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
