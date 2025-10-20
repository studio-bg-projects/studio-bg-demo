<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property int $priority
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon $updatedAt
 */
class Task extends Model
{
  use HasFactory;

  public const CREATED_AT = 'createdAt';
  public const UPDATED_AT = 'updatedAt';

  protected $fillable = [
    'title',
    'priority'
  ];

  protected $casts = [
    'priority' => 'integer'
  ];
}
