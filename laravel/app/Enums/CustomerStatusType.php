<?php

namespace App\Enums;

enum CustomerStatusType: string
{
  case Customer = 'customer';
  case Supplier = 'supplier';
  case CustomerSupplier = 'customerSupplier';
  case WaitingApproval = 'waitingApproval';
  case Archived = 'archived';
}
