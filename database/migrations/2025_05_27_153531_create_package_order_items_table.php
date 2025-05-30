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
        Schema::create('package_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('package_orders')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('gst', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_order_items');
    }
};