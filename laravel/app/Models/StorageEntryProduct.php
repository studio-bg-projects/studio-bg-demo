<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id - Уникален идентификатор на реда за заприхождаване
 * @property int $storageEntriesIncomeInvoiceId - Референция към входящата фактура за заприхождаване
 * @property int $productId - Идентификатор на продукта
 * @property double $purchasePrice - Покупната цена за този ред
 * @property int $arrangementSeq - Подредба на реда във фактурата
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageEntriesIncomeInvoice $incomeInvoice - Входящата фактура, към която принадлежи редът
 * @property Product $product - Продуктът, който се заприхождава
 * @property StorageItem[] $items - Артикулите, създадени от този ред
 */
class StorageEntryProduct extends BaseModel
{
  protected $table = 'storageEntriesProducts';
  protected $casts = [
    'purchasePrice' => 'double',
    'arrangementSeq' => 'integer',
  ];

  protected $fillable = [
    'storageEntriesIncomeInvoiceId',
    'productId',
    'purchasePrice',
    'arrangementSeq',
  ];

  public function incomeInvoice(): BelongsTo
  {
    return $this->belongsTo(StorageEntriesIncomeInvoice::class, 'storageEntriesIncomeInvoiceId');
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }

  public function items(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'storageEntryProductsId');
  }
}
