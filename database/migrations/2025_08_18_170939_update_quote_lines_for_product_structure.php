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
            // Add new columns for product structure
            $table->foreignId('part_number_id')->nullable()->after('quote_id')->constrained('part_numbers');
            $table->foreignId('product_class_id')->nullable()->after('part_number_id')->constrained('product_classes');
            
            // Add override columns for all key fields
            $table->decimal('override_price', 10, 2)->nullable()->after('final_price');
            $table->string('override_description')->nullable()->after('description');
            $table->string('override_lead_time')->nullable()->after('lead_time');
            $table->boolean('is_custom_item')->default(false)->after('override_lead_time');
            
            // Add indexes
            $table->index('part_number_id');
            $table->index('product_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropForeign(['part_number_id']);
            $table->dropForeign(['product_class_id']);
            $table->dropIndex(['part_number_id']);
            $table->dropIndex(['product_class_id']);
            $table->dropColumn([
                'part_number_id',
                'product_class_id',
                'override_price',
                'override_description',
                'override_lead_time',
                'is_custom_item'
            ]);
        });
    }
};
