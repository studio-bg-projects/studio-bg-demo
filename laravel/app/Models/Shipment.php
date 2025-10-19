<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

/**
 * @property integer $id - Уникалният идентификатор на пратката в базата данни
 * @property string $parcelId - Външен идентификатор на пратката, предоставен от куриерската система
 * @property string $courier - Наименованието на куриерската компания, която обработва пратката
 * @property integer $orderId - Идентификаторът на поръчката, към която е асоциирана пратката
 * @property integer $customerId - Идентификаторът на клиента, за когото е предназначена пратката
 * @property stdClass $requestData - Изпратените към куриерското API данни, съхранени като обект
 * @property stdClass $responseData - Отговорът от куриерското API, запазен като обект
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Order $order - Свързаната поръчка, към която принадлежи пратката
 * @property Customer $customer - Клиентът, към когото е адресирана пратката
 */
class Shipment extends BaseModel
{
  protected $casts = [
    'requestData' => 'object',
    'responseData' => 'object',
  ];

  protected $fillable = [
    'parcelId',
    'courier',
    'orderId',
    'customerId',
    'requestData',
    'responseData',
  ];

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'orderId');
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }
}
