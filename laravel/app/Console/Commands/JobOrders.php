<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobOrders extends Command
{
  protected $signature = 'job:sync:orders';

  protected $description = 'Sync orders';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('sync:orders');
  }
}
