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
            $table->string('contact_name')->nullable()->after('name');
            $table->string('email')->nullable()->after('contact_name');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('payment_terms')->nullable()->after('address');
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
    }
};