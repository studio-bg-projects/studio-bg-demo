<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncFilters extends Command
{
  protected $signature = 'job:sync:filters';

  protected $description = 'Sync filters with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:filters');
  }
}
