<?php

namespace App\Models;

use App\Enums\CustomerStatusType;
use App\Enums\DocumentType;
use App\Enums\OrderStatus;
use App\Services\MapService;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $id - Уникален идентификатор на клиента
 * @property string $email - Основен имейл за вход в магазина и за уведомления
 * @property string $password - Хеширана парола за достъп до клиентския профил
 * @property string $firstName - Собствено име на клиента
 * @property string $lastName - Фамилно име на клиента
 * @property integer $groupId - Група, към която е присвоен клиентът
 * @property CustomerStatusType $statusType - Текущ статус на клиента според изброимия тип
 * @property boolean $isDeleted - Флаг дали клиентът е маркиран като изтрит от активните списъци
 * @property string|null $preferredLang - Предпочитан език за комуникация и документи
 * @property integer|null $salesRepresentativeId - Търговски представител, който обслужва клиента
 * @property integer|null $paymentTerm - Предложен срок за плащане в дни при издаване на документи
 * @property string|null $preferredIncoterms - Предварително избран Incoterm за търговските документи
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Custom fields
 * @property string|null $companyName - Официално фирмено наименование
 * @property string|null $companyAddress - Адрес на фирмата за фактуриране
 * @property string|null $companyZipCode - Пощенски код на фирмата
 * @property string|null $companyCity - Населено място на фирмата
 * @property integer|null $companyCountryId - Референция към държавата, в която е регистрирана фирмата
 * @property string|null $companyId - ЕИК или идентификационен номер на фирмата
 * @property string|null $companyVatNumber - ДДС номер на фирмата
 * @property string|null $contactSales - Контактно лице по продажби
 * @property string|null $contactPhone - Основен телефон за връзка
 * @property string|null $contactEmail - Основен имейл за контакт
 * @property string|null $financialContactPhone - Телефон на финансовия контакт
 * @property string|null $financialContactEmail - Имейл на финансовия контакт
 * @property boolean $creditLineRequested - Маркира дали е заявена кредитна линия
 * @property integer|null $creditLineRequestValue - Желана стойност на кредитната линия
 * @property integer|null $creditLineValue - Одобрена стойност на кредитната линия
 * @property double|null $creditLineUsed - Усвоена сума от кредитната линия
 * @property double|null $creditLineLeft - Оставащ свободен ресурс от кредитната линия
 * @property string|null $note - Вътрешна бележка за клиента
 * // Aggregated fields
 * @property double|null $totalPayableOrdersAmount - Обща стойност на всички платими поръчки
 * @property double|null $totalPayableOrdersIncomes - Общо постъпили плащания по платимите поръчки
 * // Relations
 * @property CustomersGroup $group - Групата, към която принадлежи клиентът
 * @property SalesRepresentative|null $salesRepresentative - Търговският представител, отговарящ за клиента
 * @property CustomersAddress[] $addresses - Всички адреси, въведени за клиента
 * @property Order[] $orders - Поръчките, направени от клиента
 * @property Income[] $incomes - Регистрирани плащания и приходи от клиента
 * @property Document[] $documents - Издадени документи към клиента
 * @property StorageEntriesIncomeInvoice[] $storageEntriesIncomeInvoices - Входящи фактури, в които клиентът е доставчик
 * @property Country|null $companyCountry - Държавата, към която е асоциирана фирмата на клиента
 * @property StorageItem[] $storageItems - Складови артикули, доставени от клиента
 */
class Customer extends BaseModel
{
  protected $casts = [
    'statusType' => CustomerStatusType::class,
    'isDeleted' => 'boolean',
    'creditLineRequested' => 'boolean',
    'paymentTerm' => 'integer',
    'creditLineRequestValue' => 'integer',
    'creditLineValue' => 'integer',
    'creditLineUsed' => 'double',
    'creditLineLeft' => 'double',
    'totalPayableOrdersAmount' => 'double',
    'totalPayableOrdersIncomes' => 'double',
  ];

