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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontractor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->enum('role', ['engineering', 'program_management', 'design', 'certification'])->nullable();
            $table->string('phone')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('consent_given_at')->nullable();
            $table->timestamp('consent_withdrawn_at')->nullable();
            $table->text('data_processing_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Business logic constraints
            $table->unique(['subcontractor_id', 'email'], 'unique_contact_email_per_subcontractor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
