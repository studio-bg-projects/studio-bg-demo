<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobCleanupUploads extends Command
{
  protected $signature = 'job:cleanup:uploads';

  protected $description = 'Cleanup all files';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('cleanup:uploads');
  }
}
