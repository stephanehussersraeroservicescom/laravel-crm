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
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->boolean('moq_waived')->default(false)->after('moq');
            $table->string('moq_waiver_reason')->nullable()->after('moq_waived');
            $table->integer('contract_price')->nullable()->after('final_price');
            $table->string('pricing_reference')->nullable()->after('pricing_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn(['moq_waived', 'moq_waiver_reason', 'contract_price', 'pricing_reference']);
        });
    }
};