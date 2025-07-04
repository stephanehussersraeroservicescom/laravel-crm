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
    Schema::create('subcontractor_subcontractor', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_id')->constrained('subcontractors')->onDelete('cascade');
        $table->foreignId('sub_id')->constrained('subcontractors')->onDelete('cascade');
        $table->timestamps();
        $table->unique(['main_id', 'sub_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_subcontractor');
    }
};
