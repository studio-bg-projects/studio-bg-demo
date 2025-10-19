<?php

namespace App\Services\Jobs;

use App\Models\Document;
use App\Models\Income;
use App\Models\IncomesAllocation;
use App\Services\MapService;

class SyncPaymentsJob extends BaseSyncJob
{
  public function run(): void
  {
    $this->syncIncomes();
    $this->syncIncomesAllocations();
    $this->syncDocuments();
    $this->out('All good :)');
  }

  public function syncIncomes(): void
  {
    $incomes = Income::all();

    $shopIncomes = $this->dictionarizeShopRecords('extend_income', 'extend_income_id');
    $setInactive = $shopIncomes; // Collect records to be inactive

    /* @var $incomes Income[] */
    foreach ($incomes as $income) {
      // Add new record
      if (!isset($shopIncomes[$income->id])) {
        $this->out(sprintf('Add income %s', $income->id));

        $this->shopConn()->table(self::PREFIX . 'extend_income')->insert([
          'extend_income_id' => $income->id,
          'customer_id' => $income->customerId,
          'payment_date' => $income->paymentDate,
          'paid_amount' => $income->paidAmount,
          'notes_public' => $income->notesPublic,
        ]);
        $shopIncomes[$income->id] = $this->shopConn()->table(self::PREFIX . 'extend_income')
          ->where('extend_income_id', $income->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$income->id])) unset($setInactive[$income->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Compare
      if ($income->customerId !== $shopIncomes[$income->id]->customer_id) $updates['customer_id'] = $income->customerId;
      if ($income->paymentDate !== $shopIncomes[$income->id]->payment_date) $updates['payment_date'] = $income->paymentDate;
      if ((double)$income->paidAmount !== (double)$shopIncomes[$income->id]->paid_amount) $updates['paid_amount'] = $income->paidAmount;
      if ($income->notesPublic !== $shopIncomes[$income->id]->notes_public) $updates['notes_public'] = $income->notesPublic;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update income %s with differences %s', $income->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'extend_income')
          ->where('extend_income_id', $income->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopIncome) {
      $this->out(sprintf('Delete inactive income %s (not exists in ERP)', $shopIncome->extend_income_id));

      $this->shopConn()->table(self::PREFIX . 'extend_income')
        ->where('extend_income_id', $shopIncome->extend_income_id)
        ->delete();
    }
  }

  public function syncIncomesAllocations(): void
  {
    $incomesAllocations = IncomesAllocation::all();

    $shopIncomesAllocations = $this->dictionarizeShopRecords('extend_income_allocation', 'extend_income_allocation_id');
    $setInactive = $shopIncomesAllocations; // Collect records to be inactive

    /* @var $incomesAllocations IncomesAllocation[] */
    foreach ($incomesAllocations as $incomesAllocation) {
      // Add new record
      if (!isset($shopIncomesAllocations[$incomesAllocation->id])) {
        $this->out(sprintf('Add incomes allocation %s', $incomesAllocation->id));

        $this->shopConn()->table(self::PREFIX . 'extend_income_allocation')->insert([
          'extend_income_allocation_id' => $incomesAllocation->id,
          'income_id' => $incomesAllocation->incomeId,
          'document_id' => $incomesAllocation->documentId,
          'order_id' => $incomesAllocation->orderId,
          'description' => $incomesAllocation->description,
          'allocated_amount' => $incomesAllocation->allocatedAmount,
        ]);
        $shopIncomesAllocations[$incomesAllocation->id] = $this->shopConn()->table(self::PREFIX . 'extend_income_allocation')
          ->where('extend_income_allocation_id', $incomesAllocation->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$incomesAllocation->id])) unset($setInactive[$incomesAllocation->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Compare
      if ($incomesAllocation->incomeId !== $shopIncomesAllocations[$incomesAllocation->id]->income_id) $updates['income_id'] = $incomesAllocation->incomeId;
      if ($incomesAllocation->documentId !== $shopIncomesAllocations[$incomesAllocation->id]->document_id) $updates['document_id'] = $incomesAllocation->documentId;
      if ($incomesAllocation->orderId !== $shopIncomesAllocations[$incomesAllocation->id]->order_id) $updates['order_id'] = $incomesAllocation->orderId;
      if ($incomesAllocation->description !== $shopIncomesAllocations[$incomesAllocation->id]->description) $updates['description'] = $incomesAllocation->description;
      if ((double)$incomesAllocation->allocatedAmount !== (double)$shopIncomesAllocations[$incomesAllocation->id]->allocated_amount) $updates['allocated_amount'] = $incomesAllocation->allocatedAmount;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update incomes allocation %s with differences %s', $incomesAllocation->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'extend_income_allocation')
          ->where('extend_income_allocation_id', $incomesAllocation->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopIncomesAllocation) {
      $this->out(sprintf('Delete inactive incomes allocation %s (not exists in ERP)', $shopIncomesAllocation->extend_income_allocation_id));

      $this->shopConn()->table(self::PREFIX . 'extend_income_allocation')
        ->where('extend_income_allocation_id', $shopIncomesAllocation->extend_income_allocation_id)
        ->delete();
    }
  }

  public function syncDocuments(): void
  {
    $documents = Document::all();

    $shopDocuments = $this->dictionarizeShopRecords('extend_document', 'extend_document_id');
    $setInactive = $shopDocuments; // Collect records to be inactive

    /* @var $documents Document[] */
    foreach ($documents as $document) {
      // Add new record
      if (!isset($shopDocuments[$document->id])) {
        $this->out(sprintf('Add document %s', $document->id));

        $data = [
          'extend_document_id' => $document->id,
          'unique_number' => $document->documentNumber,
          'type' => $document->type,
          'customer_id' => $document->customerId ?? null,
          'file' => null,
          'is_payable' => null,
          'issue_date' => $document->issueDate->format('Y-m-d'),
          'due_date' => $document->dueDate->format('Y-m-d'),
          'total_amount' => $document->totalAmount,
          'paid_amount' => $document->paidAmount,
          'left_amount' => $document->leftAmount,
          'sales_representative_id' => $document->salesRepresentativeId,
        ];

        foreach (self::$languages as $langId => $langName) {
          $data['title_' . $langId] = MapService::documentTypes($document->type)->{'label' . $langName};
        }

        $this->shopConn()->table(self::PREFIX . 'extend_document')->insert($data);

        $shopDocuments[$document->id] = $this->shopConn()->table(self::PREFIX . 'extend_document')
          ->where('extend_document_id', $document->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$document->id])) unset($setInactive[$document->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Manufacturer compare
      if ((string)$document->documentNumber !== (string)$shopDocuments[$document->id]->unique_number) $updates['unique_number'] = (string)$document->documentNumber;
      if ((string)$document->type->value !== (string)$shopDocuments[$document->id]->type) $updates['type'] = $document->type->value;
      if ($document->customerId !== $shopDocuments[$document->id]->customer_id) $updates['customer_id'] = $document->customerId ?? null;
      if ((int)MapService::documentTypes($document->type)->isPayable !== (int)$shopDocuments[$document->id]->is_payable) $updates['is_payable'] = (int)MapService::documentTypes($document->type)->isPayable;
      if ($document->issueDate->format('Y-m-d') !== $shopDocuments[$document->id]->issue_date) $updates['issue_date'] = $document->issueDate->format('Y-m-d');
      if ($document->dueDate->format('Y-m-d') !== $shopDocuments[$document->id]->due_date) $updates['due_date'] = $document->dueDate->format('Y-m-d');
      if ((double)$document->totalAmount !== (double)$shopDocuments[$document->id]->total_amount) $updates['total_amount'] = $document->totalAmount;
      if ((double)$document->paidAmount !== (double)$shopDocuments[$document->id]->paid_amount) $updates['paid_amount'] = $document->paidAmount;
      if ((double)$document->leftAmount !== (double)$shopDocuments[$document->id]->left_amount) $updates['left_amount'] = $document->leftAmount;
      if ($document->salesRepresentativeId !== $shopDocuments[$document->id]->sales_representative_id) $updates['sales_representative_id'] = $document->salesRepresentativeId;

      foreach (self::$languages as $langId => $langName) {
        if (MapService::documentTypes($document->type)->{'label' . $langName} !== $shopDocuments[$document->id]->{'title_' . $langId}) $updates['title_' . $langId] = MapService::documentTypes($document->type)->{'label' . $langName};
      }

      // File compare
      $filePath = $document->uploads->first() ? 'erp/' . $document->uploads->first()->urls->path : '';
      if ($filePath !== (string)$shopDocuments[$document->id]->file) $updates['file'] = $filePath;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update document %s with differences %s', $document->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'extend_document')
          ->where('extend_document_id', $document->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopDocument) {
      $this->out(sprintf('Delete inactive document %s (not exists in ERP)', $shopDocument->extend_document_id));

      $this->shopConn()->table(self::PREFIX . 'extend_document')
        ->where('extend_document_id', $shopDocument->extend_document_id)
        ->delete();
    }
  }
}
