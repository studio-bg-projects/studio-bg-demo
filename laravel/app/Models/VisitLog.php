<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ipAddress
 * @property string $path
 * @property string $requestMethod
 * @property string|null $referrer
 * @property string|null $userAgent
 * @property string|null $locale
 * @property string|null $sessionId
 * @property Carbon $visitedAt
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 */
class VisitLog extends Model
{
  use HasFactory;

  public const CREATED_AT = 'createdAt';
  public const UPDATED_AT = 'updatedAt';

  protected $fillable = [
    'ipAddress',
    'path',
    'requestMethod',
    'referrer',
    'userAgent',
    'locale',
    'sessionId',
    'visitedAt'
  ];

  protected $casts = [
    'visitedAt' => 'datetime',
    'createdAt' => 'datetime',
    'updatedAt' => 'datetime'
  ];
}
