<?php

namespace App\Enums;

enum DocumentType: string
{
  case Invoice = 'invoice';
  case ProformaInvoice = 'proformaInvoice';
  case OrderConfirmation = 'orderConfirmation';
  case DeliveryNote = 'deliveryNote';
  case PackingList = 'packingList';
  case OutcomeCreditMemo = 'outcomeCreditMemo';
}
