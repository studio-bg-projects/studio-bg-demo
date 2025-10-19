<?php

namespace App\Http\Controllers\Erp;

use App\Enums\ProductUsageStatus;
use App\Enums\CustomerStatusType;
use App\Enums\ProductSource;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\StorageEntriesIncomeInvoice;
use App\Models\StorageEntryProduct;
use App\Models\StorageItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class StorageEntriesController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $documentsQuery = StorageEntriesIncomeInvoice::query();
    $documentsQuery = $this->applySort($documentsQuery);
    $documentsQuery = $this->applyFilter($documentsQuery);
    $documents = $documentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    $documents->load('supplier');

    return view('erp.storage-entries.index', [
      'documents' => $documents,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $document = new StorageEntriesIncomeInvoice();

    if ($request->isMethod('post')) {
      $document->fill($request->all());

      $validator = Validator::make($request->all(), [
        'documentNumber' => [
          'required',
          'string',
          'max:255',
          Rule::unique('storageEntriesIncomeInvoices')->where(
            fn($q) => $q->where('supplierId', $request->input('supplierId'))
          ),
        ],
        'documentDate' => ['required', 'date_format:Y-m-d'],
        'supplierId' => ['required', 'integer', 'exists:customers,id'],
      ]);

      $errors->merge($validator->errors());

      $items = $this->validateItems($request, $errors);

      if ($errors->isEmpty()) {
        try {
          DB::transaction(function () use ($document, $items) {
            $document->save();
            $this->syncItems($document, $items);
          });

          return redirect('/erp/storage-entries/update/' . $document->id)
            ->with('success', 'Успешно създадохте нов запис.');
        } catch (\RuntimeException $e) {
          $errors->add('products', $e->getMessage());
        }
      }
    } else {
      $document->documentDate = now()->format('Y-m-d');
    }

    $suppliers = Customer::whereIn('statusType', [
      CustomerStatusType::Supplier,
      CustomerStatusType::CustomerSupplier,
    ])->orderBy('companyName')->get();

    $products = $request->input('products', []);

    return view('erp.storage-entries.create', [
      'document' => $document,
      'errors' => $errors,
      'suppliers' => $suppliers,
      'products' => $products,
    ]);
  }

  public function update(int $documentId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    $document = StorageEntriesIncomeInvoice::where('id', $documentId)->firstOrFail();
    $products = $request->input('products', []);

    if ($request->isMethod('post')) {
      $document->fill($request->all());

      $validator = Validator::make($request->all(), [
        'documentNumber' => [
          'required',
          'string',
          'max:255',
          Rule::unique('storageEntriesIncomeInvoices')
            ->ignore($document->id)
            ->where(
              fn($q) => $q
                ->where('supplierId', $request->input('supplierId'))
            ),
        ],
        'documentDate' => ['required', 'date_format:Y-m-d'],
        'supplierId' => ['required', 'integer', 'exists:customers,id'],
      ]);

      $errors->merge($validator->errors());

      $items = $this->validateItems($request, $errors);

      if ($errors->isEmpty()) {
        try {
          DB::transaction(function () use ($document, $items) {
            $document->save();
            $this->syncItems($document, $items);
          });

          return redirect('/erp/storage-entries/update/' . $document->id)
            ->with('success', 'Успешно редактирахте записа.');
        } catch (\RuntimeException $e) {
          $errors->add('products', $e->getMessage());

          // Restore items state
          $products = [];
        }
      }
    }

    $suppliers = Customer::whereIn('statusType', [
      CustomerStatusType::Supplier,
      CustomerStatusType::CustomerSupplier,
    ])->orderBy('companyName')->get();

    if (!$products) {
      $document->load(['products' => function ($q) {
        $q->orderBy('arrangementSeq');
      }, 'products.product', 'products.items' => function ($q) {
        $q->orderBy('arrangementSeq');
      }]);

      $products = $document->products->map(function ($p) {
        return [
          'entryProductId' => $p->id,
          'name' => $p->product->nameBg,
          'productId' => $p->productId,
          'ean' => $p->product->ean,
          'mpn' => $p->product->mpn,
          'quantity' => $p->items->count(),
          'purchasePrice' => $p->purchasePrice,
          'originalPrice' => $p->purchasePrice,
          'items' => $p->items,
        ];
      })->toArray();
    }

    return view('erp.storage-entries.update', [
      'document' => $document,
      'errors' => $errors,
      'suppliers' => $suppliers,
      'products' => $products,
    ]);
  }

  public function delete(int $documentId)
  {
    $document = StorageEntriesIncomeInvoice::where('id', $documentId)->firstOrFail();

    // Delete check
    $hasExisted = StorageItem::where('storageEntriesIncomeInvoiceId', $documentId)
      ->where('isExited', 1)
      ->count();

    if ($hasExisted) {
      return redirect('/erp/storage-entries/update/' . $documentId)
        ->withErrors(['msg' => 'Изтриването на записа не е възможно, тъй като има заприходени артикули, които вече са изписани от склада (продадени или отписани по друг начин).']);
    }

    // Delete items
    foreach (StorageItem::where('storageEntriesIncomeInvoiceId', $documentId)->get() as $item) {
      $item->delete();
    }

    // Delete document products
    foreach (StorageEntryProduct::where('storageEntriesIncomeInvoiceId', $documentId)->get() as $entryProduct) {
      $entryProduct->delete();
    }

    // Delete document
    $document->delete();

    return redirect('/erp/storage-entries')
      ->with('success', 'Успешно изтрихте записа.');
  }

  private function validateItems(Request $request, MessageBag $errors): array
  {
    $inputProducts = $request->input('products', []);
    if (!$inputProducts) {
      $errors->add('products', 'Добавете поне един продукт.');
      return [];
    }

    $items = [];
    foreach ($inputProducts as $idx => $inputProduct) {
      $name = trim($inputProduct['name'] ?? '');
      $ean = trim($inputProduct['ean'] ?? '');
      $mpn = trim($inputProduct['mpn'] ?? '');

      if ($name === '') {
        $errors->add("products.$idx.name", 'Името е задължително.');
      }

      if ($ean === '' && $mpn === '') {
        $errors->add("products.$idx.ean", 'Попълнете поне едно от полетата EAN или MPN.');
      }

      $inputProduct['name'] = $name;
      $inputProduct['ean'] = $ean;
      $inputProduct['mpn'] = $mpn;

      $price = isset($inputProduct['purchasePrice']) ? (double)$inputProduct['purchasePrice'] : 0;
      if ($price <= 0) {
        $errors->add("products.$idx.purchasePrice", 'Покупната цена трябва да е по-голяма от 0.');
      }
      $inputProduct['purchasePrice'] = $price;

      $product = null;

      if (!empty($inputProduct['productId'])) {
        $product = Product::find($inputProduct['productId']);
      }

      if (!$product && !empty($inputProduct['mpn'])) {
        $product = Product::where('mpn', $inputProduct['mpn'])->first();
      }

      if (!$product && !empty($inputProduct['ean'])) {
        $product = Product::where('ean', $inputProduct['ean'])->first();
      }

      // Internal usage - extend existing product
      if (!empty($inputProduct['internalUse']) && $product) {
        if ($product->usageStatus !== ProductUsageStatus::InternalUse->value) {
          $product = $product->replicate();
          $product->mpn = 'internal-' . $product->mpn;
          $product->ean = 'internal-' . $product->ean;
          $product->usageStatus = ProductUsageStatus::InternalUse->value;
          $product->source = ProductSource::StorageEntries->value;
          $product->save();
        }
      }

      // Add the product
      if (!$product) {
        $product = new Product();
        $product->nameBg = $inputProduct['name'] ?? 'Ново изделие';
        $product->mpn = $inputProduct['mpn'] ?? null;
        $product->ean = $inputProduct['ean'] ?? null;
        $product->purchasePrice = $inputProduct['purchasePrice'];
        $product->price = ((dbConfig('markupPercent') / 100) * $inputProduct['purchasePrice']) + $inputProduct['purchasePrice'];
        $product->usageStatus = ProductUsageStatus::Draft->value;
        $product->source = ProductSource::StorageEntries->value;

        // Internal usage - extend new product
        if (!empty($inputProduct['internalUse'])) {
          $product->mpn .= '-internal';
          $product->ean .= '-internal';
          $product->usageStatus = ProductUsageStatus::InternalUse->value;
        }

        $product->save();
      }

      $items[] = [
        'product' => $product,
        'data' => $inputProduct,
      ];
    }

    return $items;
  }

  private function syncItems(StorageEntriesIncomeInvoice $document, array $items): void
  {
    /* @var $existingProducts StorageItem[] */
    $existingProducts = $document->products()->with('items')->get();
    $productsById = $existingProducts->keyBy('id');

    // Pre-check for exited items that would be deleted
    $usedProductIds = [];
    foreach ($items as $item) {
      $data = $item['data'];
      $entryProduct = !empty($data['entryProductId']) ? $productsById->get($data['entryProductId']) : null;
      if ($entryProduct) {
        /* @var $existingItems StorageItem[] */
        $existingItems = $entryProduct->items->keyBy('id');
        $usedItemIds = [];
        foreach (($data['items'] ?? []) as $sub) {
          if (!empty($sub['itemId'])) {
            $usedItemIds[] = $sub['itemId'];
          }
        }
        foreach ($existingItems as $existingItem) {
          if (!in_array($existingItem->id, $usedItemIds) && ($existingItem->isExited || $existingItem->priceCorrectionIncomeCreditMemoId)) {
            throw new \RuntimeException('Не може да се продължи, тъй като някои артикули вече са изписани от склада или имат кредитно известие.');
          }
        }
        $usedProductIds[] = $entryProduct->id;
      }
    }

    foreach ($existingProducts as $existingProduct) {
      if (!in_array($existingProduct->id, $usedProductIds)) {
        if ($existingProduct->items->contains(fn($i) => $i->isExited)) {
          throw new \RuntimeException('Не може да се продължи, тъй като някои артикули вече са изписани от склада или имат кредитно известие.');
        }
      }
    }

    // Perform sync
    $usedProductIds = [];
    $seq = 1;
    foreach ($items as $item) {
      $product = $item['product'];
      $data = $item['data'];

      $entryProduct = !empty($data['entryProductId']) ? $productsById->get($data['entryProductId']) : null;
      if (!$entryProduct) {
        $entryProduct = new StorageEntryProduct();
        $entryProduct->storageEntriesIncomeInvoiceId = $document->id;
      }

      $entryProduct->productId = $product->id;
      $entryProduct->purchasePrice = $data['purchasePrice'] ?? 0;
      $entryProduct->arrangementSeq = $seq;
      $entryProduct->save();

      $usedProductIds[] = $entryProduct->id;

      $existingItems = $entryProduct->items->keyBy('id');

      $quantity = max(1, (int)($data['quantity'] ?? 1));
      $subItems = $data['items'] ?? [];
      if (count($subItems) < $quantity) {
        for ($i = count($subItems); $i < $quantity; $i++) {
          $subItems[$i] = [];
        }
      }

      $subSeq = 1;
      $usedItemIds = [];
      foreach ($subItems as $sub) {
        $storageItem = !empty($sub['itemId']) ? $existingItems->get($sub['itemId']) : null;
        if (!$storageItem) {
          $storageItem = new StorageItem();
          $storageItem->storageEntryProductsId = $entryProduct->id;
          $storageItem->storageEntriesIncomeInvoiceId = $document->id;
        }

        $storageItem->productId = $product->id;
        $storageItem->purchasePrice = $entryProduct->purchasePrice;
        $storageItem->originalPrice = $entryProduct->purchasePrice;
        $storageItem->invoiceNumber = $document->documentNumber;
        $storageItem->invoiceDate = $document->documentDate;
        $storageItem->supplierId = $document->supplierId;
        $storageItem->serialNumber = $sub['serialNumber'] ?? null;
        $storageItem->note = $sub['note'] ?? null;
        $storageItem->arrangementSeq = $subSeq;
        $storageItem->save();
        if ($storageItem->wasRecentlyCreated) {
          $storageItem->addHistory('Заприхождаване на артикул');
        }

        $usedItemIds[] = $storageItem->id;
        $subSeq++;
      }

      foreach ($existingItems as $existingItem) {
        if (!in_array($existingItem->id, $usedItemIds)) {
          $existingItem->delete();
        }
      }

      $seq++;
    }

    foreach ($existingProducts as $existingProduct) {
      if (!in_array($existingProduct->id, $usedProductIds)) {
        foreach ($existingProduct->items as $item) {
          $item->delete();
        }
        $existingProduct->delete();
      }
    }
  }
}
