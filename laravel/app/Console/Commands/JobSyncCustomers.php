<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncCustomers extends Command
{
  protected $signature = 'job:sync:customers';

  protected $description = 'Sync all customers with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:customers');
  }
}
