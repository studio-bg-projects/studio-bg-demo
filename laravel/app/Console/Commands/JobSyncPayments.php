<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncPayments extends Command
{
  protected $signature = 'job:sync:payments';

  protected $description = 'Sync all incomes & documents tostore';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:payments');
  }
}
