<?php

namespace App\Models;

use DateTime;

/**
 * @property string $key - Уникален ключ на конфигурационния запис, използван като първичен идентификатор
 * @property string $value - Записаната стойност на конфигурацията според зададения тип
 * @property string $type - Тип на стойността (например string, integer), който определя начина на интерпретация
 * @property boolean $isLocked - Флаг дали настройката е заключена за редакция в интерфейса
 * @property boolean $isHidden - Показва дали настройката трябва да бъде скрита от модула за конфигурация
 * @property string $description - Текстово описание, поясняващо предназначението на конфигурацията
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 */
class Config extends BaseModel
{
  protected $fillable = [
    'value',
  ];
  protected $primaryKey = 'key';
  public $incrementing = false;

  protected $casts = [
    'isLocked' => 'boolean',
    'isHidden' => 'boolean',
  ];
}
