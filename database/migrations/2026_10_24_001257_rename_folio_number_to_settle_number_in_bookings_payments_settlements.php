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
        if (Schema::hasColumn('bookings', 'folio_number')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('folio_number', 'settle_number');
            });
        }

        if (Schema::hasColumn('payments', 'folio_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->renameColumn('folio_number', 'settle_number');
            });
        }

        if (Schema::hasColumn('settlements', 'folio_number')) {
            Schema::table('settlements', function (Blueprint $table) {
                $table->renameColumn('folio_number', 'settle_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'settle_number')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('settle_number', 'folio_number');
            });
        }

        if (Schema::hasColumn('payments', 'settle_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->renameColumn('settle_number', 'folio_number');
            });
        }

        if (Schema::hasColumn('settlements', 'settle_number')) {
            Schema::table('settlements', function (Blueprint $table) {
                $table->renameColumn('settle_number', 'folio_number');
            });
        }
    }
};
