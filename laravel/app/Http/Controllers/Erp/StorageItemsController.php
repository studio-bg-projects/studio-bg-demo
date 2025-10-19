<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\StorageItem;
use App\Models\StorageItemsWriteOffProtocol;
use App\Models\StorageExitsItem;
use App\Enums\StorageItemExitType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageItemsController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $itemsQuery = StorageItem::with('product');
    $itemsQuery = $this->applySort($itemsQuery);
    $itemsQuery = $this->applyFilter($itemsQuery);
    $storageItems = $itemsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.storage-items.index', [
      'storageItems' => $storageItems,
    ]);
  }

  public function view(int $itemId)
  {
    /* @var $storageItem StorageItem */
    $storageItem = StorageItem::where('id', $itemId)->firstOrFail();

    return view('erp.storage-items.view', [
      'storageItem' => $storageItem,
      'history' => $storageItem->history ?? [],
    ]);
  }

  public function writeoffProtocol(Request $request, int $itemId)
  {
    /* @var $storageItem StorageItem */
    $storageItem = StorageItem::where('id', $itemId)->firstOrFail();

    if ($storageItem->isExited) {
      return redirect('/erp/storage-items/view/' . $itemId)->withErrors('Този артикул е вече изписан');
    }

    if ($request->isMethod('post')) {
      $data = $request->validate([
        'reason' => 'required|string',
        'date' => 'required|date',
      ]);

      DB::transaction(function () use ($data, $itemId, $storageItem) {
        $protocol = StorageItemsWriteOffProtocol::create([
          'itemId' => $itemId,
          'reason' => $data['reason'],
          'date' => $data['date'],
          'documentNumber' => 'WO-' . $itemId,
        ]);

        StorageExitsItem::create([
          'storageItemId' => $itemId,
          'type' => StorageItemExitType::WriteOffProtocol,
          'writeOffProtocolId' => $protocol->id,
        ]);

        $storageItem->update([
          'isExited' => true,
          'exitDate' => $data['date'],
        ]);
      });

      return redirect('/erp/storage-items/view/' . $itemId)
        ->with('success', 'Протоколът за отписване е създаден');
    }

    return view('erp.storage-items.writeoff-protocol-form', [
      'storageItem' => $storageItem,
    ]);
  }

  public function inventoryItems(Request $request)
  {
    $data = $request->validate([
      'productId' => 'required|integer|exists:products,id',
      'date' => 'required|date_format:Y-m-d',
    ]);

    $inventoryDate = Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();
    $inventoryEnd = $inventoryDate->copy()->endOfDay();

    $items = StorageItem::query()
      ->where('productId', $data['productId'])
      ->where('invoiceDate', '<=', $inventoryEnd)
      ->where(function ($query) use ($inventoryEnd) {
        $query->whereNull('exitDate')->orWhere('exitDate', '>', $inventoryEnd);
      })
      ->with([
        'storageEntriesIncomeInvoice',
        'outcomeCreditMemo',
        'exit.outcomeInvoice',
        'exit.writeOffProtocol',
        'exit.incomeCreditMemo.incomeInvoice',
      ])
      ->orderBy('invoiceDate')
      ->orderBy('id')
      ->get();

    return view('erp.storage-report.partials.inventory.items', [
      'items' => $items,
      'inventoryDate' => $inventoryDate,
    ]);
  }

  public function writeoffProtocolPdf(Request $request, int $writeOffProtocolId)
  {
    /* @var $writeOffProtocol StorageItemsWriteOffProtocol */
    $writeOffProtocol = StorageItemsWriteOffProtocol::where('id', $writeOffProtocolId)->firstOrFail();

    $html = (string)view('erp.storage-items.writeoff-protocol-pdf', [
      'writeOffProtocol' => $writeOffProtocol,
      'documentTitle' => 'Протокол за отписване - #' . $writeOffProtocol->documentNumber,
    ]);

    try {
      $response = pdf($html);

      $fileName = 'writeoff-protocol-' . $writeOffProtocol->itemId . '.pdf';
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename="' . $fileName . '"');

      return $response;
    } catch (Exception $e) {
      abort(400, sprintf('Възникна грешка при генерирането на ПДФ: %s', $e->getMessage()));
    }
  }
}
