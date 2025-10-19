<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникалният първичен ключ на категорията
 * @property integer $parentId - Идентификатор на родителската категория при вложена структура
 * @property string $nameBg - Българско наименование на категорията
 * @property string $nameEn - Английско наименование на категорията
 * @property integer $sortOrder - Числова стойност за ръчно подреждане на категориите
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property boolean $isActive - Флаг дали категорията е активна
 * @property boolean $isHidden - Флаг дали категорията трябва да бъде скрита от интерфейса
 * @property boolean $isHomeSlider - Маркер дали категорията участва в началния слайдер в магазина
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Category $parent - Релация към родителската категория
 * @property Category[] $children - Списък с подкатегории, подредени по низходящ идентификатор
 * @property Product[] $products - Продукти, свързани чрез междинната таблица categoriesProducts
 * @property Upload[] $uploads - Качени файлове към категорията, филтрирани по groupType categories
 * @property Specification[] $specifications - Спецификации с пивот полета id и sortOrder, подредени по sortOrder
 */
class Category extends BaseModel
{
  protected $casts = [
    'isActive' => 'boolean',
    'isHidden' => 'boolean',
    'isHomeSlider' => 'boolean',
  ];

  protected $fillable = [
    'parentId',
    'nameBg',
    'nameEn',
    'sortOrder',
    'fileGroupId',
    'isActive',
    'isHidden',
    'isHomeSlider',
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

  public function parent(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'parentId');
  }

  public function children(): HasMany
  {
    return $this->hasMany(Category::class, 'parentId')
      ->orderBy('sortOrder');
  }

  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'categoriesProducts', 'categoryId', 'productId');
  }

  public function uploads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'fileGroupId')
      ->where('groupType', 'categories')
      ->orderBy('sortOrder');
  }

  public function specifications(): BelongsToMany
  {
    return $this->belongsToMany(Specification::class, 'categoriesSpecifications', 'categoryId', 'specificationId')
      ->withPivot('id', 'sortOrder')
      ->orderBy('sortOrder');
  }
}
