<?php

namespace App\Enums;

enum ProductSource: string
{
  case Unknown = 'unknown';
  case FeedsImportItems = 'feedsImportItems';
  case Products = 'products';
  case ProductsImport = 'productsImport';
  case StorageEntries = 'storageEntries';
}
