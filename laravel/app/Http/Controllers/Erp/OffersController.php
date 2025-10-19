<?php

namespace App\Http\Controllers\Erp;

use App\Enums\OfferStatus;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Enum;

class OffersController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $offersQuery = Offer::query();
    $offersQuery = $this->applySort($offersQuery);
    $offersQuery = $this->applyFilter($offersQuery);
    $offers = $offersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.offers.index', [
      'offers' => $offers,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $offer = new Offer();
    $items = [];

    if ($request->isMethod('post')) {
      $offer->fill($request->all());
      $items = $request->input('items') ?? [];
      $items = $request->input('items') ?? [];

      $validator = Validator::make($request->all(), [
        'offerNumber' => ['required', 'string', 'max:255', 'unique:offers,offerNumber'],
        'status' => ['required', new Enum(OfferStatus::class)],
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'validUntil' => ['nullable', 'date_format:Y-m-d'],
        'companyId' => ['nullable', 'string', 'max:255'],
        'companyName' => ['nullable', 'string', 'max:255'],
        'companyPerson' => ['nullable', 'string', 'max:255'],
        'companyEmail' => ['nullable', 'string', 'max:255'],
        'companyPhone' => ['nullable', 'string', 'max:255'],
        'companyAddress' => ['nullable', 'string', 'max:255'],
        'notesPublic' => ['nullable', 'string'],
        'notesPrivate' => ['nullable', 'string'],

        'items' => ['nullable', 'array'],
        'items.*.productId' => ['nullable', 'integer', 'exists:products,id'],
        'items.*.name' => ['required', 'string', 'max:255'],
        'items.*.mpn' => ['nullable', 'string', 'max:255'],
        'items.*.ean' => ['nullable', 'string', 'max:255'],
        'items.*.price' => ['required', 'numeric'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        'items.*.discountPercent' => ['nullable', 'numeric'],
        'items.*.totalPrice' => ['required', 'numeric'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $offer->save();

        foreach ($items as $inputItem) {
          $offerItem = new OfferItem();
          $offerItem->fill($inputItem);
          $offerItem->offerId = $offer->id;
          $offerItem->save();
        }

        return redirect('/erp/offers/update/' . $offer->id)
          ->with('success', 'Успешно създадохте нова оферта.');
      }
    } else {
      // Defaults
      $offer->offerNumber = Offer::generateOfferNumber();
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.offers.create', [
      'offer' => $offer,
      'errors' => $errors,
      'customers' => $customers,
      'items' => $items,
    ]);
  }

  public function update(int $offerId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $offer Offer */
    $offer = Offer::where('id', $offerId)->firstOrFail();

    if ($request->isMethod('post')) {
      $offer->fill($request->all());
      $items = $request->input('items') ?? [];

      $validator = Validator::make($request->all(), [
        'offerNumber' => ['required', 'string', 'max:255', 'unique:offers,offerNumber,' . $offer->id],
        'status' => ['required', new Enum(OfferStatus::class)],
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'validUntil' => ['nullable', 'date_format:Y-m-d'],
        'companyId' => ['nullable', 'string', 'max:255'],
        'companyName' => ['nullable', 'string', 'max:255'],
        'companyPerson' => ['nullable', 'string', 'max:255'],
        'companyEmail' => ['nullable', 'string', 'max:255'],
        'companyPhone' => ['nullable', 'string', 'max:255'],
        'companyAddress' => ['nullable', 'string', 'max:255'],
        'notesPublic' => ['nullable', 'string'],
        'notesPrivate' => ['nullable', 'string'],

        'items' => ['nullable', 'array'],
        'items.*.id' => ['nullable', 'integer'],
        'items.*.productId' => ['nullable', 'integer', 'exists:products,id'],
        'items.*.name' => ['required', 'string', 'max:255'],
        'items.*.mpn' => ['nullable', 'string', 'max:255'],
        'items.*.ean' => ['nullable', 'string', 'max:255'],
        'items.*.price' => ['required', 'numeric'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        'items.*.discountPercent' => ['nullable', 'numeric'],
        'items.*.totalPrice' => ['required', 'numeric'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $offer->save();

        $existingItems = $offer->items()->pluck('id')->toArray();
        $inputItemIds = array_filter(array_column($items, 'id'));
        $itemsToDelete = array_diff($existingItems, $inputItemIds);
        if (!empty($itemsToDelete)) {
          OfferItem::whereIn('id', $itemsToDelete)->delete();
        }

        foreach ($items as $inputItem) {
          $offerItem = OfferItem::where('id', $inputItem['id'] ?? 0)->first() ?? new OfferItem();
          $offerItem->fill($inputItem);
          $offerItem->id = $inputItem['id'] ?? null;
          $offerItem->offerId = $offer->id;
          $offerItem->save();
        }

        return redirect('/erp/offers/update/' . $offer->id)
          ->with('success', 'Успешно редактирахте офертата.');
      }
    } else {
      $items = $offer->items;
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.offers.update', [
      'offer' => $offer,
      'errors' => $errors,
      'customers' => $customers,
      'items' => $items,
    ]);
  }

  public function delete(int $offerId)
  {
    /* @var $offer Offer */
    $offer = Offer::where('id', $offerId)->firstOrFail();

    $offer->delete();

    return redirect('/erp/offers')
      ->with('success', 'Успешно изтрихте офертата.');
  }

  public function preview(string $lang, int $offerId, string $format)
  {
    /* @var $offer Offer */
    $offer = Offer::where('id', $offerId)->firstOrFail();

    if (!in_array($lang, ['bg', 'en'])) {
      abort(400, sprintf('Недефиниран език: %s', $lang));
    }

    if (!in_array($format, ['html', 'pdf'])) {
      abort(400, sprintf('Невалиден формат: %s', $format));
    }

    $html = (string)view('erp.offers.preview', [
      'lang' => $lang,
      'offer' => $offer,
      'pageTitle' => $offer->offerNumber,
    ]);

    if ($format === 'pdf') {
      try {
        $response = pdf($html);

        $fileName = $offer->offerNumber . ' [' . $lang . '].pdf';
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
}
