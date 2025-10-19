<?php

namespace App\Enums;

enum SpecificationValueType: string
{
  case String = 'string';
  case Boolean = 'boolean';
  case Decimal = 'decimal';
  case Number = 'number';
  case Option = 'option';
}
