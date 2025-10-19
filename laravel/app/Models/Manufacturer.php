<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникален идентификатор на производителя
 * @property string $name - Името на производителя
 * @property integer $sortOrder - Подредба на производителя в списъци и филтри
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property boolean $isActive - Флаг дали производителят е активен за използване
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Product[] $products - Списък с продуктите, свързани с производителя
 * @property Upload[] $uploads - Списък с качените файлове, асоциирани чрез fileGroupId
 */
class Manufacturer extends BaseModel
{
  protected $casts = [
    'sortOrder' => 'integer',
    'isActive' => 'boolean',
  ];

  protected $fillable = [
    'name',
    'sortOrder',
    'fileGroupId',
    'isActive',
  ];

  public function __construct(array $attributes = array())
  {
    $this->setRawAttributes([
      'fileGroupId' => Str::random(50),
    ], true);

    parent::__construct($attributes);
  }

  protected static function booted()
  {
    static::replicating(function (self $row) {
      $row->fileGroupId = Str::random(50);
    });
  }

  public function products(): HasMany
  {
    return $this->hasMany(Product::class, 'manufacturerId')
      ->orderBy('id', 'desc');
  }

  public function uploads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'fileGroupId')
      ->where('groupType', 'manufacturers')
      ->orderBy('sortOrder');
  }
}
