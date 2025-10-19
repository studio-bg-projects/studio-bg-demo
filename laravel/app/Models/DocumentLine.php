<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property integer $id - Уникален идентификатор на реда в документа
 * @property integer $documentId - Външен ключ към документа, към който принадлежи редът
 * @property integer $productId - Идентификатор на продукта, свързан с реда, ако е приложимо
 * @property string $name - Име на продукта или услугата, изписано на реда
 * @property string $mpn - Каталожен номер на производителя, използван за проследяване
 * @property string $ean - EAN баркод на продукта за автоматизирано сканиране
 * @property string $po - Референция към поръчка или заявка, свързана с реда
 * @property double $price - Единична цена на продукта без допълнителни изчисления
 * @property integer $quantity - Количество на продукта или услугата в реда
 * @property double $totalPrice - Общата сума за реда след умножение на цена и количество
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизирана от Eloquent
 * // Relations
 * @property Document $document - Документът, към който е прикачен този ред
 * @property Product|null $product - Свързаният продукт, ако редът е асоцииран със складов артикул
 * @property DocumentItem[] $documentItems - Свързани допълнителни позиции за детайлизиране на реда
 * @property StorageExitsItem[] $exitsItems - Записи за изписвания от склада, които използват този ред
 */
class DocumentLine extends BaseModel
{
  protected $casts = [
    'price' => 'double',
    'quantity' => 'integer',
    'totalPrice' => 'double',
  ];

  protected $fillable = [
    'documentId',
    'productId',
    'name',
    'mpn',
    'ean',
    'po',
    'price',
    'quantity',
    'totalPrice',
  ];

  public function document(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'documentId');
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'productId');
  }

  public function documentItems(): HasMany
  {
    return $this->hasMany(DocumentItem::class, 'documentLineId');
  }

  public function exitsItems(): HasMany
  {
    return $this->hasMany(StorageExitsItem::class, 'documentLineId');
  }
}
