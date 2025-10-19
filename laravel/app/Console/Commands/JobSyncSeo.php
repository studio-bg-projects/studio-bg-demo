<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncSeo extends Command
{
  protected $signature = 'job:sync:seo';

  protected $description = 'Sync all seo urls with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:seo');
  }
}
