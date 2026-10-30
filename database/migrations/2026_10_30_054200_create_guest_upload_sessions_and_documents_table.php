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
        // 1. Guest Check-in Mobile Upload Sessions (5-minute expiry, multiple documents)
        if (!Schema::hasTable('guest_upload_sessions')) {
            Schema::create('guest_upload_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('token', 64)->unique()->index();
                $table->string('reserve_id', 50)->nullable();
                $table->string('room_number', 50)->nullable();
                $table->string('guest_name', 150)->nullable();
                $table->string('mobile', 50)->nullable();
                $table->string('id_card_type', 100)->nullable();
                $table->string('id_card_number', 100)->nullable();
                $table->json('documents')->nullable(); // Multiple image files & doc metadata
                $table->string('status', 20)->default('pending')->index(); // pending, completed, expired
                $table->timestamp('expires_at')->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add multiple documents JSON column to guests table
        if (Schema::hasTable('guests') && !Schema::hasColumn('guests', 'documents')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->json('documents')->nullable()->after('id_card_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_upload_sessions');

        if (Schema::hasTable('guests') && Schema::hasColumn('guests', 'documents')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropColumn('documents');
            });
        }
    }
};
