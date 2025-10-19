<?php

namespace App\Models;

use App\Enums\DemoStatus;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникален идентификатор на демото в базата данни
 * @property string $demoNumber - Вътрешен номер на демото, използван за търсене и разграничаване
 * @property DemoStatus $status - Текущо състояние на демото според допустимите стойности Draft, Sent, Accepted или Declined
 * @property integer $customerId - Идентификатор на клиента, към когото е асоциирано демото
 * @property DateTime $addedDate - Дата на първоначалното добавяне или планиране на демото
 * @property string $companyName - Наименование на фирмата, с която е свързано демото
 * @property string $notesPublic - Публични бележки, видими за потребителите с достъп до записа
 * @property string $notesPrivate - Вътрешни бележки за екипа, недостъпни за външни потребители
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer $customer - Свързаният клиент, към когото сочи релацията customer
 */
class Demo extends BaseModel
{
  protected $casts = [
    'status' => DemoStatus::class,
  ];

  protected $fillable = [
    'demoNumber',
    'status',
    'customerId',
    'addedDate',
    'companyName',
    'notesPublic',
    'notesPrivate',
    'fileGroupId',
  ];

  public function __construct(array $attributes = array())
  {
    $this->setRawAttributes([
      'fileGroupId' => Str::random(50),
    ], true);

    parent::__construct($attributes);
  }

  protected static function booted()
  {
    static::replicating(function (self $row) {
      $row->fileGroupId = Str::random(50);
    });
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }
}
