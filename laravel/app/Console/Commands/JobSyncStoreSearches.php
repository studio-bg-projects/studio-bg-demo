<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncStoreSearches extends Command
{
  protected $signature = 'job:sync:store-searches';

  protected $description = 'Sync store searches with the ERP';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:store-searches');
  }
}
