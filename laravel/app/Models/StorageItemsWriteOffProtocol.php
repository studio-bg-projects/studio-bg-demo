<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id - Уникален идентификатор на протокола за отписване
 * @property int $itemId - Артикулът, за който е изготвен протоколът
 * @property string $reason - Описание на причината за отписване или бракуване
 * @property DateTime $date - Дата на документа за отписване
 * @property string $documentNumber - Номер на протокола
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageItem $item - Артикулът, към който е свързан протоколът
 * @property StorageExitsItem|null $exitItem - Изписването, което създава този протокол
 */
class StorageItemsWriteOffProtocol extends BaseModel
{
  protected $fillable = [
    'itemId',
    'reason',
    'date',
    'documentNumber',
  ];

  protected $casts = [
    'date' => 'datetime',
  ];

  public function item(): BelongsTo
  {
    return $this->belongsTo(StorageItem::class, 'itemId');
  }

  public function exitItem(): HasOne
  {
    return $this->hasOne(StorageExitsItem::class, 'writeOffProtocolId');
  }
}
