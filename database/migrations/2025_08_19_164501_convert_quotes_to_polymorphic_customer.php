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
        Schema::table('quotes', function (Blueprint $table) {
            // First drop the old foreign keys
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['airline_id']);
        });
        
        Schema::table('quotes', function (Blueprint $table) {
            // Add polymorphic columns for customer relationship
            $table->string('customer_type')->nullable()->after('user_id');
            $table->string('customer_name')->after('customer_id'); // Store the name for display/history
            
            // Rename the existing customer_id to be used for polymorphic relationship
            // It already exists so we just need to make it nullable
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            
            // Add index for polymorphic relationship
            $table->index(['customer_type', 'customer_id']);
            
            // Drop old columns
            $table->dropColumn(['airline_id', 'is_subcontractor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // Re-add old columns
            $table->foreignId('customer_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('airline_id')->nullable()->after('customer_id')->constrained()->onDelete('set null');
            $table->boolean('is_subcontractor')->default(false)->after('status');
            
            // Drop polymorphic columns
            $table->dropIndex(['customer_type', 'customer_id']);
            $table->dropColumn(['customer_type', 'customer_id', 'customer_name']);
        });
    }
};