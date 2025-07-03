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
        Schema::create('aircraft_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "A350-900", "B787-10"
            $table->string('manufacturer')->nullable(); // Optional, e.g. "Airbus", "Boeing"
            $table->string('code')->nullable(); // Optional: short code if needed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aircraft_types');
    }
};
