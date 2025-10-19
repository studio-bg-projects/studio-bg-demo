<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StorageItem;
use App\Models\StorageExitsItem;
use App\Services\MapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StorageReportController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    return redirect('/erp/storage-report/products');
  }

  public function products()
  {
    $filterProducts = request()->input('filter.products', 'withItems');
    $period = request()->input('filter.period');
    $direction = request()->input('direction', 'all');
    $noPagination = request()->boolean('filter.noPagination');
    $isExport = request('export') === 'excel';

    $productsQuery = Product::query();

    $itemFilter = function ($query) use ($period, $direction) {
      if ($direction === 'out') {
        $query->where('isExited', true);

        if ($period) {
          $this->applyRangeFilter($query, $period, 'exitDate');
        }
      } elseif ($direction === 'in') {
        if ($period) {
          $this->applyRangeFilter($query, $period, 'invoiceDate');
        }
      } else {
        if ($period) {
          $query->where(function ($q) use ($period) {
            $this->applyRangeFilter($q, $period, 'invoiceDate');
          })->orWhere(function ($q) use ($period) {
            $q->where('isExited', true);
            $this->applyRangeFilter($q, $period, 'exitDate');
          });
        }
      }

      return $query->orderBy('id');
    };

    $productsQuery->with([
      'storageItems' => function ($query) use ($itemFilter) {
        $itemFilter($query);
        $query->with([
          'exit.outcomeInvoice',
          'exit.writeOffProtocol',
          'exit.incomeCreditMemo.incomeInvoice',
          'storageEntriesIncomeInvoice',
          'outcomeCreditMemo',
        ]);
      },
      'uploads'
    ]);

    if ($filterProducts === 'withItems') {
      $productsQuery->has('storageItems');
    } elseif ($filterProducts === 'withoutItems') {
      $productsQuery->doesntHave('storageItems');
    }

    $productsQuery = $this->applyQFilter($productsQuery, ['nameBg', 'mpn', 'ean'], 'filter.q');

    if ($period || $direction !== 'all') {
      $productsQuery->whereHas('storageItems', $itemFilter);
    }

    $formatItem = function (StorageItem $item): StorageItem {
      $entryParts = [];

      if ($item->invoiceDate) {
        $entryParts[] = $item->invoiceDate->format('Y-m-d');
      }

      if ($item->storageEntriesIncomeInvoiceId) {
        $docNumber = $item->storageEntriesIncomeInvoice?->documentNumber;
        $entryParts[] = $docNumber ? 'чрез Ф-ра: #' . $docNumber : 'чрез Ф-ра';
      } elseif ($item->outcomeCreditMemoDocumentId) {
        $docNumber = $item->outcomeCreditMemo?->documentNumber;
        $entryParts[] = $docNumber ? 'чрез КИ: #' . $docNumber : 'чрез КИ';
      }

      $item->formattedEntryInfo = $entryParts ? implode(' ', $entryParts) : '-';

      if ($item->isExited) {
        $exitParts = [];

        if ($item->exitDate) {
          $exitParts[] = $item->exitDate->format('Y-m-d');
        }

        $exit = $item->exit;

        if ($exit) {
          if ($exit->type) {
            $title = MapService::storageExit($exit->type)->title ?? null;
            if ($title) {
              $exitParts[] = $title;
            }
          }

          if ($exit->outcomeInvoiceId) {
            $docNumber = $exit->outcomeInvoice?->documentNumber;
            if ($docNumber) {
              $exitParts[] = 'рез #' . $docNumber;
            }
          } elseif ($exit->writeOffProtocolId) {
            $docNumber = $exit->writeOffProtocol?->id;
            if ($docNumber) {
              $exitParts[] = 'чрез #' . $docNumber;
            }
          } elseif ($exit->incomeCreditMemoId) {
            $docNumber = $exit->incomeCreditMemo?->incomeInvoice?->documentNumber;
            if ($docNumber) {
              $exitParts[] = 'чрез #' . $docNumber;
            }
          }
        }

        $exitParts = array_filter($exitParts, fn($part) => $part !== null && $part !== '');
        $item->formattedExitInfo = $exitParts ? implode(' ', $exitParts) : '-';
      } else {
        $item->formattedExitInfo = '-';
      }

      $item->formattedPurchasePrice = price($item->purchasePrice);

      $exit = $item->exit;

      $item->formattedSellPrice = '-';
      $item->formattedOriginalSellPrice = '-';
      $item->hasDifferentSellPrices = false;

      if ($exit) {
        $sellPrice = price($exit->sellPrice);
        $item->formattedSellPrice = $sellPrice;

        if ($exit->originalPrice !== null) {
          $item->formattedOriginalSellPrice = price($exit->originalPrice);

          if ($exit->originalPrice != $exit->sellPrice) {
            $item->hasDifferentSellPrices = true;
          }
        } else {
          $item->formattedOriginalSellPrice = $sellPrice;
        }
      }

      return $item;
    };

    $applyFormatting = function (Collection $products) use ($formatItem) {
      return $products->map(function (Product $product) use ($formatItem) {
        $formattedItems = $product->storageItems->map($formatItem);

        $product->setRelation('storageItems', $formattedItems);

        return $product;
      });
    };

    if ($isExport) {
      $products = $applyFormatting($productsQuery->get());

      return $this->exportProductsToExcel($products);
    }

    $products = $noPagination
      ? $productsQuery->get()
      : $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    if ($products instanceof LengthAwarePaginator) {
      $products->setCollection($applyFormatting($products->getCollection()));
    } else {
      $products = $applyFormatting($products);
    }

    return view('erp.storage-report.products', [
      'products' => $products,
      'noPagination' => $noPagination,
    ]);
  }

  private function exportProductsToExcel(Collection $products)
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Продукти');

    $headers = [
      'A' => 'Продукт',
      'B' => 'MPN',
      'C' => 'EAN',
      'D' => 'ID на артикул',
      'E' => 'Сериен номер',
      'F' => 'Заприхождаване',
      'G' => 'Изписване',
      'H' => 'Цена на закупуване',
      'K' => 'Бележка',
    ];

    foreach ($headers as $column => $title) {
      $sheet->setCellValue("{$column}1", $title);
    }

    $sheet->setCellValue('I1', 'Цена на продажба');
    $sheet->mergeCells('I1:J1');

    $sheet->getStyle('A1:K1')->getFont()->setBold(true);

    $row = 2;

    foreach ($products as $product) {
      if ($product->storageItems->isEmpty()) {
        $this->fillProductRowWithoutItems($sheet, $row, $product);
        $row++;
        continue;
      }

      foreach ($product->storageItems as $item) {
        $sheet->setCellValue("A{$row}", $product->nameBg);
        $sheet->setCellValueExplicit("B{$row}", (string)($product->mpn ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("C{$row}", (string)($product->ean ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValue("D{$row}", $item->id);
        $sheet->setCellValueExplicit("E{$row}", (string)($item->serialNumber ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValue("F{$row}", $item->formattedEntryInfo ?? '-');
        $sheet->setCellValue("G{$row}", $item->formattedExitInfo ?? '-');
        $sheet->setCellValue("H{$row}", $item->formattedPurchasePrice ?? '-');

        if ($item->hasDifferentSellPrices) {
          $sheet->setCellValue("I{$row}", $item->formattedOriginalSellPrice ?? '-');
          $sheet->setCellValue("J{$row}", $item->formattedSellPrice ?? '-');
        } else {
          $sheet->mergeCells("I{$row}:J{$row}");
          $sheet->setCellValue("I{$row}", $item->formattedSellPrice ?? '-');
        }

        $sheet->setCellValue("K{$row}", $item->note ?? '');
        $row++;
      }
    }

    foreach (range('A', 'K') as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'storage-report-products-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

    return response()->streamDownload(function () use ($writer) {
      $writer->save('php://output');
    }, $fileName, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
  }

  private function fillProductRowWithoutItems(Worksheet $sheet, int $row, Product $product): void
  {
    $sheet->setCellValue("A{$row}", $product->nameBg);
    $sheet->setCellValueExplicit("B{$row}", (string)($product->mpn ?? ''), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit("C{$row}", (string)($product->ean ?? ''), DataType::TYPE_STRING);
    $sheet->setCellValue("D{$row}", '-');
    $sheet->setCellValue("E{$row}", '-');
    $sheet->setCellValue("F{$row}", '-');
    $sheet->setCellValue("G{$row}", '-');
    $sheet->setCellValue("H{$row}", '-');
    $sheet->mergeCells("I{$row}:J{$row}");
    $sheet->setCellValue("I{$row}", '-');
    $sheet->setCellValue("K{$row}", '-');
  }

  private function exportInventoryToExcel(Collection $products, Carbon $inventoryDate)
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Инвентаризация');

    $sheet->setCellValue('A1', 'Справка - Инвентаризация');
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $sheet->setCellValue('A2', 'Дата: ' . $inventoryDate->format('d.m.Y') . ' г.');
    $sheet->mergeCells('A2:D2');

    $headers = [
      'A3' => 'Продукт',
      'B3' => 'MPN',
      'C3' => 'EAN',
      'D3' => 'Налично количество',
    ];

    foreach ($headers as $cell => $title) {
      $sheet->setCellValue($cell, $title);
    }

    $sheet->getStyle('A3:D3')->getFont()->setBold(true);

    $row = 4;

    foreach ($products as $product) {
      $summary = $product->inventorySummary ?? [];

      $sheet->setCellValue("A{$row}", $summary['name'] ?? '');
      $sheet->setCellValueExplicit("B{$row}", (string)($summary['mpn'] ?? ''), DataType::TYPE_STRING);
      $sheet->setCellValueExplicit("C{$row}", (string)($summary['ean'] ?? ''), DataType::TYPE_STRING);
      $sheet->setCellValue("D{$row}", $summary['count'] ?? 0);
      $row++;
    }

    foreach (range('A', 'D') as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'storage-report-inventory-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

    return response()->streamDownload(function () use ($writer) {
      $writer->save('php://output');
    }, $fileName, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
  }

  public function nra()
  {
    $period = request()->input('filter.period');
    $isExport = request('export') === 'excel';

    if (!$period) {
      $endDate = Carbon::today();
      $startDate = $endDate->copy()->subMonth();
      $period = $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d');

      $filters = (array)request()->input('filter', []);
      $filters['period'] = $period;
      request()->merge(['filter' => $filters]);
    }

    $vatRate = dbConfig('default:vatRate') ?? 20;

    $entriesQuery = StorageItem::query()
      ->with(['product', 'storageEntriesIncomeInvoice.supplier']);

    if ($period) {
      $this->applyRangeFilter($entriesQuery, $period, 'invoiceDate');
    }

    $formatDate = function ($value) {
      if (!$value) {
        return null;
      }

      if ($value instanceof Carbon) {
        return $value->format('Y-m-d');
      }

      try {
        return Carbon::parse($value)->format('Y-m-d');
      } catch (\Throwable $exception) {
        return null;
      }
    };

    $formatEntry = function (StorageItem $item) use ($vatRate, $formatDate) {
      $supplier = $item->storageEntriesIncomeInvoice?->supplier;
      $taxBase = (double)($item->purchasePrice ?? 0);
      $tax = $taxBase * $vatRate / 100;
      $documentDate = $formatDate($item->invoiceDate);

      $documentNumber = '-';
      $documentType = 'Неизвестен';
      if ($item->storageEntriesIncomeInvoiceId) {
        $documentNumber = $item->invoiceNumber;
        $documentType = 'Фактура (Входяща)';
      } elseif ($item->outcomeCreditMemoDocumentId) {
        $documentNumber = $item->outcomeCreditMemo->documentNumber;
        $documentType = 'Кредитно известие (Изходящо)';
      }

      return [
        'productId' => $item->productId,
        'productName' => $item->product?->nameBg ?? 'Неизвестен продукт',
        'productMpn' => $item->product?->mpn,
        'productEan' => $item->product?->ean,
        'storageItemId' => $item->id,
        'itemName' => $item->product?->nameBg ?? 'Неизвестен артикул',
        'serialNumber' => $item->serialNumber,
        'direction' => 'in',
        'directionLabel' => 'Заприхождаване',
        'documentType' => $documentType,
        'documentNumber' => $documentNumber,
        'documentDate' => $documentDate,
        'partnerId' => $supplier?->companyId,
        'partnerName' => $supplier?->companyName,
        'taxBase' => $taxBase,
        'formattedTaxBase' => price($taxBase),
        'tax' => $tax,
        'formattedTax' => price($tax),
        'sortDate' => $documentDate ?? '9999-12-31',
      ];
    };

    $entries = $entriesQuery->get()->map($formatEntry);

    $exitsQuery = StorageExitsItem::query()
      ->with([
        'storageItem.product',
        'outcomeInvoice.customer',
        'writeOffProtocol',
        'incomeCreditMemo.incomeInvoice.supplier',
      ]);

    if ($period) {
      $exitsQuery->where(function ($query) use ($period) {
        $query->whereHas('outcomeInvoice', function ($q) use ($period) {
          $this->applyRangeFilter($q, $period, 'issueDate');
        })->orWhereHas('writeOffProtocol', function ($q) use ($period) {
          $this->applyRangeFilter($q, $period, 'date');
        })->orWhereHas('incomeCreditMemo', function ($q) use ($period) {
          $this->applyRangeFilter($q, $period, 'date');
        });
      });
    }

    $formatExit = function (StorageExitsItem $exit) use ($vatRate, $formatDate) {
      $product = $exit->storageItem?->product;
      $storageItem = $exit->storageItem;
      $exitType = MapService::storageExit($exit->type);
      $docType = $exitType->title ?? 'Изписване';
      $docNumber = null;
      $docDate = null;
      $partnerId = null;
      $partnerName = null;
      $rate = $vatRate;

      if ($exit->outcomeInvoice) {
        $docNumber = $exit->outcomeInvoice->documentNumber;
        $docDate = $formatDate($exit->outcomeInvoice->issueDate);
        $partnerId = $exit->outcomeInvoice->customer?->companyId;
        $partnerName = $exit->outcomeInvoice->customer?->companyName;
        $rate = $exit->outcomeInvoice->vatRate ?? $vatRate;
      } elseif ($exit->incomeCreditMemo) {
        $docNumber = $exit->incomeCreditMemo->incomeInvoice?->documentNumber;
        $docDate = $formatDate($exit->incomeCreditMemo->date);
        $partnerId = $exit->incomeCreditMemo->incomeInvoice?->supplier?->companyId;
        $partnerName = $exit->incomeCreditMemo->incomeInvoice?->supplier?->companyName;
      } elseif ($exit->writeOffProtocol) {
        $docNumber = $exit->writeOffProtocol->id;
        $docDate = $formatDate($exit->writeOffProtocol->date);
        $rate = 0;
      }

      $taxBase = (double)($exit->sellPrice ?? $exit->originalPrice ?? 0);
      $tax = $taxBase * $rate / 100;
      $documentDate = $docDate;

      return [
        'productId' => $product?->id ?? $storageItem?->productId,
        'productName' => $product?->nameBg ?? 'Неизвестен продукт',
        'productMpn' => $product?->mpn,
        'productEan' => $product?->ean,
        'storageItemId' => $storageItem?->id,
        'itemName' => $product?->nameBg ?? 'Неизвестен артикул',
        'serialNumber' => $storageItem?->serialNumber,
        'direction' => 'out',
        'directionLabel' => 'Изписване',
        'documentType' => $docType,
        'documentNumber' => $docNumber,
        'documentDate' => $documentDate,
        'partnerId' => $partnerId,
        'partnerName' => $partnerName,
        'taxBase' => $taxBase,
        'formattedTaxBase' => price($taxBase),
        'tax' => $tax,
        'formattedTax' => price($tax),
        'sortDate' => $documentDate ?? '9999-12-31',
      ];
    };

    $exits = $exitsQuery->get()->map($formatExit);

    $items = $entries->merge($exits)->sortBy('sortDate')->values();

    $itemsByProduct = $items->groupBy('productId')->map(function (Collection $productItems) {
      $firstItem = $productItems->first();

      $detailsParts = array_filter([
        !empty($firstItem['productId']) ? 'ID: ' . $firstItem['productId'] : null,
        !empty($firstItem['productMpn']) ? 'SKU: ' . $firstItem['productMpn'] : null,
        !empty($firstItem['productEan']) ? 'EAN: ' . $firstItem['productEan'] : null,
      ]);

      return [
        'product' => [
          'id' => $firstItem['productId'] ?? null,
          'name' => $firstItem['productName'] ?? 'Неизвестен продукт',
          'details' => $detailsParts ? implode(' | ', $detailsParts) : null,
        ],
        'items' => $productItems->map(function (array $item) {
          $item['itemLabel'] = !empty($item['storageItemId']) ? 'Артикул №' . $item['storageItemId'] : null;

          return $item;
        })->values(),
      ];
    })->values();

    if ($isExport) {
      return $this->exportNraToExcel($itemsByProduct);
    }

    return view('erp.storage-report.nra', [
      'items' => $itemsByProduct,
      'period' => $period,
    ]);
  }

  private function exportNraToExcel(Collection $items)
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('НАП');

    $headers = [
      'A' => 'Артикул',
      'B' => 'Сериен номер',
      'C' => 'Посока',
      'D' => 'Тип документ',
      'E' => 'Документ',
      'F' => 'Дата',
      'G' => 'ИД номер',
      'H' => 'Контрагент',
      'I' => 'Данъчна основа',
      'J' => 'ДДС',
    ];

    foreach ($headers as $column => $title) {
      $sheet->setCellValue("{$column}1", $title);
    }

    $sheet->getStyle('A1:J1')->getFont()->setBold(true);

    $row = 2;

    foreach ($items as $productData) {
      $product = $productData['product'] ?? [];
      $productTitle = $product['name'] ?? 'Неизвестен продукт';

      if (!empty($product['details'])) {
        $productTitle .= " - " . $product['details'];
      }

      $sheet->setCellValue("A{$row}", $productTitle);
      $sheet->mergeCells("A{$row}:J{$row}");
      $sheet->getStyle("A{$row}")->getFont()->setBold(true);
      $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
      $row++;

      foreach ($productData['items'] as $item) {
        $itemName = $item['itemName'] ?? 'Неизвестен артикул';

        if (!empty($item['itemLabel'])) {
          $itemName .= " - " . $item['itemLabel'];
        }

        $sheet->setCellValue("A{$row}", $itemName);
        $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);
        $sheet->setCellValueExplicit("B{$row}", (string)($item['serialNumber'] ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValue("C{$row}", $item['directionLabel'] ?? '');
        $sheet->setCellValue("D{$row}", $item['documentType'] ?? '');
        $sheet->setCellValueExplicit("E{$row}", (string)($item['documentNumber'] ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValue("F{$row}", $item['documentDate'] ?? '');
        $sheet->setCellValueExplicit("G{$row}", (string)($item['partnerId'] ?? ''), DataType::TYPE_STRING);
        $sheet->setCellValue("H{$row}", $item['partnerName'] ?? '');
        $sheet->setCellValue("I{$row}", $item['formattedTaxBase'] ?? '');
        $sheet->setCellValue("J{$row}", $item['formattedTax'] ?? '');
        $row++;
      }
    }

    foreach (range('A', 'J') as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'storage-report-nra-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

    return response()->streamDownload(function () use ($writer) {
      $writer->save('php://output');
    }, $fileName, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
  }

  public function inventory()
  {
    $dateInput = request()->input('filter.date');

    if (!$dateInput || !Carbon::hasFormatWithModifiers($dateInput, 'Y-m-d')) {
      $inventoryDate = now()->startOfDay();
    } else {
      $inventoryDate = Carbon::createFromFormat('Y-m-d', $dateInput)->startOfDay();
    }

    $inventoryEnd = $inventoryDate->copy()->endOfDay();
    $isExport = request('export') === 'excel';

    $productsQuery = Product::query()
      ->with(['uploads' => function ($query) {
        $query->orderBy('sortOrder');
      }]);

    $productsQuery = $this->applyQFilter($productsQuery, ['nameBg', 'mpn', 'ean']);

    $productsQuery->withCount(['storageItems as inventoryCount' => function ($query) use ($inventoryEnd) {
      $query->where('invoiceDate', '<=', $inventoryEnd)
        ->where(function ($q) use ($inventoryEnd) {
          $q->whereNull('exitDate')->orWhere('exitDate', '>', $inventoryEnd);
        });
    }])->having('inventoryCount', '>', 0);

    $productsQuery->orderBy('nameBg');

    $formatProduct = function (Product $product): Product {
      $mpn = $product->mpn ?? '';
      $ean = $product->ean ?? '';
      $count = (int)($product->inventoryCount ?? 0);

      $product->inventorySummary = [
        'id' => $product->id,
        'name' => $product->nameBg ?? 'Неименуван продукт',
        'mpn' => $mpn,
        'ean' => $ean,
        'details' => 'MPN: ' . $mpn . ' | EAN: ' . $ean,
        'count' => $count,
        'countLabel' => 'Налично: ' . $count . ' бр.',
      ];

      return $product;
    };

    $applyFormatting = function (Collection $products) use ($formatProduct) {
      return $products->map($formatProduct);
    };

    if ($isExport) {
      $products = $applyFormatting($productsQuery->get());

      return $this->exportInventoryToExcel($products, $inventoryDate);
    }

    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    if ($products instanceof LengthAwarePaginator) {
      $products->setCollection($applyFormatting($products->getCollection()));
    }

    return view('erp.storage-report.inventory', [
      'products' => $products,
      'inventoryDate' => $inventoryDate,
      'selectedDate' => $inventoryDate->format('Y-m-d'),
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
}
