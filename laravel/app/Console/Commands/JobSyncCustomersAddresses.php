<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSyncCustomersAddresses extends Command
{
  protected $signature = 'job:sync:customers-addresses';

  protected $description = 'Sync all customers addresses with the store';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:customers-addresses');
  }
}
