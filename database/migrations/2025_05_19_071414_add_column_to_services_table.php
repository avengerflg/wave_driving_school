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
        Schema::table('services', function (Blueprint $table) {
            // Add image_path column after description
            $table->string('image_path')->nullable()->after('description');
            
            // Add category column after image_path
            $table->string('category', 50)->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop the columns if the migration is rolled back
            $table->dropColumn(['category', 'image_path']);
        });
    }
};