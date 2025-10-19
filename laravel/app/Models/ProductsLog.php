<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id - Уникален идентификатор на записа в журнала на продуктите
 * @property int $productId - Продуктът, за който е записана промяната
 * @property string $action - Тип на действието (create/update и др.)
 * @property array|null $original - Данни преди промяната, когато са налични
 * @property array $new - Новите стойности след промяната
 * @property string $place - Произход на действието (клас и ред), записан за аудит
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Product $product - Продуктът, към който се отнася журналният запис
 */
class ProductsLog extends BaseModel
{
  protected $table = 'productsLog';

  protected $casts = [
    'original' => 'array',
    'new' => 'array',
  ];

  protected $fillable = [
    'productId',
    'action',
    'original',
    'new',
    'place',
  ];

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }
}
