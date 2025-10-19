<?php

namespace App\Enums;

enum ProductNonSyncStatus: string
{
  case SkipQuantity = 'skipQuantity';
  case SkipPrice = 'skipPrice';
  case SkipBoth = 'skipBoth';
}
