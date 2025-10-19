<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id - Уникалният първичен ключ на адреса, синхронизиран между магазина и ERP системата
 * @property integer $customerId - Идентификатор на клиента, към когото е регистриран адресът
 * @property string $firstName - Собствено име на контактното лице, което ще получи доставката
 * @property string $lastName - Фамилно име на контактното лице за адреса
 * @property string $companyName - Фирмено наименование, когато адресът се използва за юридическо лице
 * @property string $zipCode - Пощенски код за избраното населено място
 * @property integer $countryId - Референция към държавата от таблицата countries
 * @property string $city - Име на населеното място, въведено свободно от потребителя
 * @property integer $citySpeedyId - Идентификатор на населеното място по каталога на Спиди за автоматично попълване
 * @property string $street - Основен адресен ред или име на улицата за доставка
 * @property integer $streetSpeedyId - Идентификатор на улицата по каталога на Спиди, ако е наличен
 * @property string $streetNo - Номер на улицата или сградата
 * @property string $blockNo - Номер на блока при жилищни комплекси
 * @property string $entranceNo - Буква или номер на входа
 * @property string $floor - Етаж на адреса за доставка
 * @property string $apartmentNo - Номер на апартамент, офис или помещение
 * @property string $addressDetails - Допълнителни указания или втори адресен ред
 * @property string $phone - Телефон за връзка, използван от куриерите
 * @property string $email - Имейл за потвърждения и връзка относно доставката
 * @property string $operatingHours - Описание на работното време или часови прозорци за приемане на пратки
 * @property boolean $isDeleted - Флаг дали адресът е маркиран за изтриване и очаква синхронизация
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Relations
 * @property Customer $customer - Релация към клиента, който притежава адреса
 * @property Country $country - Релация към държавата, избрана за адреса
 */
class CustomersAddress extends BaseModel
{
  protected $casts = [
    'isDeleted' => 'boolean',
  ];

  protected $fillable = [
    'customerId',
    'firstName',
    'lastName',
    'companyName',
    'zipCode',
    'countryId',
    'city',
    'citySpeedyId',
    'street',
    'streetSpeedyId',
    'streetNo',
    'blockNo',
    'entranceNo',
    'floor',
    'apartmentNo',
    'addressDetails',
    'phone',
    'email',
    'operatingHours',
  ];

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customerId');
  }

  public function country(): BelongsTo
  {
    return $this->belongsTo(Country::class, 'countryId');
  }
}
