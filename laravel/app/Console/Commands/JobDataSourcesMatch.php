<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobDataSourcesMatch extends Command
{
  protected $signature = 'job:data-sources-match';

  protected $description = 'Match the items from data sources to products & feed items';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('data-sources-match');
  }
}
