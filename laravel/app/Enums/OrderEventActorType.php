<?php

namespace App\Enums;

enum OrderEventActorType: string
{
  case SystemSync = 'systemSync';
  case Operator = 'operator';
}
