<?php

namespace App\Http\Controllers\Erp;

use App\Enums\DocumentType;
use App\Enums\OrderEventAction;
use App\Enums\OrderEventActorType;
use App\Enums\UploadGroupType;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Customer;
use App\Models\Document;
use App\Models\DocumentLine;
use App\Models\DocumentItem;
use App\Models\IncomesAllocation;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrdersEvent;
use App\Models\SalesRepresentative;
use App\Models\Upload;
use App\Models\StorageItem;
use App\Models\StorageExitsItem;
use App\Enums\StorageItemExitType;
use App\Services\MailMakerService;
use App\Services\MapService;
use App\Services\UploadsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class DocumentsController extends Controller
{
  protected UploadsService $uploadsService;

  use FilterAndSort;

  public function __construct()
  {
    $this->uploadsService = new UploadsService();
    parent::__construct();
  }

  public function index(Request $request)
  {
    $documentsQuery = Document::query();
    $documentsQuery = $this->applySort($documentsQuery);
    $documentsQuery = $this->applyFilter($documentsQuery);
    $documentsQuery = $this->applyQFilter($documentsQuery, ['type', 'documentNumber']);

    if ($request->get('related')) {
      $relatedId = (int)$request->get('related');
      $relatedIds = DB::table('documentRelated')
        ->where('documentId', $relatedId)
        ->pluck('relatedId')
        ->toArray();

      $documentsQuery->whereIn('id', array_merge([$relatedId], $relatedIds));
    }

    $documents = $documentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.documents.index', [
      'documents' => $documents,
    ]);
  }

  public function prepare(Request $request, DocumentType $type)
  {
    $orderId = $request->input('orderId');
    $customerId = $request->input('customerId');
    $relatedId = $request->input('relatedId', $request->input('refDocumentId', $request->input('related')));

    $relatedType = MapService::documentTypes($type)->relatedType;
    $relatedRequired = MapService::documentTypes($type)->relatedRequired;

    $customers = Customer::orderBy('companyName')->get();
    $orders = Order::orderBy('id', 'desc')->get();
    $relatedDocuments = Document::where('type', $relatedType)->orderBy('id', 'desc')->get();

    // Set by order
    if ($orderId && !$customerId) {
      $order = Order::find($orderId);
      if ($order) {
        $customerId = $order->customerId;
        $relatedDocuments = Document::where('type', $relatedType)
          ->where('orderId', $order->id)
          ->orderBy('id', 'desc')->get();
      }
    }

    return view('erp.documents.prepare', [
      'type' => $type,
      'relatedType' => $relatedType,
      'relatedRequired' => $relatedRequired,
      'orderId' => $orderId,
      'customerId' => $customerId,
      'relatedId' => $relatedId,
      'customers' => $customers,
      'orders' => $orders,
      'relatedDocuments' => $relatedDocuments,
    ]);
  }

  public function create(Request $request, DocumentType $type, int $refDocumentId)
  {
    $info = MapService::documentTypes($type);
    $refDocument = $refDocumentId ? Document::with(['lines.documentItems.storageItem'])->find($refDocumentId) : null;

    if ($type === DocumentType::OutcomeCreditMemo) {
      foreach ($refDocument->related as $related) {
        if ($related->type === DocumentType::OutcomeCreditMemo) {
          abort(400, 'Вече има друго кредитно известие с номер: ' . $related->documentNumber);
        }
      }

      if (!$refDocument || $refDocument->type !== DocumentType::Invoice) {
        return redirect('/erp/documents/prepare/' . $type->value)
          ->with('error', 'Моля изберете фактура.');
      }
    }

    if ($info->relatedRequired && !$refDocument) {
      return redirect('/erp/documents/prepare/' . $type->value)
        ->with('error', 'Моля изберете свързан документ.');
    }

    if ($refDocumentId && $info->relatedType && (!$refDocument || $refDocument->type !== $info->relatedType)) {
      return redirect('/erp/documents/prepare/' . $type->value)
        ->with('error', 'Избран е документ с неправилен тип.');
    }

    $errors = new MessageBag();
    $document = new Document();
    $lines = [];
    $order = null;
    $orderProducts = [];
    $shippingPrice = null;

    if ($request->isMethod('post')) {
      $request->merge(['type' => $type->value]);

      $prefix = MapService::documentTypes($type)->prefix;
      $inputNumber = $request->input('documentNumber');
      if ($inputNumber !== null && $inputNumber !== '') {
        $parts = explode('-', $inputNumber, 2);
        $number = $parts[1] ?? $parts[0];
        $request->merge(['documentNumber' => $prefix . '-' . $number]);
      }

      $document->fill($request->all());
      $document->type = $type;

      $validator = Validator::make($request->all(), [
        'documentNumber' => ['required', 'string', 'max:255', Rule::unique('documents')->where(fn($query) => $query->where('type', $type->value))],
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'orderId' => ['nullable', 'integer', 'exists:orders,id'],
        'isForeignInvoice' => ['required', 'boolean'],
        'incoterms' => ['nullable', 'string', 'min:3', 'max:3'],
        'issueDate' => ['required', 'date_format:Y-m-d'],
        'dueDate' => ['nullable', 'date_format:Y-m-d'],
        'recipientName' => ['required', 'string', 'max:255'],
        'recipientCompanyId' => ['required', 'string', 'max:255'],
        'recipientVatId' => ['required', 'string', 'max:255'],
        'recipientAddress' => ['required', 'string', 'max:255'],
        'shipToName' => ['required', 'string', 'max:255'],
        'shipToCompanyId' => ['required', 'string', 'max:255'],
        'shipToVatId' => ['required', 'string', 'max:255'],
        'shipToAddress' => ['required', 'string', 'max:255'],
        'issuerNameBg' => ['required', 'string', 'max:255'],
        'issuerNameEn' => ['required', 'string', 'max:255'],
        'issuerCompanyId' => ['required', 'string', 'max:255'],
        'issuerVatId' => ['nullable', 'string', 'max:255'],
        'issuerAddressBg' => ['required', 'string', 'max:255'],
        'issuerAddressEn' => ['required', 'string', 'max:255'],
        'issuerBankNameBg' => ['required', 'string', 'max:255'],
        'issuerBankNameEn' => ['required', 'string', 'max:255'],
        'issuerIBankAddressBg' => ['required', 'string', 'max:255'],
        'issuerIBankAddressEn' => ['required', 'string', 'max:255'],
        'issuerIban' => ['required', 'string', 'max:255'],
        'issuerSwift' => ['required', 'string', 'max:255'],
        'incomeMethodBg' => ['nullable', 'string', 'max:255'],
        'incomeMethodEn' => ['nullable', 'string', 'max:255'],
        'incomeCommentBg' => ['nullable', 'string', 'max:255'],
        'incomeCommentEn' => ['nullable', 'string', 'max:255'],
        'salesRepresentativeId' => ['nullable', 'integer', 'exists:salesRepresentatives,id'],
      ]);

      $errors->merge($validator->errors());

      $readOnlyDocs = [DocumentType::DeliveryNote, DocumentType::OrderConfirmation, DocumentType::PackingList];
      if (in_array($document->type, $readOnlyDocs, true) && $refDocument) {
        $lines = $this->extractLines($refDocument);
      } else {
        $lines = $this->validateInvoiceLines($request, $errors, $document->type);
      }

      if ($errors->isEmpty() && $document->type === DocumentType::OutcomeCreditMemo) {
        $filteredLines = [];
        foreach ($lines as $inputLine) {
          $items = $inputLine['items'] ?? [];
          $inputLine['items'] = [];
          foreach ($items as $lineItem) {
            if (($lineItem['creditAmount'] ?? 0) > 0) {
              $inputLine['items'][] = $lineItem;
            }
          }

          $lineCredit = ($inputLine['price'] * (int)$inputLine['quantity']) - $inputLine['totalPrice'];
          if ($lineCredit > 0) {
            $filteredLines[] = $inputLine;
          }
        }
        $lines = $filteredLines;
        if (empty($lines)) {
          $errors->add('lines', 'Добавете поне един ред.');
        }
      }

      if ($errors->isEmpty()) {
        $document->vatRate = $document->vatRate ?? dbConfig('default:vatRate');

        $totalAmountNoVat = 0;
        if ($document->type === DocumentType::OutcomeCreditMemo) {
          foreach ($lines as $inputLine) {
            $totalAmountNoVat += $inputLine['creditAmount'];
          }
          $totalAmountNoVat *= -1;
        } else {
          foreach ($lines as $inputLine) {
            $totalAmountNoVat += $inputLine['totalPrice'];
          }
        }

        $document->totalAmountNoVat = $totalAmountNoVat;
        $document->totalVat = $totalAmountNoVat * $document->vatRate / 100;
        $document->totalAmount = $document->totalAmountNoVat + $document->totalVat;
        $document->paidAmount = $document->paidAmount ?? 0;
        $document->leftAmount = $document->totalAmount;
        $document->save();

        foreach ($lines as $inputLine) {
          $documentLine = new DocumentLine();
          $documentLine->fill($inputLine);
          $documentLine->documentId = $document->id;
          $documentLine->totalPrice = $inputLine['totalPrice'];
          $documentLine->save();

          if (!empty($inputLine['items'])) {
            foreach ($inputLine['items'] as $lineItem) {
              if (empty($lineItem['storageItemId'])) {
                continue;
              }

              /* @var $item StorageItem */
              $item = StorageItem::find($lineItem['storageItemId']);

              if ($document->type === DocumentType::Invoice) {
                if (!empty($lineItem['selected']) && (!$item->isExited && !$item?->exit?->count() > 0)) {
                  $documentItem = new DocumentItem();
                  $documentItem->storageItemId = $item->id;
                  $documentItem->documentLineId = $documentLine->id;
                  $documentItem->documentId = $document->id;
                  $documentItem->save();

                  $item->isExited = true;
                  $item->exitDate = $document->issueDate;
                  $item->save();

                  $storageExitsItem = new StorageExitsItem();
                  $storageExitsItem->storageItemId = $item->id;
                  $storageExitsItem->documentLineId = $documentLine->id;
                  $storageExitsItem->outcomeInvoiceId = $document->id;
                  $storageExitsItem->sellPrice = $documentLine->price;
                  $storageExitsItem->originalPrice = $documentLine->price;
                  $storageExitsItem->type = StorageItemExitType::Invoice;
                  $storageExitsItem->save();
                }
              } elseif ($document->type === DocumentType::OutcomeCreditMemo) {
                $credit = (double)($lineItem['creditAmount'] ?? 0);
                if ($credit <= 0) {
                  continue;
                }

                $documentItem = new DocumentItem();
                $documentItem->storageItemId = $item->id;
                $documentItem->documentLineId = $documentLine->id;
                $documentItem->documentId = $document->id;
                $documentItem->save();

                /* @var $exit StorageExitsItem */
                $exit = StorageExitsItem::where('storageItemId', $item->id)->first();
                if ($exit) {
                  $exit->sellPrice = max(0, $exit->sellPrice - $credit);
                  $exit->priceCorrectionOutcomeCreditMemoId = $document->id;
                  $exit->save();

                  if ($exit->sellPrice <= 0) {
                    $newItem = new StorageItem();
                    $newItem->storageEntryProductsId = $item->storageEntryProductsId;
                    $newItem->productId = $item->productId;
                    $newItem->purchasePrice = $item->purchasePrice;
                    $newItem->originalPrice = $item->originalPrice;
                    $newItem->invoiceNumber = $item->invoiceNumber;
                    $newItem->invoiceDate = $item->invoiceDate;
                    $newItem->supplierId = $item->supplierId;
                    $newItem->serialNumber = $item->serialNumber;
                    $newItem->note = $item->note;
                    $newItem->arrangementSeq = $item->arrangementSeq;
                    $newItem->outcomeCreditMemoDocumentId = $document->id;
                    $newItem->isExited = false;
                    $newItem->predecessorId = $item->id;
                    $newItem->save();
                    $newItem->addHistory('Връщане чрез кредитно известие');
                  }
                }
              } else {
                $documentItem = new DocumentItem();
                $documentItem->storageItemId = $item->id;
                $documentItem->documentLineId = $documentLine->id;
                $documentItem->documentId = $document->id;
                $documentItem->save();
              }
            }
          }
        }

        $this->generateDocument($document);
        $this->incrementSeqNumbers($document);
        $this->syncRelated($document, $refDocumentId ? [$refDocumentId] : []);

        $document->resyncPaid();

        if ($document->orderId) {
          $ordersEvent = new OrdersEvent();
          $ordersEvent->orderId = $document->orderId;
          $ordersEvent->action = OrderEventAction::AddDocument;
          $ordersEvent->actionNote = $document->type->value;
          $ordersEvent->actionData = ['id' => $document->id];
          $ordersEvent->actorType = OrderEventActorType::Operator;
          $ordersEvent->actorId = Auth::id();
          $ordersEvent->save();
        }

        return redirect('/erp/documents/view/' . $document->id)
          ->with('success', 'Успешно създадохте нов документ.');
      }
    } else {
      if ($refDocument) {
        $document->fill($refDocument->toArray());
        $lines = $this->extractLines($refDocument);
      } else {
        $document->issuerNameBg = dbConfig('default:issuerNameBg');
        $document->issuerNameEn = dbConfig('default:issuerNameEn');
        $document->issuerAddressBg = dbConfig('default:issuerAddressBg');
        $document->issuerAddressEn = dbConfig('default:issuerAddressEn');
        $document->issuerCompanyId = dbConfig('default:issuerCompanyId');
        $document->issuerVatId = dbConfig('default:issuerVatId');
        $document->incomeMethodBg = dbConfig('default:paymentMethodBg');
        $document->incomeMethodEn = dbConfig('default:paymentMethodEn');
        $document->issuerBankNameBg = dbConfig('default:issuerBankNameBg');
        $document->issuerBankNameEn = dbConfig('default:issuerBankNameEn');
        $document->issuerIBankAddressBg = dbConfig('default:issuerIBankAddressBg');
        $document->issuerIBankAddressEn = dbConfig('default:issuerIBankAddressEn');
        $document->issuerIban = '';
        $document->issuerSwift = dbConfig('default:issuerSwift');
        $document->vatRate = dbConfig('default:vatRate');
      }

      // Default fields (after extending $refDocument)
      $document->type = $type;
      $document->issueDate = new \DateTime();

      $customerId = null;

      $orderId = (int)$request->input('orderId');
      if ($orderId) {
        $order = Order::find($orderId);

        if ($order) {
          $document->orderId = $order->id;
          $customerId = $order->customerId;

          foreach ($order->shopData->order_product ?? [] as $orderProduct) {
            $erpProduct = Product::find($orderProduct->product_id);
            $orderProducts[] = [
              'type' => 'product',
              'productId' => $orderProduct->product_id,
              'name' => $orderProduct->name,
              'mpn' => $erpProduct->mpn ?? null,
              'ean' => $erpProduct->ean ?? null,
              'po' => $orderProduct->po ?? null,
              'weight' => $erpProduct->weight ?? null,
              'width' => $erpProduct->width ?? null,
              'height' => $erpProduct->height ?? null,
              'length' => $erpProduct->length ?? null,
              'price' => (double)$orderProduct->price,
              'quantity' => (double)$orderProduct->quantity,
              'totalPrice' => (double)$orderProduct->total,
            ];
          }

          $shipping = collect($order->shopData->order_total ?? [])->firstWhere('code', 'shipping');
          $shippingPrice = $shipping ? (double)$shipping->value : null;

          if (empty($lines)) {
            $lines = $orderProducts;
            if ($shippingPrice) {
              $lines[] = [
                'type' => 'empty',
                'productId' => null,
                'name' => 'Доставка',
                'mpn' => null,
                'ean' => null,
                'po' => null,
                'price' => $shippingPrice,
                'quantity' => 1,
                'totalPrice' => $shippingPrice,
              ];
            }
          }
        }
      }

      $customerId = (int)($request->input('customerId') ?? $customerId);
      if ($customerId) {
        $customer = Customer::find($customerId);

        if ($customer) {
          $document->customerId = $customer->id;
          $document->recipientName = $customer->companyName;
          $document->recipientCompanyId = $customer->companyId;
          $document->recipientVatId = $customer->companyVatNumber;
          $document->recipientAddress = trim(($customer->companyCity ?? '') . ', ' . ($customer->companyAddress ?? ''), ', ');

          $document->shipToName = $customer->companyName;
          $document->shipToCompanyId = $customer->companyId;
          $document->shipToVatId = $customer->companyVatNumber;
          $document->shipToAddress = trim(($customer->companyCity ?? '') . ', ' . ($customer->companyAddress ?? ''), ', ');

          if ($customer->paymentTerm > 0 && !$document->dueDate) {
            $document->dueDate = now()->addDays($customer->paymentTerm);
          }

          if ($customer->preferredIncoterms > 0 && !$document->incoterms) {
            $document->incoterms = $customer->preferredIncoterms;
          }

          if ($customer->preferredLang > 0 && !$document->language) {
            $document->language = $customer->preferredLang;
          }
        }
      }
    }

    /* @var $salesRepresentatives SalesRepresentative[] */
    $salesRepresentatives = SalesRepresentative::orderBy('nameBg')->get();

    return view('erp.documents.create', [
      'document' => $document,
      'salesRepresentatives' => $salesRepresentatives,
      'refDocument' => $refDocument,
      'errors' => $errors,
      'lines' => $lines,
      'order' => $order,
      'products' => $orderProducts,
      'shippingPrice' => $shippingPrice,
    ]);
  }

  public function view(int $documentId, Request $request)
  {
    /* @var $document Document */
    $document = Document::with(['lines.documentItems.storageItem'])
      ->where('id', $documentId)
      ->firstOrFail();

    $createDocumentTypes = [];
    foreach (DocumentType::cases() as $type) {
      $map = MapService::documentTypes($type);
      if (($map->relatedType ?? null) === $document->type) {
        $createDocumentTypes[] = [
          'type' => $type->value,
          'label' => $map->labelBg,
        ];
      }
    }

    return view('erp.documents.view', [
      'document' => $document,
      'lines' => $document->lines,
      'createDocumentTypes' => $createDocumentTypes,
    ]);
  }

  public function notify(int $documentId)
  {
    /* @var $document Document */
    $document = Document::where('id', $documentId)->firstOrFail();

    if (!$document->customerId) {
      return redirect('/erp/documents/view/' . $document->id)
        ->withErrors(['msg' => 'Документът няма клиент.']);
    }

    $mailMaker = new MailMakerService();
    $mailMaker->document($document->id);

    return redirect('/erp/documents/view/' . $document->id)
      ->with('success', 'Изпратен е имейл на клиента.');
  }

  public function delete(int $documentId)
  {
    /* @var $document Document */
    $document = Document::where('id', $documentId)->firstOrFail();

    if (!MapService::documentTypes($document->type)->isDeletable) {
      return redirect('/erp/documents/view/' . $document->id)
        ->withErrors(['msg' => 'Този тип документ не може да бъде изтрит!']);
    }

    if ($document->incomesAllocations && $document->incomesAllocations->count()) {
      return redirect('/erp/documents/view/' . $document->id)
        ->withErrors(['msg' => 'Не може да триете документ към които има прикачени плащания!']);
    }

    $document->delete();

    return redirect('/erp/documents')
      ->with('success', 'Успешно изтрихте документа.');
  }

  public function preview(string $lang, int $documentId, string $type, string $format)
  {
    /* @var $document Document */
    $document = Document::where('id', $documentId)->firstOrFail();

    if (!in_array($lang, ['bg', 'en'])) {
      abort(400, sprintf('Недефиниран език: %s', $lang));
    }

    if (!DocumentType::tryFrom($type)) {
      abort(400, sprintf('Недефиниран тип на документ: %s', $type));
    }

    if (!in_array($format, ['html', 'pdf'])) {
      abort(400, sprintf('Невалиден формат: %s', $format));
    }

    $html = (string)view('erp.documents.templates.' . $type, [
      'lang' => $lang,
      'document' => $document,
      'pageTitle' => $type . ' - #' . $document->documentNumber,
    ]);

    if ($format === 'pdf') {
      try {
        $response = pdf($html);

        $fileName = $type . ' - ' . $document->documentNumber . ' [' . $lang . '].pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        return $response;
      } catch (Exception $e) {
        abort(400, sprintf('Възникна грешка при генерирането на ПДФ: %s', $e->getMessage()));
      }
    } else {
      return $html;
    }
  }

  public function incomesAllocations(int $documentId)
  {
    /* @var $document Document */
    $document = Document::where('id', $documentId)->firstOrFail();

    if (!MapService::documentTypes($document->type)->isPayable) {
      abort('400', 'Този документ не може да има плащания!');
    }

    // Income Allocations
    $incomesAllocationsQuery = IncomesAllocation::query();
    $incomesAllocationsQuery = $this->applySort($incomesAllocationsQuery);
    $incomesAllocationsQuery->where([
      'documentId' => $document->id,
    ]);
    $incomesAllocations = $incomesAllocationsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.documents.incomes-allocations', [
      'document' => $document,
      'incomesAllocations' => $incomesAllocations,
    ]);
  }

  protected function generateDocument(Document $document)
  {
    $type = $document->type->value;
    $lang = $document->language ?: $document->customer?->preferredLang ?: 'bg';
    $html = (string)view('erp.documents.templates.' . $type, [
      'lang' => $lang,
      'document' => $document,
      'pageTitle' => $type . ' - #' . $document->documentNumber,
    ]);

    $pdf = pdf($html);

    // Delete old and reuse the name
    $existingItem = count($document->uploads) ? $document->uploads[0] : null;
    $fileName = $existingItem ? $existingItem->name : $document->type->value . '-' . date('YmdHis') . '.pdf';
    if ($existingItem) {
      $this->uploadsService->deleteFile($existingItem->groupType, $existingItem->groupId, $existingItem->name);
      $existingItem->delete();
    }

    // Save to db
    $file = new Upload();
    $file->groupType = UploadGroupType::Documents;
    $file->groupId = $document->fileGroupId;
    $file->name = $fileName;
    $file->hash = md5($pdf);
    $file->size = strlen($pdf);
    $file->originalName = $file->name;
    $file->extension = 'pdf';
    $file->mimeType = 'application/pdf';
    $file->sortOrder = 0;
    $file->save();

    // Save as file
    $this->uploadsService->putFile($file->groupType, $file->groupId, $file->name, $pdf);

    // Set generated date
    $now = new \DateTime();
    $document->updatedAt = $now;
    $document->save();
  }

  protected function incrementSeqNumbers(Document $document)
  {
    $updateKey = null;

    if ($document->type === DocumentType::Invoice) {
      if ($document->isForeignInvoice) {
        $updateKey = 'document:seq:world';
      } else {
        $updateKey = 'document:seq:bg';
      }
    } elseif ($document->type === DocumentType::ProformaInvoice) {
      $updateKey = 'document:seq:proforma';
    }

    if ($updateKey) {
      Config::where('key', $updateKey)->update([
        'value' => dbConfig($updateKey) + 1
      ]);
    }
  }

  protected function syncRelated(Document $document, array $relatedIds)
  {
    if (empty($relatedIds)) {
      $document->related()->sync([]);
      DB::table('documentRelated')
        ->where('relatedId', $document->id)
        ->delete();
      return;
    }

    $groupIds = collect($relatedIds)->merge([$document->id])->unique()->values();
    $queue = $groupIds->values();

    for ($i = 0; $i < $queue->count(); $i++) {
      $id = $queue[$i];
      $neighbors = DB::table('documentRelated')
        ->where('documentId', $id)
        ->pluck('relatedId')
        ->merge(DB::table('documentRelated')
          ->where('relatedId', $id)
          ->pluck('documentId'));

      foreach ($neighbors as $neighbor) {
        if (!$groupIds->contains($neighbor)) {
          $groupIds->push($neighbor);
          $queue->push($neighbor);
        }
      }
    }

    $idsWithoutDoc = $groupIds->filter(fn($id) => $id !== $document->id)->values();

    $document->related()->sync($idsWithoutDoc->toArray());

    DB::table('documentRelated')
      ->where('relatedId', $document->id)
      ->whereNotIn('documentId', $idsWithoutDoc)
      ->delete();

    $now = now();
    $data = [];
    foreach ($groupIds as $id1) {
      foreach ($groupIds as $id2) {
        if ($id1 === $id2) {
          continue;
        }
        $data[] = [
          'documentId' => $id1,
          'relatedId' => $id2,
          'createdAt' => $now,
          'updatedAt' => $now,
        ];
      }
    }

    DB::table('documentRelated')
      ->upsert($data, ['documentId', 'relatedId'], ['updatedAt']);
  }

  private function extractLines(Document $document): array
  {
    return $document->lines->map(function (DocumentLine $row) {
      return [
        'productId' => $row->productId,
        'name' => $row->name,
        'mpn' => $row->mpn,
        'ean' => $row->ean,
        'po' => $row->po,
        'price' => $row->price,
        'quantity' => $row->quantity,
        'totalPrice' => $row->totalPrice,
        'type' => $row->productId ? 'product' : 'empty',
        'items' => $row->documentItems->map(function (DocumentItem $item) {
          return [
            'storageItemId' => $item->storageItemId,
            ...$item->storageItem->toArray(),
            ...[
              'selected' => true,
              'sellPrice' => $item->storageItem->exit->sellPrice ?? null,
            ],
          ];
        })->toArray(),
      ];
    })->toArray();
  }

  private function validateInvoiceLines(Request $request, MessageBag $errors, DocumentType $type): array
  {
    $rows = $request->input('lines', []);
    if (!$rows) {
      $errors->add('lines', 'Добавете поне един ред.');
      return [];
    }

    $isInvoice = $type === DocumentType::Invoice;
    $isCreditMemo = $type === DocumentType::OutcomeCreditMemo;

    foreach ($rows as $idx => &$row) {
      $lineValidator = Validator::make($row, [
        'type' => ['required', 'in:product,empty'],
        'productId' => ['nullable', 'integer', 'exists:products,id'],
        'name' => ['required', 'string', 'max:255'],
        'mpn' => ['nullable', 'string', 'max:255'],
        'ean' => ['nullable', 'string', 'max:255'],
        'po' => ['nullable', 'string', 'max:255'],
        'price' => ['required', 'numeric'],
        'quantity' => ['required', 'integer', 'min:1'],
        'creditAmount' => ['nullable', 'numeric', 'min:0'],
        'items' => ['array'],
        'items.*.storageItemId' => ['integer', 'exists:storageItems,id'],
        'items.*.selected' => ['boolean'],
        'items.*.creditAmount' => ['nullable', 'numeric', 'min:0'],
      ]);

      foreach ($lineValidator->errors()->messages() as $field => $messages) {
        foreach ($messages as $message) {
          $errors->add("lines.$idx.$field", $message);
        }
      }

      if (($row['type'] ?? null) === 'product' && empty($row['productId'])) {
        $errors->add("lines.$idx.productId", 'Моля изберете продукт.');
      }

      if ($isInvoice && !empty($row['productId'])) {
        $available = StorageItem::where('productId', $row['productId'])
          ->where('isExited', false)
          ->count();
        if ($row['quantity'] > $available) {
          $errors->add("lines.$idx.quantity", 'Няма достатъчно наличност.');
        }
      }

      if ($isInvoice && !empty($row['items'])) {
        $selected = collect($row['items'])->where('selected', true)->count();
        if ($selected !== (int)$row['quantity']) {
          $errors->add("lines.$idx.items", 'Бройката не съвпада с избраните артикули.');
        }
      }

      if ($isCreditMemo) {
        $lineCredit = 0;
        if (!empty($row['items'])) {
          foreach ($row['items'] as $iIdx => &$item) {
            $credit = (double)($item['creditAmount'] ?? 0);
            $max = (double)($item['sellPrice'] ?? 0);
            if ($credit > $max) {
              $errors->add("lines.$idx.items.$iIdx.creditAmount", 'Невалидна сума.');
            }
            $item['creditAmount'] = $credit;
            $lineCredit += $credit;
          }
        } else {
          $credit = (double)($row['creditAmount'] ?? 0);
          $max = (double)$row['price'] * (int)$row['quantity'];
          if ($credit > $max) {
            $errors->add("lines.$idx.creditAmount", 'Невалидна сума.');
          }
          $row['creditAmount'] = $credit;
          $lineCredit = $credit;
        }
        $row['totalPrice'] = (double)$row['price'] * (int)$row['quantity'] - $lineCredit;
      } else {
        $row['totalPrice'] = (double)$row['price'] * (int)$row['quantity'];
      }
    }

    return $rows;
  }
}

