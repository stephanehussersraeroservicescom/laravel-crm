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
            $table->string('code', 10)->unique();
            $table->enum('region', [
                        'North America', 'South America', 'Europe', 'Asia', 'Middle East', 'Africa', 'Oceania'
                        ]); 
            $table->foreignId('account_executive_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['account_executive_id']);
            $table->index(['region']);
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('airlines');
    }

};
