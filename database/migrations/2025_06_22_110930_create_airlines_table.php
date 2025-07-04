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
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                
            $table->enum('region', [
                        'North America', 'South America', 'Europe', 'Asia', 'Middle East', 'Africa', 'Oceania'
                        ]); 
            $table->string('account_executive')->nullable(); // You could also use 'user_id' for a relation to a users table
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airlines');
    }

};
