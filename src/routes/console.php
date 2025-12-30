<?php

use App\Console\Commands\SyncGarminActivities;
use App\Console\Commands\SyncWeather;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncGarminActivities::class)->hourly();
Schedule::command(SyncWeather::class)->hourly();
