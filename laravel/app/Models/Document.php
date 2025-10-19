<?php

namespace App\Models;

use App\Enums\DocumentType;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Picqer\Barcode\Types\TypeCode128;

/**
 * @property int $id - Уникален идентификатор на документа
 * @property DocumentType $type - Тип на документа според вътрешното изброяване
 * @property string|null $documentNumber - Външен номер на документа, показван към клиента
 * @property int|null $customerId - Идентификатор на клиента, към когото е издаден документът
 * @property int|null $orderId - Идентификатор на свързаната продажба или поръчка
 * @property bool $isForeignInvoice - Маркира дали документът е предназначен за чуждестранен клиент
 * @property string|null $incoterms - Договорените Incoterms условия за доставка
 * @property DateTime|null $issueDate - Дата на издаване на документа
 * @property DateTime|null $dueDate - Срок за плащане във формат низ
 * @property string|null $recipientName - Наименование на получателя, изписано във фактурата
 * @property string|null $recipientCompanyId - Идентификационен номер на компанията на получателя
 * @property string|null $recipientVatId - ДДС номер на получателя
 * @property string|null $recipientAddress - Адрес на получателя за фактурата
 * @property string|null $shipToName - Наименование на получателя на доставка
 * @property string|null $shipToCompanyId - Идентификационен номер на юридическото лице за доставката
 * @property string|null $shipToVatId - ДДС номер на получателя на доставката
 * @property string|null $shipToAddress - Адрес за доставка на стоките или услугите
 * @property string|null $issuerNameBg - Наименование на издателя на български език
 * @property string|null $issuerNameEn - Наименование на издателя на английски език
 * @property string|null $issuerCompanyId - Булстат/ЕИК на издателя
 * @property string|null $issuerVatId - ДДС номер на издателя
 * @property string|null $issuerAddressBg - Адрес на издателя на български език
 * @property string|null $issuerAddressEn - Адрес на издателя на английски език
 * @property string|null $issuerBankNameBg - Наименование на банката на издателя на български
 * @property string|null $issuerBankNameEn - Наименование на банката на издателя на английски
 * @property string|null $issuerIBankAddressBg - Адрес на банката на издателя на български
 * @property string|null $issuerIBankAddressEn - Адрес на банката на издателя на английски
 * @property string|null $issuerIban - IBAN на банковата сметка на издателя
 * @property string|null $issuerSwift - SWIFT/BIC код на банковата сметка на издателя
 * @property string|null $incomeMethodBg - Начин на плащане на български език
 * @property string|null $incomeMethodEn - Начин на плащане на английски език
 * @property string|null $incomeCommentBg - Допълнителни инструкции за плащане на български
 * @property string|null $incomeCommentEn - Допълнителни инструкции за плащане на английски
 * @property double|null $vatRate - Приложим процент на ДДС за документа
 * @property double|null $totalAmountNoVat - Обща стойност без начислен ДДС
 * @property double|null $totalVat - Начислен размер на ДДС
 * @property double|null $totalAmount - Крайна сума с включен ДДС
 * @property double|null $paidAmount - Сума, която вече е платена по документа
 * @property double|null $leftAmount - Остатък за плащане по документа
 * @property int|null $salesRepresentativeId - Идентификатор на отговорния търговски представител
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property string|null $language - Езиков код, използван при генериране на документа
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Attributes
 * @property string|null $barcode - SVG представяне на баркода на документа
 * @property array $additionals - Допълнителни изчислени стойности за типа и цената
 * // Relations
 * @property DocumentLine[] $lines - Списък с редовете на документа
 * @property Customer|null $customer - Клиентът, към когото е издаден документът
 * @property Upload[] $uploads - Прикачените файлове, групирани към документа
 * @property StorageExitsItem[] $exitsItems - Складовите изходи, които формират документа
 * @property SalesRepresentative|null $salesRepresentative - Търговският представител по документа
 * @property Document[] $related - Други документи, свързани чрез междинната таблица
 * @property IncomesAllocation[] $incomesAllocations - Разпределенията на плащания към документа
 * @property StorageItem[] $outcomeCreditMemoStorageItems - Складови артикули, обвързани с кредитното известие
 */
