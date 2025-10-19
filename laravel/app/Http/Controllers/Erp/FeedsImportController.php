<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\FeedImport;
use App\Models\FeedImportItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class FeedsImportController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $feedsQuery = FeedImport::query();
    $feedsQuery = $this->applySort($feedsQuery);
    $feedsQuery = $this->applyFilter($feedsQuery);
    $feeds = $feedsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.feeds-imports.index', [
      'feeds' => $feeds,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $feed = new FeedImport();

    if ($request->isMethod('post')) {
      $data = $request->all();

      if (isset($data['syncSchedule'])) {
        $data['syncSchedule'] = array_values(array_unique(array_filter($data['syncSchedule'])));
        sort($data['syncSchedule']);
      }

      $feed->fill($data);

      $validator = Validator::make($data, [
        'providerName' => ['required', 'string', 'max:255'],
        'adapterName' => ['required', 'string', 'max:255'],
        'feedUrl' => ['required', 'string'],
        'markupPercent' => ['required', 'numeric', 'min:0'],
        'techEmail' => ['nullable', 'string', 'max:255'],
        'note' => ['nullable', 'string'],
        'syncSchedule' => ['nullable', 'array'],
        'syncSchedule.*' => ['distinct', 'date_format:H:i'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $feed->save();

        return redirect('/erp/feeds-imports/update/' . $feed->id)
          ->with('success', 'Успешно създадохте нов запис.');
      }
    } else {
      // Defaults
      $feed->markupPercent = dbConfig('markupPercent');
    }

    return view('erp.feeds-imports.create', [
      'feed' => $feed,
      'errors' => $errors,
    ]);
  }

  public function update(int $feedId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $feed = FeedImport::where('id', $feedId)->firstOrFail();

    if ($request->isMethod('post')) {
      $data = $request->all();

      if (isset($data['syncSchedule'])) {
        $data['syncSchedule'] = array_values(array_unique(array_filter($data['syncSchedule'])));
        sort($data['syncSchedule']);
      }

      $feed->fill($data);

      $validator = Validator::make($data, [
        'providerName' => ['required', 'string', 'max:255'],
        'adapterName' => ['required', 'string', 'max:255'],
        'feedUrl' => ['required', 'string'],
        'markupPercent' => ['required', 'numeric', 'min:0'],
        'techEmail' => ['nullable', 'string', 'max:255'],
        'note' => ['nullable', 'string'],
        'syncSchedule' => ['nullable', 'array'],
        'syncSchedule.*' => ['distinct', 'date_format:H:i'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $feed->save();

        return redirect('/erp/feeds-imports/update/' . $feed->id)
          ->with('success', 'Успешно редактирахте записа.');
      }
    }

    return view('erp.feeds-imports.update', [
      'feed' => $feed,
      'errors' => $errors,
    ]);
  }

  public function delete(int $feedId)
  {
    $feed = FeedImport::where('id', $feedId)->firstOrFail();
    $feed->delete();

    return redirect('/erp/feeds-imports')
      ->with('success', 'Успешно изтрихте записа.');
  }

  public function items(int $feedId)
  {
    /* @var $feed FeedImport */
    $feed = FeedImport::where('id', $feedId)->firstOrFail();

    $itemsQuery = FeedImportItem::query()->with(['feedImport', 'product']);
    $itemsQuery = $this->applySort($itemsQuery);
    $itemsQuery = $this->applyFilter($itemsQuery);
    $itemsQuery->where('parentId', $feed->id);
    $items = $itemsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.feeds-imports.items', [
      'feed' => $feed,
      'items' => $items,
    ]);
  }
}
