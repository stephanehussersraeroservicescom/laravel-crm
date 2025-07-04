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
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }

};
