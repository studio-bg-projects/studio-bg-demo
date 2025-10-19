<?php

namespace App\Models;

use DateTime;

/**
 * @property int $id - Unique identifier of the category in the table
 * @property int $categoryId - ID-то на категорията от външната база данни
 * @property null|int $parentId - Релация към родителската категория (categoryId)
 * @property null|string $name - Category name provided by Icecat
 * @property null|string $descriptionEn - Описание на категорията
 * @property DateTime $createdAt - Record creation date managed by Eloquent
 * @property DateTime $updatedAt - Date of the last update managed by Eloquent
 */
class DataSourceCategory extends BaseModel
{
  protected $connection = 'mysql-data';

  protected $fillable = [
    'categoryId',
    'parentId',
    'name',
    'descriptionEn',
  ];
}
