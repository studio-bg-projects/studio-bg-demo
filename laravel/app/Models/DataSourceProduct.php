<?php

namespace App\Models;

use DateTime;

/**
 * @property int $id - Unique identifier of the record in the table
 * @property int $externalProductId - Unique identifier of the product from the Icecat catalog
 * @property array $identifiers - List of all available product identifiers (EAN/UPC)
 * @property string|null $modelName - Model name provided by Icecat
 * @property int|null $categoryId - Identifier of the product category
 * @property string|null $pictureUrl - Absolute URL to the product image provided by the source
 * @property array|null $data - Пълната информация за продукта
 * @property DateTime $createdAt - Record creation date managed automatically by Eloquent
 * @property DateTime $updatedAt - Date of the last update synchronized by Eloquent
 */
class DataSourceProduct extends BaseModel
{
  protected $connection = 'mysql-data';

  protected $fillable = [
    'externalProductId',
    'identifiers',
    'modelName',
    'categoryId',
    'pictureUrl',
  ];

  protected $casts = [
    'identifiers' => 'array',
    'data' => 'array',
  ];

  public function scopeWhereIdentifiers($query, array $identifiers)
  {
    $ids = array_values(array_unique(array_map('strval', $identifiers)));

    if (empty($ids)) {
      return $query;
    }

    return $query->whereRaw('JSON_OVERLAPS(`identifiers`, ?)', [json_encode($ids)]);
  }
}

