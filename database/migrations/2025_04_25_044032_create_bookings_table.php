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
        // Schema::create('bookings', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('service_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('suburb_id')->constrained()->onDelete('cascade');
        //     $table->date('date');
        //     $table->time('start_time');
        //     $table->time('end_time');
        //     $table->string('status'); // pending, confirmed, cancelled, completed
        //     $table->text('notes')->nullable();
        //     $table->string('booking_for')->default('self'); // self or other
        //     $table->string('other_name')->nullable();
        //     $table->string('other_email')->nullable();
        //     $table->string('other_phone')->nullable();
        //     $table->string('address')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
