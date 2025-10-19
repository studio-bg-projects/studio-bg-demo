<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncSpecifications extends Command
{
  protected $signature = 'job:sync:specifications';

  protected $description = 'Sync all specifications with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:specifications');
  }
}
