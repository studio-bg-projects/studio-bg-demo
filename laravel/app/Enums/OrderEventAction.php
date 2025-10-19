<?php

namespace App\Enums;

enum OrderEventAction: string
{
  case Create = 'create';
  case SetStatus = 'setStatus';
  case AddDocument = 'addDocument';
  case SentMail = 'sentMail';
}
