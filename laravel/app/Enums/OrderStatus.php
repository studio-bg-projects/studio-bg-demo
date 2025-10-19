<?php

namespace App\Enums;

enum OrderStatus: string
{
  case Unknown = 'unknown';
  case Pending = 'pending';
  case Processing = 'processing';
  case Shipped = 'shipped';
  case Complete = 'complete';
  case Canceled = 'canceled';
  case Denied = 'denied';
  case CanceledReversal = 'canceledReversal';
  case Failed = 'failed';
  case Refunded = 'refunded';
  case Reversed = 'reversed';
  case Chargeback = 'chargeback';
  case Expired = 'expired';
  case Processed = 'processed';
  case Voided = 'voided';
}
