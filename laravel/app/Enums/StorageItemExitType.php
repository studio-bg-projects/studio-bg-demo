<?php

namespace App\Enums;

enum StorageItemExitType: string
{
  case Invoice = 'invoice';
  case WriteOffProtocol = 'writeOffProtocol';
  case IncomeCreditMemo = 'incomeCreditMemo';
}
