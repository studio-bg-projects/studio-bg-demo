<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncProducts extends Command
{
  protected $signature = 'job:sync:products';

  protected $description = 'Sync all products with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:products');
  }
}
