<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property integer $id - Уникален идентификатор на търговския представител в системата
 * @property string $nameBg - Име на търговския представител на български език за вътрешни списъци
 * @property string $nameEn - Име на търговския представител на английски език за международни комуникации
 * @property string $titleBg - Длъжност на търговския представител на български език
 * @property string $titleEn - Длъжност на търговския представител на английски език
 * @property string $phone1 - Основен телефон за контакт с търговския представител
 * @property string $phone2 - Допълнителен телефон за връзка при нужда
 * @property string $email1 - Основен имейл на търговския представител за кореспонденция
 * @property string $email2 - Резервен имейл за контакт и уведомления
 * @property string $fileGroupId - Random идентификатор за свързване към качените файлове
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer[] $customers - Клиенти, за които отговаря търговският представител
 * @property Upload[] $uploads - Качени файлове, асоциирани към представителя чрез fileGroupId
 * @property Income[] $incomes - Регистрирани приходи, свързани с дейността на представителя
 * @property Document[] $documents - Документи, издадени във връзка с представителя
 */
class SalesRepresentative extends BaseModel
{
  protected $fillable = [
    'nameBg',
    'nameEn',
    'titleBg',
    'titleEn',
    'phone1',
    'phone2',
    'email1',
    'email2',
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

  public function customers(): HasMany
  {
    return $this->hasMany(Customer::class, 'salesRepresentativeId')
      ->orderBy('id', 'desc');
  }

  public function uploads(): HasMany
  {
    return $this->hasMany(Upload::class, 'groupId', 'fileGroupId')
      ->where('groupType', 'salesRepresentatives')
      ->orderBy('sortOrder');
  }

  public function incomes(): HasMany
  {
    return $this->hasMany(Income::class, 'salesRepresentativeId')
      ->orderBy('id', 'desc');
  }

  public function documents(): HasMany
  {
    return $this->hasMany(Document::class, 'salesRepresentativeId')
      ->orderBy('id', 'desc');
  }
}
