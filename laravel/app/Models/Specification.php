<?php

namespace App\Models;

use App\Enums\SpecificationValueType;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id - Уникален идентификатор на спецификацията
 * @property string $nameBg - Наименование на спецификацията на български език
 * @property string $nameEn - Наименование на спецификацията на английски език
 * @property SpecificationValueType $valueType - Тип на стойността (стринг, число, булев и т.н.)
 * @property string|null $options - Допълнителни опции, когато стойността е списък
 * @property boolean $isActive - Флаг дали спецификацията е активна за използване
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Model[] $categories - Категории, в които спецификацията се използва
 */
class Specification extends BaseModel
{
  protected $casts = [
    'valueType' => SpecificationValueType::class,
    'isActive' => 'boolean',
  ];

  protected $fillable = [
    'nameBg',
    'nameEn',
    'valueType',
    'options',
    'isActive',
  ];

  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class, 'categoriesSpecifications', 'specificationId', 'categoryId')
      ->withPivot('sortOrder')
      ->orderBy('sortOrder');
  }
}
