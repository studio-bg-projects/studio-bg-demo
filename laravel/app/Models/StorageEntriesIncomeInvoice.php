<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id - Уникален идентификатор на входящата фактура за заприхождаване
 * @property string $documentNumber - Номер на входящата фактура
 * @property string $documentDate - Дата на фактурата за заприхождаване
 * @property int $supplierId - Идентификатор на доставчика
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer $supplier - Релация към доставчика
 * @property StorageEntryProduct[] $products - Продуктови редове във входящата фактура
 * @property StorageItem[] $items - Артикули, заприходени по фактурата
 * @property IncomeCreditMemo[] $incomeCreditMemos - Входящи кредитни известия към фактурата
 */
class StorageEntriesIncomeInvoice extends BaseModel
{
  protected $casts = [
    'documentDate' => 'datetime',
  ];

  protected $fillable = [
    'documentNumber',
    'documentDate',
    'supplierId',
    'fileGroupId',
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

  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'supplierId');
  }

  public function products(): HasMany
  {
    return $this->hasMany(StorageEntryProduct::class, 'storageEntriesIncomeInvoiceId');
  }

  public function items(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'storageEntriesIncomeInvoiceId');
  }

  public function incomeCreditMemos(): HasMany
  {
    return $this->hasMany(IncomeCreditMemo::class, 'incomeInvoiceId');
  }
}
