<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Instructor;

class UpdateInstructorSuburbsToStrings extends Migration
{
    public function up()
    {
        // Convert existing suburbs to strings
        $instructors = Instructor::all();
        foreach ($instructors as $instructor) {
            if (is_array($instructor->suburbs)) {
                $instructor->suburbs = array_map('strval', $instructor->suburbs);
                $instructor->save();
            }
        }
    }

    public function down()
    {
        // Convert back to integers if needed
        $instructors = Instructor::all();
        foreach ($instructors as $instructor) {
            if (is_array($instructor->suburbs)) {
                $instructor->suburbs = array_map('intval', $instructor->suburbs);
                $instructor->save();
            }
        }
    }
}
