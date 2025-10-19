<?php

namespace App\Enums;

enum OfferStatus: string
{
  case Draft = 'draft';
  case Sent = 'sent';
  case Accepted = 'accepted';
  case Declined = 'declined';
}
