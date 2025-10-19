<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id - Уникален идентификатор на връзката между продукт и спецификация
 * @property integer $productId - Външен ключ към продукта, за който се записва спецификацията
 * @property integer $categoryId - Външен ключ към категорията, в чийто контекст е валидна спецификацията
 * @property integer $specificationId - Външен ключ към конкретната спецификация
 * @property string $specificationValueBg - Стойността на спецификацията на български език
 * @property string $specificationValueEn - Стойността на спецификацията на английски език
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Product $product - Продуктът, към който е прикрепена спецификацията
 * @property Category $category - Категорията, в рамките на която се използва спецификацията
 * @property Specification $specification - Дефиницията на спецификацията, описваща каква стойност се съхранява
 */
class ProductsSpecification extends BaseModel
{
  protected $fillable = [
    'productId',
    'categoryId',
    'specificationId',
    'specificationValueBg',
    'specificationValueEn',
  ];

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'categoryId');
  }

  public function specification(): BelongsTo
  {
    return $this->belongsTo(Specification::class, 'specificationId');
  }
}
