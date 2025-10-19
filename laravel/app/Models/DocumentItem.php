<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id - Уникален идентификатор на връзката между документ и складов артикул
 * @property int $storageItemId - Идентификатор на складовия артикул, който се изписва
 * @property int $documentLineId - Ред от документа, към който е приложен артикулът
 * @property int $documentId - Документът, към който принадлежи редът
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageItem $storageItem - Артикулът, който участва в записа
 * @property DocumentLine $documentLine - Документният ред, към който е свързан артикулът
 * @property Document $document - Документът, в който е включен редът
 */
class DocumentItem extends BaseModel
{
  protected $table = 'documentItems';

  protected $fillable = [
    'storageItemId',
    'documentLineId',
    'documentId',
  ];

  public function storageItem(): BelongsTo
  {
    return $this->belongsTo(StorageItem::class, 'storageItemId');
  }

  public function documentLine(): BelongsTo
  {
    return $this->belongsTo(DocumentLine::class, 'documentLineId');
  }

  public function document(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'documentId');
  }
}
