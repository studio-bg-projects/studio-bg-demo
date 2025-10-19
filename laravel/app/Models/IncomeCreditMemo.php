<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id - Уникален идентификатор на кредитното известие
 * @property int $incomeInvoiceId - Референция към входящата фактура за заприхождаване
 * @property DateTime $date - Дата на кредитното известие
 * @property string|null $note - Вътрешна бележка към кредитното известие
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageEntriesIncomeInvoice $incomeInvoice - Входящата фактура, към която е приложено известието
 * @property StorageItem[] $storageItems - Артикули, коригирани чрез кредитното известие
 * @property StorageExitsItem[] $storageExitsItems - Изписвания, свързани с кредитното известие
 */
class IncomeCreditMemo extends BaseModel
{
  protected $casts = [
    'date' => 'datetime',
  ];

  protected $fillable = [
    'incomeInvoiceId',
    'date',
    'note',
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

  public function incomeInvoice(): BelongsTo
  {
    return $this->belongsTo(StorageEntriesIncomeInvoice::class, 'incomeInvoiceId');
  }

  public function storageItems(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'priceCorrectionIncomeCreditMemoId');
  }

  public function storageExitsItems(): HasMany
  {
    return $this->hasMany(StorageExitsItem::class, 'incomeCreditMemoId');
  }
}
