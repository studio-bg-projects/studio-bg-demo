<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id - Уникален идентификатор на записа за конкретно търсене в магазина
 * @property string $keyword - Ключовата дума, която клиентът е въвел в търсачката
 * @property string $language - Кода на езика, в който е извършено търсенето
 * @property integer|null $categoryId - Идентификатор на избраната основна категория при филтриране
 * @property integer|null $subCategoryId - Идентификатор на избраната подкатегория при допълнително стесняване
 * @property integer|null $customerId - Идентификатор на клиента, извършил търсенето, ако е автентициран
 * @property integer $results - Брой върнати резултати от търсачката при записа
 * @property string $ip - IP адресът, от който е направено търсенето
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Category|null $category - Релация към основната категория, към която е приложено търсенето
 * @property Category|null $subCategory - Релация към подкатегорията, асоциирана с търсенето
 * @property Customer|null $customer - Релация към клиента, който е инициирал търсенето
 */
class StoreSearch extends BaseModel
{
  protected $fillable = [
    'keyword',
    'language',
    'categoryId',
    'subCategoryId',
    'customerId',
    'results',
    'ip',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'categoryId');
  }

  public function subCategory(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'subCategoryId');
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }
}
