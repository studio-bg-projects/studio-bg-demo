<?php

namespace App\Console\Commands;

use App\Services\SchedulesService;
use Illuminate\Console\Command;

class JobSendMailQueue extends Command
{
  protected $signature = 'job:send-mail-queue';

  protected $description = 'Send all waiting mails';

  public function handle()
  {
    $schedules = new SchedulesService();
    $schedules->run('send-mail-queue');
  }
}
