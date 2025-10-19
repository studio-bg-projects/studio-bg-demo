<?php

namespace App\Models;

use App\Enums\OrderEventAction;
use App\Enums\OrderStatus;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * @property integer $id - Уникален идентификатор на поръчката
 * @property integer $customerId - Референция към клиента, който е направил поръчката
 * @property OrderStatus $status - Текущ статус на поръчката според изброимия тип
 * @property stdClass $shopData - Данни от магазина за съдържанието и сумите на поръчката
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer $customer - Клиентът, асоцииран с поръчката
 * @property Document[] $documents - Документите, издадени по поръчката
 * @property IncomesAllocation[] $incomesAllocations - Разпределения на приходи, свързани с поръчката
 * @property OrdersEvent[] $events - Събитията от историята на поръчката
 */
class Order extends BaseModel
{
  public $incrementing = false;

  protected $casts = [
    'status' => OrderStatus::class,
    'shopData' => 'object',
  ];

  protected $fillable = [
    'id',
    'customerId',
    'status',
    'shopData',
  ];

  public function hasEvent(OrderEventAction $action, string|null $actionNote = null, array $matchData = [])
  {
    $events = OrdersEvent::where([
      'orderId' => $this->id,
      'action' => $action,
      ...($actionNote ? ['actionNote' => $actionNote] : []),
    ])->get();

    if ($matchData) {
      /* @var $event OrdersEvent */
      foreach ($events as $event) {
        if (count(array_intersect((array)$event->actionData, $matchData)) === count($matchData)) {
          return true;
        }
      }

      return false;
    } else {
      return count($events) > 0;
    }
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }

  public function documents(): HasMany
  {
    return $this->hasMany(Document::class, 'orderId')
      ->orderBy('id', 'desc');
  }

  public function incomesAllocations(): HasMany
  {
    return $this->hasMany(IncomesAllocation::class, 'orderId')
      ->orderBy('id', 'desc');
  }

  public function events(): HasMany
  {
    return $this->hasMany(OrdersEvent::class, 'orderId')
      ->orderBy('id', 'desc');
  }
}
