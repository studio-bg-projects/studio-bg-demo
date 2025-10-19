<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id - Уникален идентификатор на артикула от импорта
 * @property string $uniqueId - Външен уникален код на артикула във входящия фийд
 * @property int $parentId - Идентификатор на импорта, от който идва записът
 * @property string $itemName - Име на артикула според доставчика
 * @property string $itemEan - EAN код предоставен от доставчика
 * @property string $itemMpn - Производствен номер (MPN) на артикула
 * @property double $itemPrice - Доставна цена на артикула от фийда
 * @property int $itemQuantity - Наличност, докладвана от фийда
 * @property array|null $data - Допълнителни сурови данни от фийда
 * @property int|null $productId - Свързан продукт в системата, ако е наличен
 * @property bool $isLeadRecord - Маркира водещ запис при импортиране на варианти
 * @property bool $skipSync - Флаг дали записът да се пропусне при синхронизация
 * @property bool $isSynced - Статус дали записът е синхронизиран към продуктите
 * @property bool $isIgnored - Да се показва ли записва в листинга с айтъми (използва се за улеснено преглеждане)
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property FeedImport $feedImport - Импортът, към който принадлежи записът
 * @property Product|null $product - Продуктът, който е свързан с импортирания артикул
 */
class FeedImportItem extends BaseModel
{
  protected $casts = [
    'itemPrice' => 'double',
    'itemQuantity' => 'integer',
    'data' => 'array',
    'isSynced' => 'boolean',
    'isLeadRecord' => 'boolean',
    'skipSync' => 'boolean',
    'isIgnored' => 'boolean',
  ];

  protected $fillable = [
    'uniqueId',
    'parentId',
    'itemName',
    'itemEan',
    'itemMpn',
    'itemPrice',
    'itemQuantity',
    'data',
    'productId',
    'isLeadRecord',
    'skipSync',
    'isSynced',
    'isIgnored',
  ];

  public function feedImport(): BelongsTo
  {
    return $this->belongsTo(FeedImport::class, 'parentId');
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }
}
