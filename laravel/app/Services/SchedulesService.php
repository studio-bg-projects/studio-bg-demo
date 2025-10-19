<?php

namespace App\Services;

use App\Models\Schedule;
use App\Services\Jobs\CleanupUploadsJob;
use App\Services\Jobs\DataSourcesMatchJob;
use App\Services\Jobs\LoadDataSourcesJob;
use App\Services\Jobs\SendMailQueue;
use App\Services\Jobs\SyncCategoriesJob;
use App\Services\Jobs\SyncCustomersAddressesJob;
use App\Services\Jobs\SyncCustomersJob;
use App\Services\Jobs\SyncFeedsImports;
use App\Services\Jobs\SyncFiltersJob;
use App\Services\Jobs\SyncManufacturersJob;
use App\Services\Jobs\SyncMiscJob;
use App\Services\Jobs\SyncOrdersJob;
use App\Services\Jobs\SyncPaymentsJob;
use App\Services\Jobs\SyncProductsJob;
use App\Services\Jobs\SyncSeoJob;
use App\Services\Jobs\SyncSpecificationsJob;
use App\Services\Jobs\SyncStoreSearchesJob;
use DateTime;
use Exception;

class SchedulesService
{
  public function run(string $jobId)
  {
    /* @var $schedule Schedule */
    $schedule = Schedule::where('jobId', $jobId)->first();

    if (!$schedule) {
      throw new Exception(sprintf('Unknown schedule id: %s', $jobId));
    }

    $schedule->lastSync = new DateTime();

    switch ($jobId) {
      case 'cleanup:uploads':
      {
        $job = new CleanupUploadsJob();
        $job->run();
        break;
      }
      case 'sync:categories':
      {
        $job = new SyncCategoriesJob();
        $job->run();
        break;
      }
      case 'sync:manufacturers':
      {
        $job = new SyncManufacturersJob();
        $job->run();
        break;
      }
      case 'sync:products':
      {
        $job = new SyncProductsJob();
        $job->run();
        break;
      }
      case 'sync:specifications':
      {
        $job = new SyncSpecificationsJob();
        $job->run();
        break;
      }
      case 'sync:filters':
      {
        $job = new SyncFiltersJob();
        $job->run();
        break;
      }
      case 'sync:seo':
      {
        $job = new SyncSeoJob();
        $job->run();
        break;
      }
      case 'sync:customers':
      {
        $job = new SyncCustomersJob();
        $job->run();
        break;
      }
      case 'sync:customers-addresses':
      {
        $job = new SyncCustomersAddressesJob();
        $job->run();
        break;
      }
      case 'sync:store-searches':
      {
        $job = new SyncStoreSearchesJob();
        $job->run();
        break;
      }
      case 'sync:orders':
      {
        $job = new SyncOrdersJob();
        $job->run();
        break;
      }
      case 'sync:payments':
      {
        $job = new SyncPaymentsJob();
        $job->run();
        break;
      }
      case 'sync:misc':
      {
        $job = new SyncMiscJob();
        $job->run();
        break;
      }
      case 'sync:feeds-imports':
      {
        $job = new SyncFeedsImports();
        $job->run();
        break;
      }
      case 'send-mail-queue':
      {
        $job = new SendMailQueue();
        $job->run();
        break;
      }
      case 'load:data-sources':
      {
        $job = new LoadDataSourcesJob();
        $job->run();
        break;
      }
      case 'data-sources-match':
      {
        $job = new DataSourcesMatchJob();
        $job->run();
        break;
      }
      default:
      {
        throw new Exception(sprintf('Unhandled job id: %s', $jobId));
      }
    }

    $schedule->endAt = new DateTime();
    $schedule->save();
  }
}
