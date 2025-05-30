<?php

use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    // run your custom notifications command every hour
    $schedule->command('notifications:send')->hourly();
};