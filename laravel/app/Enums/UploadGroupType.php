<?php

namespace App\Enums;

enum UploadGroupType: string
{
  case Categories = 'categories';
  case Documents = 'documents';
  case Manufacturers = 'manufacturers';
  case ProductDownloads = 'productDownloads';
  case Products = 'products';
  case SalesRepresentatives = 'salesRepresentatives';
  case Banners = 'banners';
  case Demo = 'demo';
  case StorageEntriesIncomeInvoices = 'storageEntriesIncomeInvoices';
  case IncomeCreditMemo = 'incomeCreditMemo';
}

// Check laravel/app/Services/UploadsService.php
