<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_suburb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('suburb_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate combinations
            $table->unique(['instructor_id', 'suburb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_suburb');
    }
};