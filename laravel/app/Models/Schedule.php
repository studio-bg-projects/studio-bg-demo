<?php

namespace App\Models;

use DateTime;

/**
 * @property string $jobId - Уникален идентификатор на планираната задача
 * @property integer $interval - Интервал в секунди между две последователни синхронизации
 * @property DateTime $lastSync - Последният момент на изпълнение на задачата
 * @property DateTime $endAt - Крайна дата и час, след които задачата не се изпълнява
 */
class Schedule extends BaseModel
{
  protected $primaryKey = 'jobId';
  public $incrementing = false;
  protected $keyType = 'string';

  public $timestamps = false;

  protected $casts = [
    'lastSync' => 'datetime',
    'endAt' => 'datetime',
  ];
}
