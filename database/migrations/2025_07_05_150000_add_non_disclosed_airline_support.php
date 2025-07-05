<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Airline;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Make airline_id nullable in projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['airline_id']);
            $table->foreignId('airline_id')->nullable()->change();
            $table->foreign('airline_id')->references('id')->on('airlines')->nullOnDelete();
        });

        // 2. Add fields to support non-disclosed projects
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'airline_disclosed')) {
                $table->boolean('airline_disclosed')->default(true)->after('airline_id');
            }
            if (!Schema::hasColumn('projects', 'airline_code_placeholder')) {
                $table->string('airline_code_placeholder')->nullable()->after('airline_disclosed');
            }
            if (!Schema::hasColumn('projects', 'confidentiality_notes')) {
                $table->text('confidentiality_notes')->nullable()->after('airline_code_placeholder');
            }
            if (!Schema::hasColumn('projects', 'airline_disclosed_at')) {
                $table->timestamp('airline_disclosed_at')->nullable()->after('confidentiality_notes');
            }
            if (!Schema::hasColumn('projects', 'disclosed_by')) {
                $table->foreignId('disclosed_by')->nullable()->constrained('users')->nullOnDelete()->after('airline_disclosed_at');
            }
        });

        // 3. Add index for better performance on disclosed/non-disclosed queries
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['airline_disclosed', 'created_at']);
            $table->index(['airline_id', 'airline_disclosed']);
        });

        // 4. Add code column to airlines if it doesn't exist
        if (!Schema::hasColumn('airlines', 'code')) {
            Schema::table('airlines', function (Blueprint $table) {
                $table->string('code', 10)->nullable()->unique()->after('name');
            });
        }

        // 5. Create the default "Non-Disclosed" airline entry
        Airline::firstOrCreate(
            ['name' => 'Non-Disclosed Airline'],
            [
                'name' => 'Non-Disclosed Airline',
                'code' => 'CONFIDENTIAL',
                'region' => 'North America',
            ]
        );

        // 6. Update existing airlines with default codes if needed
        $airlines = [
            'Delta Air Lines' => 'DAL',
            'American Airlines' => 'AAL', 
            'United Airlines' => 'UAL',
            'Lufthansa' => 'LH',
            'British Airways' => 'BA',
            'Emirates' => 'EK',
            'Singapore Airlines' => 'SQ',
        ];

        foreach ($airlines as $name => $code) {
            $airline = Airline::where('name', $name)->first();
            if ($airline && !$airline->code) {
                $airline->update(['code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the default "Non-Disclosed" airline
        Airline::where('code', 'CONFIDENTIAL')->delete();

        // Remove new columns
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['airline_disclosed', 'created_at']);
            $table->dropIndex(['airline_id', 'airline_disclosed']);
            
            $table->dropForeign(['disclosed_by']);
            $table->dropColumn([
                'airline_disclosed',
                'airline_code_placeholder', 
                'confidentiality_notes',
                'airline_disclosed_at',
                'disclosed_by'
            ]);
        });

        // Make airline_id required again
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['airline_id']);
            $table->foreignId('airline_id')->nullable(false)->change();
            $table->foreign('airline_id')->references('id')->on('airlines')->cascadeOnDelete();
        });
    }
};