<?php

namespace App\Http\Controllers\Erp;

use App\Enums\StorageItemExitType;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\IncomeCreditMemo;
use App\Models\StorageEntriesIncomeInvoice;
use App\Models\StorageExitsItem;
use App\Models\StorageItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class StorageEntriesIncomeCreditMemosController extends Controller
{
  use FilterAndSort;

  public function index(int $documentId)
  {
    /* @var $document StorageEntriesIncomeInvoice */
    $document = StorageEntriesIncomeInvoice::where('id', $documentId)->firstOrFail();

    $notesQuery = IncomeCreditMemo::query();
    $notesQuery->where('incomeInvoiceId', $documentId);
    $notesQuery->orderBy('date', 'DESC');
    $notes = $notesQuery->get();

    return view('erp.storage-entries-income-credit-memos.index', [
      'document' => $document,
      'notes' => $notes,
    ]);
  }

  public function create(int $documentId, Request $request)
  {
    /* @var $document StorageEntriesIncomeInvoice */
    $document = StorageEntriesIncomeInvoice::where('id', $documentId)->firstOrFail();

    $errors = session()->get('errors') ?? new MessageBag();
    $creditMemo = new IncomeCreditMemo();
    $creditMemo->incomeInvoiceId = $document->id;

    if ($request->isMethod('post')) {
      $creditMemo->fill($request->all());

      $validator = Validator::make($request->all(), [
        'date' => ['required', 'date_format:Y-m-d'],
        'note' => ['nullable', 'string'],
      ]);

      $errors->merge($validator->errors());

      $totalCreditValue = 0;
      foreach ($request->input('items', []) as $itemId => $row) {
        /* @var $item StorageItem */
        $item = StorageItem::find($itemId);

        if (!$item) {
          continue;
        }

        if ($item->priceCorrectionIncomeCreditMemoId && $item->priceCorrectionIncomeCreditMemoId !== $creditMemo->id) {
          continue;
        }

        $creditValue = doubleval($row['creditValue'] ?? 0);
        $totalCreditValue += $creditValue;

        if ($creditValue < 0 || $creditValue > $item->originalPrice) {
          $errors->add('items.' . $itemId . '.creditValue', 'Невалидна кредитна стойност.');
        }
      }

      if (!$totalCreditValue) {
        $errors->add('items', 'Трябва да нанесете някаква корекция.');
      }

      if ($errors->isEmpty()) {
        $creditMemo->save();

        foreach ($request->input('items', []) as $itemId => $row) {
          // Edit item
          /* @var $item StorageItem */
          $item = StorageItem::find($itemId);

          if (!$item) {
            continue;
          }

          if ($item->priceCorrectionIncomeCreditMemoId && $item->priceCorrectionIncomeCreditMemoId !== $creditMemo->id) {
            continue;
          }

          if ($item->isExited) {
            continue;
          }

          $creditValue = doubleval($row['creditValue'] ?? 0);

          if (!$creditValue) {
            continue;
          }

          $item->purchasePrice = $item->originalPrice - $creditValue;
          $item->isExited = $creditValue == $item->originalPrice;
          $item->exitDate = $item->isExited ? $creditMemo->date : null;
          $item->priceCorrectionIncomeCreditMemoId = $item->isExited ? null : $creditMemo->id;
          $item->save();

          // Add exits
          if ($item->isExited) {
            $storageExitsItem = new StorageExitsItem();
            $storageExitsItem->storageItemId = $item->id;
            $storageExitsItem->incomeCreditMemoId = $creditMemo->id;
            $storageExitsItem->type = StorageItemExitType::IncomeCreditMemo;
            $storageExitsItem->save();
          } else {
            $item->addHistory('Коригиране на цена чрез входящо кредитно известие');
          }
        }

        return redirect('/erp/storage-entries/income-credit-memos/view/' . $creditMemo->id)
          ->with('success', 'Успешно създадохте ново кредитно известие.');
      }

      $request->flash();
    } else {
      $creditMemo->date = now()->format('Y-m-d');
    }

    return view('erp.storage-entries-income-credit-memos.create', [
      'document' => $document,
      'creditMemo' => $creditMemo,
      'errors' => $errors,
    ]);
  }

  public function view(int $id)
  {
    /* @var $creditMemo IncomeCreditMemo */
    $creditMemo = IncomeCreditMemo::where('id', $id)
      ->with([
        'storageItems.product',
        'storageExitsItems.storageItem.product',
      ])->firstOrFail();

    $document = $creditMemo->incomeInvoice;

    return view('erp.storage-entries-income-credit-memos.view', [
      'document' => $document,
      'creditMemo' => $creditMemo,
      'items' => $creditMemo->storageItems,
      'exitItems' => $creditMemo->storageExitsItems,
    ]);
  }
}
