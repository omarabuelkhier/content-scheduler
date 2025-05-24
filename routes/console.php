<?php

use App\Jobs\PublishScheduledPosts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the PublishScheduledPosts job
app(Schedule::class)->job(new PublishScheduledPosts)->everyMinute();
// // Example of a scheduled command : php artisan schedule:work
