<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobLoadDataSources extends Command
{
  protected $signature = 'job:load:data-sources';

  protected $description = 'Load Products Data Sources (IceCat)';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('load:data-sources');
  }
}
