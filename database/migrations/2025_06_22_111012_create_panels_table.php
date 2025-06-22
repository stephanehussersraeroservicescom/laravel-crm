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
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('cabin_class', ['first', 'business', 'premium_economy', 'economy']);
            $table->float('probability')->nullable();
            $table->string('opportunity_status')->nullable();
            $table->foreignId('certification_status_id')->nullable()->constrained('statuses');
            $table->text('potential')->nullable();
            $table->string('phy_path')->nullable(); // For uploaded PHY file path
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('panels');
    }

};