class Document extends BaseModel
{
  protected $casts = [
    'type' => DocumentType::class,
    'issueDate' => 'datetime',
    'dueDate' => 'datetime',
    'isForeignInvoice' => 'boolean',
    'vatRate' => 'double',
    'totalAmountNoVat' => 'double',
    'totalVat' => 'double',
    'totalAmount' => 'double',
    'paidAmount' => 'double',
    'leftAmount' => 'double',
  ];

  protected $fillable = [
    'type',
    'documentNumber',
    'customerId',
    'orderId',
    'isForeignInvoice',
    'incoterms',
    'issueDate',
    'dueDate',
    'recipientName',
    'recipientCompanyId',
    'recipientVatId',
    'recipientAddress',
    'shipToName',
    'shipToCompanyId',
    'shipToVatId',
    'shipToAddress',
    'issuerNameBg',
    'issuerNameEn',
    'issuerCompanyId',
    'issuerVatId',
    'issuerAddressBg',
    'issuerAddressEn',
    'issuerBankNameBg',
    'issuerBankNameEn',
    'issuerIBankAddressBg',
    'issuerIBankAddressEn',
    'issuerIban',
    'issuerSwift',
    'incomeMethodBg',
    'incomeMethodEn',
    'incomeCommentBg',
    'incomeCommentEn',
    'vatRate',
    'totalAmountNoVat',
    'totalVat',
    'totalAmount',
    'paidAmount',
    'leftAmount',
    'salesRepresentativeId',
    'fileGroupId',
    'language',
  ];

  protected $appends = [
    'barcode',
    'additionals',
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

  public function resyncPaid()
  {
    $this->paidAmount = 0;
    $this->leftAmount = 0;

    if (\App\Services\MapService::documentTypes($this->type)->isPayable) {
      foreach ($this->incomesAllocations as $allocation) {
        /* @var $allocation IncomesAllocation */
        $this->paidAmount += $allocation->allocatedAmount;
      }

      $this->leftAmount = $this->totalAmount - $this->paidAmount;
    }

    $this->save();

    if ($this->customerId) {
      $this->customer->recalc();
    }
  }

  public function getBarcodeAttribute()
  {
    if ($this->documentNumber) {
      $barcode = (new TypeCode128())->getBarcode($this->documentNumber);
      $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
      return $renderer->render($barcode, $barcode->getWidth(), 15);
    } else {
      return null;
    }
  }

  public function getAdditionalsAttribute()
  {
    return [
      'typeTitle' => \App\Services\MapService::documentTypes($this->type)->labelBg,
      'isPayable' => \App\Services\MapService::documentTypes($this->type)->isPayable,
      'price' => price($this->totalAmount),
    ];
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'orderId');
  }

  public function uploads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'fileGroupId')
      ->where('groupType', 'documents')
      ->orderBy('sortOrder');
  }

  public function lines(): HasMany
  {
    return $this->hasMany(DocumentLine::class, 'documentId')
      ->orderBy('id'); // On lines listing must be ASC
  }

  public function exitsItems(): HasMany
  {
    return $this->hasMany(StorageExitsItem::class, 'outcomeInvoiceId');
  }

  public function salesRepresentative(): BelongsTo
  {
    return $this->belongsTo(SalesRepresentative::class, 'salesRepresentativeId');
  }

  public function related(): BelongsToMany
  {
    return $this->belongsToMany(Document::class, 'documentRelated', 'documentId', 'relatedId')
      ->withTimestamps();
  }

  public function incomesAllocations(): HasMany
  {
    return $this->hasMany(IncomesAllocation::class, 'documentId')
      ->orderBy('id'); // On allocations listing must be ASC
  }

  public function outcomeCreditMemoStorageItems(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'outcomeCreditMemoDocumentId');
  }
}
