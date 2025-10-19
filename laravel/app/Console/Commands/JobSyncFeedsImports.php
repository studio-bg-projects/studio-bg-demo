<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncFeedsImports extends Command
{
  protected $signature = 'job:sync:feeds-imports';

  protected $description = 'Sync all feeds imports';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:feeds-imports');
  }
}
