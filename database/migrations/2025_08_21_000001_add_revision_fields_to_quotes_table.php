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
            $table->unsignedBigInteger('parent_quote_id')->nullable()->after('quote_number');
            $table->integer('revision_number')->default(0)->after('parent_quote_id');
            $table->text('revision_reason')->nullable()->after('revision_number');
            $table->string('primary_pricing_source')->nullable()->after('status');
            
            // Add foreign key for parent quote
            $table->foreign('parent_quote_id')->references('id')->on('quotes')->onDelete('set null');
            
            // Add index for finding revisions
            $table->index('parent_quote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['parent_quote_id']);
            $table->dropColumn(['parent_quote_id', 'revision_number', 'revision_reason', 'primary_pricing_source']);
        });
    }
};