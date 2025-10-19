<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id - Уникален идентификатор на плащането
 * @property integer $customerId - Външен ключ към клиента който е извършил плащането
 * @property DateTime $paymentDate - Дата на постъпване на сумата според платежния документ
 * @property double $paidAmount - Реално получената сума от клиента
 * @property string $notesPrivate - Вътрешни бележки видими само за екипа
 * @property string $notesPublic - Публични бележки показвани към клиента и документите
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer $customer - Свързаният клиент към който принадлежи плащането
 * @property IncomesAllocation[] $allocations - Разпределенията на плащането към конкретни документи
 */
class Income extends BaseModel
{
  protected $casts = [
    // 'paymentDate'  => 'date', // do not cast because of the format "Y-m-d"
    'paidAmount' => 'double',
  ];

  protected $fillable = [
    'customerId',
    'paymentDate',
    'paidAmount',
    'notesPrivate',
    'notesPublic',
  ];

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }

  public function allocations(): HasMany
  {
    return $this->hasMany(IncomesAllocation::class, 'incomeId')
      ->orderBy('id'); // On allocations listing must be ASC
  }
}
