<?php

namespace App\Models;

use DateTime;

/**
 * @property string $key - Уникален API ключ, който служи за последващ достъп
 * @property string $description - Вътрешно описание, обясняващо предназначението на ключа
 * @property string $case - Идентификатор на case/канал за генерираните публични фийдове
 * @property int $requestsCount - Брояч на всички заявки, изпратени с ключа
 * @property DateTime $latestRequest - Дата и час на последната заявка, обновяван при валидиране
 * @property array|null $requestsLog - История на последните заявки, съхранявана в JSON формат
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 */
class ApiKey extends BaseModel
{
  public $incrementing = false;

  protected $casts = [
    'latestRequest' => 'datetime',
    'requestsCount' => 'integer',
    'requestsLog' => 'array',
  ];

  protected $primaryKey = 'key';
}
