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
        Schema::table('subcontractors', function (Blueprint $table) {
            if (!Schema::hasColumn('subcontractors', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('subcontractors', 'email')) {
                $table->string('email')->nullable()->after('contact_name');
            }
            if (!Schema::hasColumn('subcontractors', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('subcontractors', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('subcontractors', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('address');
            }
        });
        
        Schema::table('quote_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('quote_lines', 'moq_waived')) {
                $table->boolean('moq_waived')->default(false)->after('moq');
            }
            if (!Schema::hasColumn('quote_lines', 'moq_waiver_reason')) {
                $table->string('moq_waiver_reason')->nullable()->after('moq_waived');
            }
            if (!Schema::hasColumn('quote_lines', 'contract_price')) {
                $table->integer('contract_price')->nullable()->after('final_price');
            }
            if (!Schema::hasColumn('quote_lines', 'pricing_reference')) {
                $table->string('pricing_reference')->nullable()->after('pricing_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcontractors', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'email', 'phone', 'address', 'payment_terms']);
        });
        
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn(['moq_waived', 'moq_waiver_reason', 'contract_price', 'pricing_reference']);
        });
    }
};
