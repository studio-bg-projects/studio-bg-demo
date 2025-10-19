<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id - Уникален идентификатор на артикула
 * @property int $storageEntryProductsId - Референция към реда за заприхождаване
 * @property int|null $storageEntriesIncomeInvoiceId - Референция към входящата фактура за заприхождаване
 * @property int|null $outcomeCreditMemoDocumentId - Документ на изходящо кредитно известие
 * @property int $productId - Идентификатор на продукта
 * @property double $purchasePrice - Текуща покупна цена на артикула
 * @property double $originalPrice - Оригинална покупна цена при заприхождаване
 * @property string $invoiceNumber - Номер на фактурата за входяща доставка
 * @property DateTime $invoiceDate - Дата на фактурата за входяща доставка
 * @property int $supplierId - Идентификатор на доставчика
 * @property string|null $serialNumber - Сериен номер
 * @property string|null $note - Вътрешна бележка за артикула
 * @property int $arrangementSeq - Подредба на артикула в списъка
 * @property int|null $priceCorrectionIncomeCreditMemoId - Идентификатор на кредитно известие за ценова корекция
 * @property array|null $history - История на действията по артикула, съхранена като JSON
 * @property bool $isExited - Флаг дали артикулът е изписан от склада
 * @property int|null $predecessorId - Артикулът който е бил прододаден чрез, фактура и след кредитно известие е създаден на ново със сегашния запис
 * @property DateTime|null $exitDate - Дата на изписване, ако има такава
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property StorageEntryProduct $entryProduct - Редът за заприхождаване, към който принадлежи артикула
 * @property StorageEntriesIncomeInvoice $storageEntriesIncomeInvoice - Входящата фактура за заприхождаване
 * @property Document $outcomeCreditMemo - Изходящото кредитно известие, с което продуктът е възстановен
 * @property Product $product - Продуктът, към който принадлежи артикула
 * @property Customer $supplier - Доставчикът на артикула
 * @property StorageExitsItem $exit - Изписването на артикула от склада
 * @property StorageItemsWriteOffProtocol $writeOffProtocol - Протокол за отписване
 * @property IncomeCreditMemo $priceCorrectionIncomeCreditMemo - Кредитно известие за ценова корекция
 * @property StorageItem $predecessor - Артикулът, на който се базира текущият запис
 * @property StorageItem $successor - Артикулът, който се базира на текущия запис
 */
class StorageItem extends BaseModel
{
  protected $casts = [
    'purchasePrice' => 'double',
    'originalPrice' => 'double',
    'arrangementSeq' => 'integer',
    'invoiceDate' => 'datetime',
    'history' => 'array',
    'isExited' => 'bool',
    'exitDate' => 'datetime',
  ];

  protected $fillable = [
    'storageEntryProductsId',
    'storageEntriesIncomeInvoiceId',
    'outcomeCreditMemoDocumentId',
    'productId',
    'purchasePrice',
    'originalPrice',
    'invoiceNumber',
    'invoiceDate',
    'supplierId',
    'serialNumber',
    'note',
    'arrangementSeq',
    'priceCorrectionIncomeCreditMemoId',
    'history',
    'isExited',
    'exitDate',
  ];

  public function entryProduct(): BelongsTo
  {
    return $this->belongsTo(StorageEntryProduct::class, 'storageEntryProductsId');
  }

  public function storageEntriesIncomeInvoice(): BelongsTo
  {
    return $this->belongsTo(StorageEntriesIncomeInvoice::class, 'storageEntriesIncomeInvoiceId');
  }

  public function outcomeCreditMemo(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'outcomeCreditMemoDocumentId');
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }

  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'supplierId');
  }

  public function exit(): HasOne
  {
    return $this->hasOne(StorageExitsItem::class, 'storageItemId');
  }

  public function priceCorrectionIncomeCreditMemo(): BelongsTo
  {
    return $this->belongsTo(IncomeCreditMemo::class, 'priceCorrectionIncomeCreditMemoId');
  }

  public function addHistory(string $action, ?string $note = null): void
  {
    $history = $this->history ?? [];
    $history[] = [
      'date' => now()->format('Y-m-d H:i:s'),
      'action' => $action,
      'note' => $note,
    ];
    $this->history = $history;
    $this->save();
  }

  public function predecessor(): BelongsTo
  {
    return $this->belongsTo(StorageItem::class, 'predecessorId');
  }

  public function successor(): hasOne
  {
    return $this->hasOne(StorageItem::class, 'predecessorId');
  }
}
