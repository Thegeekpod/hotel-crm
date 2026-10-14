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
        Schema::table('guests', function (Blueprint $table) {
            $table->string('gst_type')->default('exclusive')->after('discount_amount');
            $table->foreignId('gst_percentage_id')->nullable()->after('gst_type')->constrained('gst_percentages')->nullOnDelete();
            $table->decimal('gst_percentage', 5, 2)->default(0.00)->after('gst_percentage_id');
            $table->decimal('gst_amount', 12, 2)->default(0.00)->after('gst_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['gst_percentage_id']);
            $table->dropColumn([
                'gst_type',
                'gst_percentage_id',
                'gst_percentage',
                'gst_amount',
            ]);
        });
    }
};
