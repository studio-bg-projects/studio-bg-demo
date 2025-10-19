<?php

namespace App\Services;
class PermissionsService
{
  const string PRODUCTS = 'products';
  const string CUSTOMERS = 'customers';
  const string DOCUMENTS = 'documents';
  const string ORDERS = 'orders';
  const string SHIPMENTS = 'shipments';
  const string INCOMES = 'incomes';
  const string STORAGE = 'storage';
  const string SEARCH_REPORT = 'searchReport';
  const string SYSTEM = 'system';
  const string USERS = 'users';
  const string SUPPLIERS = 'suppliers';
  const string DEMO = 'demo';

  public static function getAllPermission(): array
  {
    return [
      self::PRODUCTS => 'Управление на продуктите (продукти, категории, производители и др.)',
      self::CUSTOMERS => 'Управление на клиентите (одобрение, групи, търговски представители и др.)',
      self::DOCUMENTS => 'Документооборот (преглед, управление на фактури и документи)',
      self::ORDERS => 'Управление на поръчките от онлайн магазина',
      self::SHIPMENTS => 'Управление на доставки',
      self::INCOMES => 'Управление на плащанията',
      self::STORAGE => 'Склад',
      self::SEARCH_REPORT => 'Преглед на търсенията от клиенти',
      self::SUPPLIERS => 'Управление на доставчици',
      self::DEMO => 'Демо модул',
      self::SYSTEM => 'Системни (конфигурация, сървърни задачи)',
      self::USERS => 'Управление на потребители/администратори',
    ];
  }
}
