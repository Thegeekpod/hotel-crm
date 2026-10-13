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
            $columnsToDrop = [];
            if (Schema::hasColumn('guests', 'dob')) {
                $columnsToDrop[] = 'dob';
            }
            if (Schema::hasColumn('guests', 'anniversary')) {
                $columnsToDrop[] = 'anniversary';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'dob')) {
                $table->date('dob')->nullable()->after('email');
            }
            if (!Schema::hasColumn('guests', 'anniversary')) {
                $table->date('anniversary')->nullable()->after('dob');
            }
        });
    }
};
