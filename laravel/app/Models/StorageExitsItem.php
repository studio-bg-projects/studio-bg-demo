<?php

namespace App\Models;

use App\Enums\StorageItemExitType;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\StorageItemsWriteOffProtocol;

/**
 * @property int $id - Уникален идентификатор на записа за изписване
 * @property int $storageItemId - Артикулът, който е изписан от склада
 * @property int|null $documentLineId - Редът от документа, към който е свързано изписването
 * @property int|null $outcomeInvoiceId - Документът, който е причинил изписването
 * @property double|null $sellPrice - Продажна цена при изписване, ако е приложимо
 * @property double|null $originalPrice - Оригинална покупна цена на артикула
 * @property int|null $incomeCreditMemoId - Входящо кредитно известие, използвано за корекция
 * @property int|null $writeOffProtocolId - Протокол за отписване, ако изписването е чрез бракуване
 * @property int|null $priceCorrectionOutcomeCreditMemoId - Изходящо кредитно известие за корекция на цена
 * @property StorageItemExitType $type - Начинът, по който артикулът е изписан (фактура, протокол, кредитно известие)
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageItem $storageItem - Артикулът, който е изписан
 * @property DocumentLine|null $documentLine - Документният ред, към който е вързан записът
 * @property Document|null $outcomeInvoice - Документът, който е изписал артикула
 * @property IncomeCreditMemo|null $incomeCreditMemo - Свързано входящо кредитно известие
 * @property StorageItemsWriteOffProtocol|null $writeOffProtocol - Протоколът за бракуване, ако има такъв
 * @property Document|null $priceCorrectionOutcomeCreditMemo - Изходящо кредитно известие за ценова корекция
 */
class StorageExitsItem extends BaseModel
{
  protected $table = 'storageExitsItems';

  protected $casts = [
    'type' => StorageItemExitType::class,
    'sellPrice' => 'double',
    'originalPrice' => 'double',
  ];

  protected $fillable = [
    'storageItemId',
    'documentLineId',
    'outcomeInvoiceId',
    'sellPrice',
    'originalPrice',
    'incomeCreditMemoId',
    'type',
    'writeOffProtocolId',
    'priceCorrectionOutcomeCreditMemoId',
  ];

  protected static function booted()
  {
    static::created(function (StorageExitsItem $exit) {
      $item = $exit->storageItem;
      if (!$item) {
        return;
      }

      $action = match ($exit->type) {
        StorageItemExitType::Invoice => 'Отписване чрез фактура',
        StorageItemExitType::WriteOffProtocol => 'Отписване чрез протокол за отписване/бракуване',
        StorageItemExitType::IncomeCreditMemo => 'Изписване чрез входящо кредитно известие',
        default => null,
      };

      if ($action) {
        $item->addHistory($action);
      }
    });
  }

  public function storageItem(): BelongsTo
  {
    return $this->belongsTo(StorageItem::class, 'storageItemId');
  }

  public function documentLine(): BelongsTo
  {
    return $this->belongsTo(DocumentLine::class, 'documentLineId');
  }

  public function outcomeInvoice(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'outcomeInvoiceId');
  }

  public function incomeCreditMemo(): BelongsTo
  {
    return $this->belongsTo(IncomeCreditMemo::class, 'incomeCreditMemoId');
  }

  public function writeOffProtocol(): BelongsTo
  {
    return $this->belongsTo(StorageItemsWriteOffProtocol::class, 'writeOffProtocolId');
  }

  public function priceCorrectionOutcomeCreditMemo(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'priceCorrectionOutcomeCreditMemoId');
  }
}
