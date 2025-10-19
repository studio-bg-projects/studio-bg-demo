<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id - Уникален идентификатор на артикула в офертата
 * @property integer $offerId - Връзка към офертата, към която принадлежи редът
 * @property integer|null $productId - Връзка към каталожния продукт, ако редът е обвързан с наличен продукт
 * @property string $name - Име на артикула такъв, какъвто се показва в офертата
 * @property string|null $mpn - Производствен номер на артикула, ако е известен
 * @property string|null $ean - EAN код на артикула, ако е наличен
 * @property double $price - Единична продажна цена без включена отстъпка
 * @property integer $quantity - Количество на артикула в офертата
 * @property double $totalPrice - Крайна стойност за реда след прилагане на отстъпката
 * @property double $discountPercent - Процент на търговската отстъпка за реда
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Offer $offer - Офертата, към която принадлежи текущият ред
 * @property Product|null $product - Свързаният продукт от каталога, ако е избран
 */
class OfferItem extends BaseModel
{
  protected $casts = [
    'price' => 'double',
    'quantity' => 'integer',
    'totalPrice' => 'double',
    'discountPercent' => 'double',
  ];

  protected $fillable = [
    'offerId',
    'productId',
    'name',
    'mpn',
    'ean',
    'price',
    'quantity',
    'totalPrice',
    'discountPercent',
  ];

  public function offer(): BelongsTo
  {
    return $this->belongsTo(Offer::class, 'offerId');
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }
}
