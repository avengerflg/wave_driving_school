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
        Schema::table('availabilities', function (Blueprint $table) {
            // Add visibility field
            $table->enum('visibility', ['public', 'private', 'hidden', 'note'])
                  ->default('public')
                  ->after('is_available');
            
            // Add note fields
            $table->text('private_note')->nullable()->after('visibility');
            $table->text('public_note')->nullable()->after('private_note');
            
            // Add suburbs field as JSON
            $table->json('suburbs')->nullable()->after('public_note');
            
            // Add duration in minutes for easier calculations
            $table->integer('duration_minutes')->nullable()->after('suburbs');
            
            // Add index for better performance
            $table->index(['instructor_id', 'date', 'visibility']);
            $table->index(['date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropIndex(['instructor_id', 'date', 'visibility']);
            $table->dropIndex(['date', 'start_time']);
            $table->dropColumn([
                'visibility',
                'private_note',
                'public_note',
                'suburbs',
                'duration_minutes'
            ]);
        });
    }
};
