<?php

namespace App\Models;

use App\Enums\OrderEventAction;
use App\Enums\OrderEventActorType;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

/**
 * @property integer $id - Уникален идентификатор на записа в историята на поръчката
 * @property integer $orderId - Референция към поръчката, за която е записано събитието
 * @property OrderEventAction $action - Тип на извършеното действие
 * @property string|null $actionNote - Допълнителна текстова бележка към действието
 * @property stdClass|null $actionData - Допълнителни данни за действието, съхранени в JSON формат
 * @property OrderEventActorType $actorType - Посочва дали действието е от система или оператор
 * @property integer|null $actorId - Идентификатор на оператора, ако събитието е извършено от човек
 * @property stdClass|null $actorData - Допълнителна информация за извършителя, запазена в JSON
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Order $order - Поръчката, към която принадлежи събитието
 */
class OrdersEvent extends BaseModel
{
  protected $casts = [
    'action' => OrderEventAction::class,
    'actorType' => OrderEventActorType::class,
    'actionData' => 'object',
    'actorData' => 'object',
  ];

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'orderId');
  }
}
