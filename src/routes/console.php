<?php

use App\Console\Commands\SyncGarminActivities;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncGarminActivities::class)->hourly();
