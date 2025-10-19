<?php

namespace App\Enums;

enum DemoStatus: string
{
  case Draft = 'draft';
  case Sent = 'sent';
  case Accepted = 'accepted';
  case Declined = 'declined';
}
