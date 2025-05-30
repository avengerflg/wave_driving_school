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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('confirmation_sent')->default(false);
            $table->boolean('two_day_reminder_sent')->default(false);
            $table->boolean('one_day_reminder_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('confirmation_sent');
            $table->dropColumn('two_day_reminder_sent');
            $table->dropColumn('one_day_reminder_sent');
        });
    }
};