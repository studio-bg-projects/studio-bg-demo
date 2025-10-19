<?php

namespace App\Models;

use App\Enums\OfferStatus;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникален идентификатор на офертата
 * @property string $offerNumber - Автоматично генериран номер на офертата по година
 * @property OfferStatus $status - Текущ статус на офертата според изброимия тип
 * @property integer $customerId - Идентификатор на клиента, за когото е изготвена офертата
 * @property DateTime $validUntil - Крайна валидност на офертата, след която става невалидна
 * @property string $companyId - Вътрешен код или ЕИК на компанията, която издава офертата
 * @property string $companyName - Официално наименование на компанията, издаваща офертата
 * @property string $companyPerson - Име на контактното лице от компанията
 * @property string $companyEmail - Имейл на контактното лице за комуникация по офертата
 * @property string $companyPhone - Телефон за връзка с компанията по офертата
 * @property string $companyAddress - Адрес на компанията, издаваща офертата
 * @property string $notesPublic - Бележки, видими за клиента в офертата
 * @property string $notesPrivate - Вътрешни бележки, предназначени за екипа
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property OfferItem[] $items - Записите от редове спадащи кътм офертата
 * @property Customer $customer - Клиентът, към когото е насочена офертата
 */
class Offer extends BaseModel
{
  protected $casts = [
    'status' => OfferStatus::class,
    'validUntil' => 'date',
  ];

  protected $fillable = [
    'offerNumber',
    'status',
    'customerId',
    'validUntil',
    'companyId',
    'companyName',
    'companyPerson',
    'companyEmail',
    'companyPhone',
    'companyAddress',
    'notesPublic',
    'notesPrivate',
  ];

  public function items(): HasMany
  {
    return $this->hasMany(OfferItem::class, 'offerId')
      ->orderBy('id');
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }

  public static function generateOfferNumber(): string
  {
    $year = date('Y');
    $prefix = "OFR-$year-";

    $last = self::where('offerNumber', 'like', "$prefix%")
      ->orderByDesc('offerNumber')
      ->value('offerNumber');

    if ($last) {
      $lastNumber = (int)Str::afterLast($last, '-');
      $nextNumber = $lastNumber + 1;
    } else {
      $nextNumber = 1;
    }

    return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
  }
}
