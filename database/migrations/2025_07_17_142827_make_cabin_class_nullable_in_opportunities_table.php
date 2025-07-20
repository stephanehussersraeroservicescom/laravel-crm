<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to alter enum column since Doctrine DBAL doesn't support enum
        DB::statement("ALTER TABLE opportunities MODIFY cabin_class ENUM('first_class', 'business_class', 'premium_economy', 'economy') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set any NULL values to a default before making it NOT NULL
        DB::table('opportunities')->whereNull('cabin_class')->update(['cabin_class' => 'economy']);
        
        // Use raw SQL to alter enum column
        DB::statement("ALTER TABLE opportunities MODIFY cabin_class ENUM('first_class', 'business_class', 'premium_economy', 'economy') NOT NULL");
    }
};