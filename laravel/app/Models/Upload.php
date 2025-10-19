<?php

namespace App\Models;

use App\Enums\UploadGroupType;
use App\Services\ImgService;
use DateTime;

/**
 * @property integer $id - Уникален идентификатор на качения файл
 * @property UploadGroupType $groupType - Типът група, към която принадлежи файлът
 * @property string $groupId - Идентификатор на групата, използван за организиране на файловете
 * @property string $name - Име на файла, съхранявано в системата
 * @property integer $size - Размер на файла в байтове
 * @property string $hash - Хеш стойност за проверка на целостта на файла
 * @property string $originalName - Оригиналното име на качения файл
 * @property string $extension - Разширение на файла
 * @property string $mimeType - MIME тип на файла за съдържанието
 * @property integer $sortOrder - Подредба на файла в рамките на групата
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 */
class Upload extends BaseModel
{
  protected $fillable = [
    'sortOrder'
  ];

  protected $casts = [
    'groupType' => UploadGroupType::class,
    'size' => 'integer',
    'sortOrder' => 'integer',
  ];

  protected $appends = [
    'urls'
  ];

  public function getUrlsAttribute()
  {
    $rs = new \stdClass();
    $rs->path = $this->groupType->value . '/' . $this->groupId . '/' . $this->name;
    $rs->main = asset('storage/uploads/' . $rs->path);
    $rs->preview = ImgService::url(url: $rs->main, suffix: 'preview');
    $rs->tiny = ImgService::url(url: $rs->main, suffix: 'tiny');
    return $rs;
  }
}
