<?php

namespace App\Enums;

enum ProductUsageStatus: string
{
  case NotListed = 'notListed';
  case Draft = 'draft';
  case ListedOnline = 'listedOnline';
  case InternalUse = 'internalUse';
}
