<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncManufacturers extends Command
{
  protected $signature = 'job:sync:manufacturers';

  protected $description = 'Sync all manufacturers with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:manufacturers');
  }
}
