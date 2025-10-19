<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id - Уникалният идентификатор на импорта на фийда
 * @property string $providerName - Официалното име на доставчика на продуктови данни
 * @property string $adapterName - Името на адаптера, който обработва фийда
 * @property string $feedUrl - Уеб адресът, от който се изтегля фийдът
 * @property double $markupPercent - Процентът надценка, който се добавя върху цените от фийда
 * @property string|null $techEmail - Имейл за технически контакти при възникнал проблем със синхронизацията
 * @property string|null $note - Допълнителна вътрешна бележка за този импорт
 * @property array|null $syncSchedule - Настройки за графика на синхронизацията във формат на масив
 * @property DateTime|null $lastSync - Дата и час на последната успешна синхронизация
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 *  // Relations
 * @property FeedImportItem[] $items
 */
class FeedImport extends BaseModel
{
  protected $casts = [
    'syncSchedule' => 'array',
    'markupPercent' => 'double',
    'lastSync' => 'datetime',
  ];

  protected $fillable = [
    'providerName',
    'adapterName',
    'feedUrl',
    'markupPercent',
    'techEmail',
    'note',
    'syncSchedule',
    'lastSync',
  ];

  public function items(): HasMany
  {
    return $this->hasMany(FeedImportItem::class, 'parentId');
  }
}
