<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncCategories extends Command
{
  protected $signature = 'job:sync:categories';

  protected $description = 'Sync all categories with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:categories');
  }
}
