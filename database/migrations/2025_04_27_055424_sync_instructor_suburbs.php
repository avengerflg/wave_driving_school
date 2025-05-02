<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Instructor;

class SyncInstructorSuburbs extends Migration
{
    public function up()
    {
        // Sync all instructors' suburbs
        $instructors = Instructor::all();
        foreach ($instructors as $instructor) {
            $instructor->syncSuburbsWithPivot();
        }
    }

    public function down()
    {
        // No need for down migration as we're just syncing data
    }
}
