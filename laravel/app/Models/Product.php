<?php

namespace App\Models;

use App\Enums\ProductUsageStatus;
use App\Enums\ProductNonSyncStatus;
use App\Enums\ProductSource;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникалният първичен ключ на продукта
 * @property string $nameBg - Българско наименование на продукта
 * @property string $nameEn - Английско наименование на продукта
 * @property string $descriptionBg - Описание на български, използвано във витрината
 * @property string $descriptionEn - Описание на английски за външни канали и интеграции
 * @property integer $quantity - Налично количество в основния склад
 * @property double $price - Продажна цена в основната валута на системата
 * @property double $purchasePrice - Доставна стойност за изчисляване на марж
 * @property string $mpn - Код на производителя (MPN)
 * @property string $ean - EAN баркод за продукта
 * @property double $weight - Тегло на артикула за логистични изчисления
 * @property double $width - Ширина на опаковката в сантиметри
 * @property double $height - Височина на опаковката в сантиметри
 * @property double $length - Дължина на опаковката в сантиметри
 * @property integer $warrantyPeriod - Гаранционен срок в месеци
 * @property integer $deliveryDays - Срок за доставка в дни
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property string $downloadsGroupId- Random идентификатор за свързване към качените файлове
 * @property integer $manufacturerId - Външен ключ към производителя
 * @property boolean $onStock - Флаг дали продуктът е наличен на склад
 * @property boolean $isFeatured - Маркер дали продуктът се показва в избрани секции
 * @property ProductUsageStatus $usageStatus - Статус на използване според ProductUsageStatus
 * @property ProductNonSyncStatus|null $nonSyncStatus - Причина за изключване от синхронизации според ProductNonSyncStatus
 * @property ProductSource $source - Източник на данните според ProductSource
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Manufacturer $manufacturer - Релация към производителя на продукта
 * @property Category[] $categories - Категории, асоциирани чрез междинната таблица categoriesProducts
 * @property Upload[] $uploads - Качени файлове към продукта, филтрирани по groupType products
 * @property Upload[] $downloads - Файлове за изтегляне към продукта, филтрирани по groupType productDownloads
 * @property ProductsSpecification[] $specifications - Технически характеристики, подредени по идентификатор в низходящ ред
 * @property Product[] $related - Свързани продукти, върнати през таблицата productRelated
 * @property FeedImportItem[] $feedImportItems - Редове от импортнати продуктови фийдове за артикула
 * @property StorageEntryProduct[] $storageEntryProducts - Детайли от входящи складови документи
 * @property StorageItem[] $storageItems - Складови наличности по артикула
 * @property ProductsLog[] $logs - История на промените, записана от ProductsLog
 */
class Product extends BaseModel
{
  protected $casts = [
    'quantity' => 'integer',
    'price' => 'double',
    'purchasePrice' => 'double',
    'weight' => 'double',
    'width' => 'double',
    'height' => 'double',
    'length' => 'double',
    'warrantyPeriod' => 'integer',
    'deliveryDays' => 'integer',
    'onStock' => 'boolean',
    'isFeatured' => 'boolean',
    'usageStatus' => ProductUsageStatus::class,
    'nonSyncStatus' => ProductNonSyncStatus::class,
    'source' => ProductSource::class,
  ];

  protected $fillable = [
    'nameBg',
    'nameEn',
    'descriptionBg',
    'descriptionEn',
    'quantity',
    'price',
    'purchasePrice',
    'mpn',
    'ean',
    'weight',
    'width',
    'height',
    'length',
    'warrantyPeriod',
    'deliveryDays',
    'fileGroupId',
    'downloadsGroupId',
    'manufacturerId',
    'onStock',
    'isFeatured',
    'usageStatus',
    'nonSyncStatus',
    'source',
  ];

  public function __construct(array $attributes = array())
  {
    $this->setRawAttributes([
      'fileGroupId' => Str::random(50),
      'downloadsGroupId' => Str::random(50),
    ], true);

    parent::__construct($attributes);
  }

  protected static function booted()
  {
    static::replicating(function (self $row) {
      $row->fileGroupId = Str::random(50);
      $row->downloadsGroupId = Str::random(50);
    });

    static::created(function (self $row) {
      $new = collect($row->attributesToArray())
        ->except(['id', 'createdAt', 'updatedAt'])
        ->toArray();

      $row->logs()->create([
        'action' => 'create',
        'original' => null,
        'new' => $new,
        'place' => self::tracePlace(),
      ]);
    });

    static::updated(function (self $row) {
      $changes = collect($row->getChanges())
        ->except(['updatedAt'])
        ->toArray();

      if (empty($changes)) {
        return;
      }

      $original = collect($row->getOriginal())
        ->only(array_keys($changes))
        ->toArray();

      $row->logs()->create([
        'action' => 'update',
        'original' => $original,
        'new' => $changes,
        'place' => self::tracePlace(),
      ]);
    });

    static::saved(function (self $row) {
      if (!$row->wasRecentlyCreated && !$row->wasChanged(['mpn', 'ean'])) {
        return;
      }

      DataSourceMatch::where(['erpProductId' => $row->id])->delete();

      $matches = DataSourceProduct::whereIdentifiers([
        $row->mpn,
        $row->ean,
      ])
        ->pluck('externalProductId')
        ->unique()
        ->values()
        ->all();

      $dataSourceMatch = new DataSourceMatch();
      $dataSourceMatch->erpProductId = $row->id;
      $dataSourceMatch->hasMatch = !empty($matches);
      $dataSourceMatch->matches = $matches;
      $dataSourceMatch->save();
    });
  }

  protected static function tracePlace(): string
  {
    foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
      $file = $trace['file'] ?? '';
      if (!str_contains($file, DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR)) {
        continue;
      }

      $relative = Str::after($file, base_path() . DIRECTORY_SEPARATOR);
      if ($relative === 'app/Models/Product.php') {
        continue;
      }

      return $relative . ':' . ($trace['line'] ?? '');
    }

    return '';
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(Manufacturer::class, 'manufacturerId');
  }

  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class, 'categoriesProducts', 'productId', 'categoryId')
      ->orderBy('sortOrder');
  }

  public function uploads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'fileGroupId')
      ->where('groupType', 'products')
      ->orderBy('sortOrder');
  }

  public function downloads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'downloadsGroupId')
      ->where('groupType', 'productDownloads')
      ->orderBy('sortOrder');
  }

  public function specifications(): HasMany
  {
    return $this->hasMany(ProductsSpecification::class, 'productId')
      ->orderBy('id', 'desc');
  }

  public function related(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'productRelated', 'productId', 'relatedId')
      ->withTimestamps();
  }

  public function feedImportItems(): HasMany
  {
    return $this->hasMany(FeedImportItem::class, 'productId');
  }

  public function storageEntryProducts(): HasMany
  {
    return $this->hasMany(StorageEntryProduct::class, 'productId');
  }

  public function storageItems(): HasMany
  {
    return $this->hasMany(StorageItem::class, 'productId');
  }

  public function logs(): HasMany
  {
    return $this->hasMany(ProductsLog::class, 'productId');
  }
}
