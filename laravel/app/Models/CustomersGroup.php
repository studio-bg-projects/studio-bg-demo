<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id - Уникален идентификатор на групата клиенти
 * @property string $nameBg - Наименование на групата на български език
 * @property string $nameEn - Наименование на групата на английски език
 * @property double $discountPercent - Процент отстъпка за клиентите в групата
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer[] $customers - Списък с клиентите, принадлежащи към групата
 */
class CustomersGroup extends BaseModel
{
  protected $casts = [
    'discountPercent' => 'double',
  ];

  protected $fillable = [
    'nameBg',
    'nameEn',
    'discountPercent',
  ];

  public function customers(): HasMany
  {
    return $this->hasMany(Customer::class, 'groupId')
      ->orderBy('id', 'desc');
  }
}
