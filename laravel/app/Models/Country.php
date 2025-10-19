<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id - Уникалният първичен ключ на държавата (съвпада с тези от магазина)
 * @property string $name - Наименование на държавата за визуализация в системата
 * @property string $isoCode2 - Двубуквен ISO 3166-1 alpha-2 код за идентифициране на държавата
 * @property string $isoCode3 - Трибуквен ISO 3166-1 alpha-3 код за идентифициране на държавата
 * // Relations
 * @property CustomersAddress[] $addresses - Списък с клиентски адреси към държавата, подредени по низходящ идентификатор
 */
class Country extends BaseModel
{
  public function addresses(): HasMany
  {
    return $this->hasMany(CustomersAddress::class, 'countryId')
      ->orderBy('id', 'desc');
  }
}
