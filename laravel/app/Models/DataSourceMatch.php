<?php

namespace App\Models;

use DateTime;

/**
 * @property int $id - Уникален идентификатор на записа в таблицата
 * @property int|null $erpProductId - Референция към продукта, който е проверен за съвпадения
 * @property int|null $feedItemId - Референция към импортиран артикул, който е проверен за съвпадения
 * @property bool $hasMatch - Маркер дали са открити съвпадащи продукти в източника
 * @property array|null $matches - Списък с externalProductId стойности от dataSourceProducts, които съвпадат
 * @property DateTime $createdAt - Record creation date managed by Eloquent
 * @property DateTime $updatedAt - Date of the last update managed by Eloquent
 */
class DataSourceMatch extends BaseModel
{
  protected $connection = 'mysql-data';

  protected $casts = [
    'hasMatch' => 'boolean',
    'matches' => 'array',
  ];

  protected $fillable = [
    'erpProductId',
    'feedItemId',
    'hasMatch',
    'matches',
  ];
}
