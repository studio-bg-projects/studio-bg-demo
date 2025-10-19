<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobMisc extends Command
{
  protected $signature = 'job:sync:misc';

  protected $description = 'Sync misc (sales representatives)';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:misc');
  }
}
