<?php

namespace App\Enums;

enum StorageEntryDocumentType: string
{
  case Invoice = 'invoice';
  case CreditMemo = 'creditMemo';
}