  protected $hidden = [
    'password'
  ];

  protected $fillable = [
    'email',
    'password',
    'firstName',
    'lastName',
    'groupId',
    'statusType',
    'isDeleted',
    'preferredLang',
    'salesRepresentativeId',
    'paymentTerm',
    'preferredIncoterms',
    // Custom fields
    'companyName',
    'companyAddress',
    'companyZipCode',
    'companyCity',
    'companyCountryId',
    'companyId',
    'companyVatNumber',
    'contactSales',
    'contactPhone',
    'contactEmail',
    'financialContactPhone',
    'financialContactEmail',
    'creditLineRequested',
    'creditLineRequestValue',
    'creditLineValue',
    'creditLineUsed',
    'creditLineLeft',
    'note',
    // Other fields
    'totalPayableOrdersAmount',
    'totalPayableOrdersIncomes',
  ];

  public function recalc()
  {
    $payableOrdersStatuses = [];
    foreach (OrderStatus::cases() as $status) {
      if (MapService::orderStatus($status)->isPayable) {
        $payableOrdersStatuses[] = $status->value;
      }
    }

    $payableDocumentsTypes = [];
    foreach (DocumentType::cases() as $type) {
      if (MapService::documentTypes($type)->isPayable) {
        $payableDocumentsTypes[] = $type->value;
      }
    }

    // Set totalPayableOrdersAmount
    DB::update('
      UPDATE `customers` AS `c`
      SET `totalPayableOrdersAmount` = (
        SELECT SUM(CAST(`o`.`shopData`->"$.order.total" AS DECIMAL(10, 2)) / CAST(`o`.`shopData`->"$.order.currency_value" AS DECIMAL(10, 2)))
        FROM `orders` AS `o`
        WHERE `o`.`customerId` = `c`.`id`
        AND `o`.`status` IN  ("' . implode('", "', $payableOrdersStatuses) . '")
      )
      WHERE `c`.`id` = :customerId
    ', [
      'customerId' => $this->id,
    ]);

    // Set totalPayableOrdersIncomes
    DB::update('
      UPDATE `customers` AS `c`
      SET `totalPayableOrdersIncomes` = (
        SELECT SUM(`d`.`paidAmount`)
        FROM `orders` AS `o`
        JOIN `documents` AS `d` ON `d`.`orderId` = `o`.`id`
        WHERE `o`.`customerId` = `c`.`id`
        AND `o`.`status` IN  ("' . implode('", "', $payableOrdersStatuses) . '")
        AND `d`.`type` IN  ("' . implode('", "', $payableDocumentsTypes) . '")
      )
      WHERE `c`.`id` = :customerId
    ', [
      'customerId' => $this->id,
    ]);
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo(CustomersGroup::class, 'groupId');
  }

  public function salesRepresentative(): BelongsTo
  {
    return $this->belongsTo(SalesRepresentative::class, 'salesRepresentativeId');
  }

  public function addresses(): HasMany
  {
    return $this->hasMany(CustomersAddress::class, 'customerId')
      ->orderBy('id', 'desc');
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'customerId')
      ->orderBy('id', 'desc');
  }

  public function incomes(): HasMany
  {
    return $this->hasMany(Income::class, 'customerId')
      ->orderBy('id', 'desc');
  }

  public function documents(): HasMany
  {
    return $this->hasMany(Document::class, 'customerId')
      ->orderBy('id', 'desc');
  }

  public function storageEntriesIncomeInvoices(): HasMany
  {
    return $this->hasMany(StorageEntriesIncomeInvoice::class, 'supplierId')
      ->orderBy('id', 'desc');
  }

  public function storageItems(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'supplierId')
      ->orderBy('id', 'desc');
  }

  public function companyCountry(): BelongsTo
  {
    return $this->belongsTo(Country::class, 'companyCountryId');
  }
}
