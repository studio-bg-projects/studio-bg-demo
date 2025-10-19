<?php

namespace App\Services;

use App\Enums\CustomerStatusType;
use App\Enums\DocumentType;
use App\Enums\OfferStatus;
use App\Enums\DemoStatus;
use App\Enums\ProductUsageStatus;
use App\Enums\ProductNonSyncStatus;
use App\Enums\ProductSource;
use App\Enums\OrderStatus;
use App\Enums\SpecificationValueType;
use App\Enums\StorageItemExitType;
use stdClass;

class MapService
{
  public static function orderStatus(OrderStatus $status): stdClass
  {
    $object = new stdClass();

    switch ($status->value) {
      case OrderStatus::Unknown->value:
      {
        $object->labelBg = 'Неизвестен';
        $object->labelEn = 'Unknown';
        $object->color = '#6c757d'; // Gray (Neutral)
        $object->shopId = 0;
        $object->isPayable = true;
        $object->isCompleted = false;
        break;
      }
      case OrderStatus::Pending->value:
      {
        $object->labelBg = 'В изчакване';
        $object->labelEn = 'Pending';
        $object->color = '#ffc107'; // Yellow (Warning)
        $object->shopId = 1;
        $object->isPayable = true;
        $object->isCompleted = false;
        break;
      }
      case OrderStatus::Processing->value:
      {
        $object->labelBg = 'В процес на обработка';
        $object->labelEn = 'Processing';
        $object->color = '#17a2b8'; // Blue (Info)
        $object->shopId = 2;
        $object->isPayable = true;
        $object->isCompleted = false;
        break;
      }
      case OrderStatus::Shipped->value:
      {
        $object->labelBg = 'Изпратен';
        $object->labelEn = 'Shipped';
        $object->color = '#0dcaf0'; // Light Blue
        $object->shopId = 3;
        $object->isPayable = true;
        $object->isCompleted = false;
        break;
      }
      case OrderStatus::Complete->value:
      {
        $object->labelBg = 'Завършен / Приключен';
        $object->labelEn = 'Complete';
        $object->color = '#28a745'; // Green (Success)
        $object->shopId = 5;
        $object->isPayable = true;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Canceled->value:
      {
        $object->labelBg = 'Отказан';
        $object->labelEn = 'Canceled';
        $object->color = '#dc3545'; // Red (Danger)
        $object->shopId = 7;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Denied->value:
      {
        $object->labelBg = 'Забранен';
        $object->labelEn = 'Denied';
        $object->color = '#bd2130'; // Dark Red
        $object->shopId = 8;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::CanceledReversal->value:
      {
        $object->labelBg = 'Отменено сторниране';
        $object->labelEn = 'CanceledReversal';
        $object->color = '#fd7e14'; // Orange
        $object->shopId = 9;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Failed->value:
      {
        $object->labelBg = 'Неуспешен';
        $object->labelEn = 'Failed';
        $object->color = '#e83e8c'; // Pink/Red Tone
        $object->shopId = 10;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Refunded->value:
      {
        $object->labelBg = 'Възстановена сума';
        $object->labelEn = 'Refunded';
        $object->color = '#20c997'; // Teal/Greenish
        $object->shopId = 11;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Reversed->value:
      {
        $object->labelBg = 'Сторниран';
        $object->labelEn = 'Reversed';
        $object->color = '#6610f2'; // Indigo
        $object->shopId = 12;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Chargeback->value:
      {
        $object->labelBg = 'Оспорена транзакция (Chargeback)';
        $object->labelEn = 'Chargeback';
        $object->color = '#6f42c1'; // Purple (Alert)
        $object->shopId = 13;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Expired->value:
      {
        $object->labelBg = 'Изтекъл';
        $object->labelEn = 'Expired';
        $object->color = '#adb5bd'; // Light Gray
        $object->shopId = 14;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Processed->value:
      {
        $object->labelBg = 'Обработен';
        $object->labelEn = 'Processed';
        $object->color = '#007bff'; // Blue (Primary)
        $object->shopId = 15;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
      case OrderStatus::Voided->value:
      {
        $object->labelBg = 'Анулиран';
        $object->labelEn = 'Voided';
        $object->color = '#343a40'; // Dark Gray/Black
        $object->shopId = 16;
        $object->isPayable = false;
        $object->isCompleted = true;
        break;
      }
    }

    return $object;
  }

  public static function specificationValueTypes(SpecificationValueType $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case SpecificationValueType::String->value:
      {
        $object->label = 'Текст';
        $object->description = 'Текстово поле за въвеждане на произволни стойности.';
        break;
      }
      case SpecificationValueType::Boolean->value:
      {
        $object->label = 'Да/Не';
        $object->description = 'Поле за избор между две опции - Да или Не.';
        break;
      }
      case SpecificationValueType::Decimal->value:
      {
        $object->label = 'Десетично число';
        $object->description = 'Поле за въвеждане на числови стойности с десетични знаци.';
        break;
      }
      case SpecificationValueType::Number->value:
      {
        $object->label = 'Цяло число';
        $object->description = 'Поле за въвеждане на числови стойности без десетични знаци.';
        break;
      }
      case SpecificationValueType::Option->value:
      {
        $object->label = 'Опция';
        $object->description = 'Поле с предварително зададени опции за избор на една стойност.';
        break;
      }
    }

    return $object;
  }

  public static function documentTypes(DocumentType $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case DocumentType::ProformaInvoice->value:
      {
        $object->labelBg = 'Проформа фактура';
        $object->labelEn = 'Proforma Invoice';
        $object->prefix = 'PRF';
        $object->isPayable = false;
        $object->isDeletable = true;
        $object->relatedType = DocumentType::Invoice;
        $object->relatedRequired = false;
        break;
      }
      case DocumentType::Invoice->value:
      {
        $object->labelBg = 'Фактура';
        $object->labelEn = 'Invoice';
        $object->prefix = 'INV';
        $object->isPayable = true;
        $object->isDeletable = false;
        $object->relatedType = DocumentType::ProformaInvoice;
        $object->relatedRequired = false;
        break;
      }
      case DocumentType::OrderConfirmation->value:
      {
        $object->labelBg = 'Потвърждение на поръчка';
        $object->labelEn = 'Order Confirmation';
        $object->prefix = 'OCN';
        $object->isPayable = false;
        $object->isDeletable = true;
        $object->relatedType = DocumentType::Invoice;
        $object->relatedRequired = true;
        break;
      }
      case DocumentType::DeliveryNote->value:
      {
        $object->labelBg = 'Приемо-предавателен протокол';
        $object->labelEn = 'Delivery Note';
        $object->prefix = 'DLN';
        $object->isPayable = false;
        $object->isDeletable = true;
        $object->relatedType = DocumentType::Invoice;
        $object->relatedRequired = true;
        break;
      }
      case DocumentType::PackingList->value:
      {
        $object->labelBg = 'ППП';
        $object->labelEn = 'Packing List';
        $object->prefix = 'PKL';
        $object->isPayable = false;
        $object->isDeletable = true;
        $object->relatedType = DocumentType::Invoice;
        $object->relatedRequired = true;
        break;
      }
      case DocumentType::OutcomeCreditMemo->value:
      {
        $object->labelBg = 'Кредитно известие';
        $object->labelEn = 'Credit Memo';
        $object->prefix = 'CRM';
        $object->isPayable = false;
        $object->isDeletable = false;
        $object->relatedType = DocumentType::Invoice;
        $object->relatedRequired = true;
        break;
      }
    }

    return $object;
  }

  public static function offerStatuses(OfferStatus $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case OfferStatus::Draft->value:
      {
        $object->label = 'Чернова';
        break;
      }
      case OfferStatus::Sent->value:
      {
        $object->label = 'Изпратена';
        break;
      }
      case OfferStatus::Accepted->value:
      {
        $object->label = 'Приета';
        break;
      }
      case OfferStatus::Declined->value:
      {
        $object->label = 'Отказана';
        break;
      }
    }

    return $object;
  }

  public static function demoStatuses(DemoStatus $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case DemoStatus::Draft->value:
      {
        $object->label = 'Чернова';
        break;
      }
      case DemoStatus::Sent->value:
      {
        $object->label = 'Изпратена';
        break;
      }
      case DemoStatus::Accepted->value:
      {
        $object->label = 'Приета';
        break;
      }
      case DemoStatus::Declined->value:
      {
        $object->label = 'Отказана';
        break;
      }
    }

    return $object;
  }

  public static function customerStatusType(CustomerStatusType $statusType): stdClass
  {
    $object = new stdClass();

    switch ($statusType->value) {
      case CustomerStatusType::Customer->value:
      {
        $object->label = 'Клиент';
        $object->allowInShop = true;
        $object->color = 'success';
        break;
      }
      case CustomerStatusType::Supplier->value:
      {
        $object->label = 'Доставчик';
        $object->allowInShop = false;
        $object->color = 'success';
        break;
      }
      case CustomerStatusType::CustomerSupplier->value:
      {
        $object->label = 'Клиент и доставчик';
        $object->allowInShop = true;
        $object->color = 'success';
        break;
      }
      case CustomerStatusType::WaitingApproval->value:
      {
        $object->label = 'Чака одобрение';
        $object->allowInShop = false;
        $object->color = 'danger';
        break;
      }
      case CustomerStatusType::Archived->value:
      {
        $object->label = 'Архивиран';
        $object->allowInShop = false;
        $object->color = 'secondary';
        break;
      }
    }

    return $object;
  }

  public static function productUsageStatus(ProductUsageStatus $status): stdClass
  {
    $object = new stdClass();

    switch ($status->value) {
      case ProductUsageStatus::NotListed->value:
      {
        $object->label = 'Не се предлага в онлайн магазина и платформите';
        $object->short = 'Неактивен';
        $object->color = 'secondary';
        break;
      }
      case ProductUsageStatus::Draft->value:
      {
        $object->label = 'Draft - в процес на попълване и не се предлага';
        $object->short = 'Draft';
        $object->color = 'secondary';
        break;
      }
      case ProductUsageStatus::ListedOnline->value:
      {
        $object->label = 'Предлага се в онлайн магазина и платформите';
        $object->short = 'Активен';
        $object->color = 'success';
        break;
      }
      case ProductUsageStatus::InternalUse->value:
      {
        $object->label = 'Фирмено ползване (на склад, не се вижда в платформите)';
        $object->short = 'Фирмено ползване';
        $object->color = 'danger';
        break;
      }
    }

    return $object;
  }

  public static function productNonSyncStatus(ProductNonSyncStatus $reason): stdClass
  {
    $object = new stdClass();

    switch ($reason->value) {
      case ProductNonSyncStatus::SkipQuantity->value:
      {
        $object->label = 'Не синхронизирай количество';
        break;
      }
      case ProductNonSyncStatus::SkipPrice->value:
      {
        $object->label = 'Не синхронизирай цена';
        break;
      }
      case ProductNonSyncStatus::SkipBoth->value:
      {
        $object->label = 'Не синхронизирай цена и количество';
        break;
      }
    }

    return $object;
  }

  public static function productSource(ProductSource $source): stdClass
  {
    $object = new stdClass();

    switch ($source->value) {
      case ProductSource::Unknown->value:
      {
        $object->title = 'Unknown';
        $object->icon = 'fa-circle-question';
        break;
      }
      case ProductSource::FeedsImportItems->value:
      {
        $object->title = 'Feeds';
        $object->icon = 'fa-rss';
        break;
      }
      case ProductSource::Products->value:
      {
        $object->title = 'Продукти';
        $object->icon = 'fa-box';
        break;
      }
      case ProductSource::ProductsImport->value:
      {
        $object->title = 'Импорт на продукти';
        $object->icon = 'fa-file-import';
        break;
      }
      case ProductSource::StorageEntries->value:
      {
        $object->title = 'Заприхождаване';
        $object->icon = 'fa-inbox';
        break;
      }
    }

    return $object;
  }

  public static function storageExit(StorageItemExitType $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case StorageItemExitType::Invoice->value:
      {
        $object->title = 'Фактура';
        $object->icon = 'fa-file-lines';
        break;
      }
      case StorageItemExitType::WriteOffProtocol->value:
      {
        $object->title = 'Протокол за изписване/брак';
        $object->icon = 'fa-wine-glass-crack';
        break;
      }
      case StorageItemExitType::IncomeCreditMemo->value:
      {
        $object->title = 'Входящо кредитно известие';
        $object->icon = 'fa-file-circle-minus';
        break;
      }
    }

    return $object;
  }
}
