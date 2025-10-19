<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\SchedulesService;

class SchedulersController extends Controller
{
  protected SchedulesService $schedulesService;

  public function __construct()
  {
    $this->schedulesService = new SchedulesService();
    parent::__construct();
  }

  public function index()
  {
    $schedulers = Schedule::all();

    return view('erp.schedulers.index', [
      'schedulers' => $schedulers,
    ]);
  }

  public function view(string $jobId)
  {
    /* @var $schedule Schedule */
    $schedule = Schedule::where('jobId', $jobId)->firstOrFail();

    return view('erp.schedulers.view', [
      'schedule' => $schedule,
    ]);
  }

  public function run(string $jobId)
  {
    /* @var $schedule Schedule */
    Schedule::where('jobId', $jobId)->firstOrFail();

    print '<pre>';
    $this->schedulesService->run($jobId);
    print '</pre>';
  }
}
