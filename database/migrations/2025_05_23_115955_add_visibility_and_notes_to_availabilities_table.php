<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->string('visibility_status')->default('public')->after('is_available'); // e.g., public, private
            $table->text('notes')->nullable()->after('visibility_status');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null')->after('instructor_id');
        });
    }

    public function down()
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn(['visibility_status', 'notes', 'service_id']);
        });
    }
};