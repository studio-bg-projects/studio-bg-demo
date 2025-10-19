<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id - Уникален идентификатор на разпределението на плащане
 * @property int $incomeId - Идентификатор на прихода, към който се отнася сумата
 * @property int|null $documentId - Документът, по който е насочено разпределението
 * @property int|null $orderId - Поръчката, извлечена от документа за удобство при справки
 * @property string|null $description - Обяснителен текст за начина на разпределяне
 * @property double $allocatedAmount - Сумата, която е разпределена към конкретния документ
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 *  // Relations
 * @property Income $income - Приходът, към който принадлежи разпределението
 * @property Document|null $document - Документът, който получава част от плащането
 * @property Order|null $order - Поръчката, асоциирана чрез документа при наличност
 */
class IncomesAllocation extends BaseModel
{
  protected $casts = [
    'allocatedAmount' => 'double',
  ];

  protected $fillable = [
    'incomeId',
    'documentId',
    'description',
    'allocatedAmount',
  ];

  protected static function booted()
  {
    // Set order id
    static::saving(function ($allocation) {
      if ($allocation->isDirty('documentId')) {
        $document = Document::select('orderId')->find($allocation->documentId);
        if ($document) {
          $allocation->orderId = $document->orderId;
        }
      }
    });
  }

  public function income(): BelongsTo
  {
    return $this->belongsTo(Income::class, 'incomeId');
  }

  public function document(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'documentId');
  }

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'orderId');
  }
}
