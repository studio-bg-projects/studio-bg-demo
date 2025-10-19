<?php

namespace App\Http\Controllers\Erp;

use App\Enums\ProductUsageStatus;
use App\Enums\SpecificationValueType;
use App\Enums\ProductNonSyncStatus;
use App\Enums\ProductSource;
use App\Enums\UploadGroupType;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductsSpecification;
use App\Models\FeedImportItem;
use App\Models\Upload;
use App\Services\Jobs\SyncProductsJob;
use App\Services\UploadsService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Intervention\Image\Laravel\Facades\Image;

class ProductsController extends Controller
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
    $productsQuery = Product::query();
    $productsQuery = $this->applySort($productsQuery);
    $productsQuery = $this->applyFilter($productsQuery);
    $productsQuery = $this->applyQFilter($productsQuery, ['mpn', 'ean', 'nameBg']);

    if ($request->input('cFilter.check') === 'noCategories') {
      $productsQuery->whereDoesntHave('categories');
    }

    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 100)->withQueryString();

    // Prepare for JSON response
    $products->load('uploads');

    return view('erp.products.index', [
      'products' => $products,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $product = new Product();
    $product->source = ProductSource::Products->value;

    // Defaults
    $assignedCategories = [];

    if ($request->isMethod('post')) {
      $product->fill($request->all());

      $assignedCategories = $request->input('categories', []);

      $validator = Validator::make($request->all(), [
        'mpn' => ['required', 'string', 'max:255', 'unique:products,mpn'],
        'ean' => ['nullable', 'string', 'max:14', 'unique:products,ean'],
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0.01'],
        'purchasePrice' => ['nullable', 'numeric', 'min:0'],
        'quantity' => ['required', 'integer', 'min:0'],
        'weight' => ['nullable', 'numeric', 'min:0'],
        'width' => ['nullable', 'numeric', 'min:0'],
        'height' => ['nullable', 'numeric', 'min:0'],
        'length' => ['nullable', 'numeric', 'min:0'],
        'warrantyPeriod' => ['nullable', 'numeric', 'min:0'],
        'deliveryDays' => ['nullable', 'numeric', 'min:0'],
        'manufacturerId' => ['nullable', 'integer', 'exists:manufacturers,id'],
        'onStock' => ['required', 'boolean'],
        'isFeatured' => ['required', 'boolean'],
        'usageStatus' => ['required', new Enum(ProductUsageStatus::class)],
        'nonSyncStatus' => ['nullable', new Enum(ProductNonSyncStatus::class)],
        'categories' => ['required_if:usageStatus,' . ProductUsageStatus::ListedOnline->value, 'array'],
        'categories.*' => ['integer', 'exists:categories,id'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $product->save();

        $product->categories()->sync($request->input('categories', []));

        return redirect('/erp/products/update/' . $product->id)
          ->with('success', 'Успешно създадохте нов продукт.');
      }
    } else {
      $product->quantity = 0;
    }

    /* @var $manufacturers Manufacturer[] */
    $manufacturers = Manufacturer::orderBy('name')->get();

    /* @var $categories Category[] */
    $categories = Category::whereNull('parentId')
      ->orderBy('sortOrder')
      ->get();

    return view('erp.products.create', [
      'product' => $product,
      'errors' => $errors,
      'manufacturers' => $manufacturers,
      'categories' => $categories,
      'assignedCategories' => $assignedCategories,
    ]);
  }

  public function update(int $productId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    if ($request->isMethod('post')) {
      $product->fill($request->all());

      // Upload gallery before submitting the data
      if ($request->gallery) {
        $sortOrder = count($product->uploads);
        foreach ($request->gallery as $url) {
          $sortOrder++;
          $this->uploadImage($product, $url, $sortOrder);
        }
      }

      $assignedCategories = $request->input('categories', []);

      $validator = Validator::make($request->all(), [
        'mpn' => ['required', 'string', 'max:255', 'unique:products,mpn,' . $product->id],
        'ean' => ['nullable', 'string', 'max:14', 'unique:products,ean,' . $product->id],
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0.01'],
        'purchasePrice' => ['nullable', 'numeric', 'min:0'],
        'quantity' => ['required', 'integer', 'min:0'],
        'weight' => ['nullable', 'numeric', 'min:0'],
        'width' => ['nullable', 'numeric', 'min:0'],
        'height' => ['nullable', 'numeric', 'min:0'],
        'length' => ['nullable', 'numeric', 'min:0'],
        'warrantyPeriod' => ['nullable', 'numeric', 'min:0'],
        'deliveryDays' => ['nullable', 'numeric', 'min:0'],
        'manufacturerId' => ['nullable', 'integer', 'exists:manufacturers,id'],
        'onStock' => ['required', 'boolean'],
        'isFeatured' => ['required', 'boolean'],
        'usageStatus' => ['required', new Enum(ProductUsageStatus::class)],
        'nonSyncStatus' => ['nullable', new Enum(ProductNonSyncStatus::class)],
        'categories' => ['required_if:usageStatus,' . ProductUsageStatus::ListedOnline->value, 'array'],
        'categories.*' => ['integer', 'exists:categories,id'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $product->save();

        $product->categories()->sync($request->input('categories', []));

        return redirect('/erp/products/update/' . $product->id . '?' . $request->getQueryString())
          ->with('success', 'Успешно редактирахте продукта.');
      }
    } else {
      // Defaults
      $assignedCategories = $product->categories->pluck('id')->toArray();
    }

    /* @var $manufacturers Manufacturer[] */
    $manufacturers = Manufacturer::orderBy('name')->get();

    /* @var $categories Category[] */
    $categories = Category::whereNull('parentId')
      ->orderBy('sortOrder')
      ->get();

    $feedItems = FeedImportItem::where('productId', $product->id)
      ->with('feedImport')
      ->get();

    return view('erp.products.update', [
      'product' => $product,
      'errors' => $errors,
      'manufacturers' => $manufacturers,
      'categories' => $categories,
      'assignedCategories' => $assignedCategories,
      'feedItems' => $feedItems,
    ]);
  }

  public function delete(int $productId)
  {
    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();
    if ($product->storageItems()->exists()) {
      return redirect()->back()
        ->withErrors(['msg' => 'Не може да изтриете продукта, защото има заприходени артикули към него!']);
    }

    $product->delete();

    return redirect('/erp/products')
      ->with('success', 'Успешно изтрихте продукта.');
  }

  public function specifications(int $productId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    // Product specifications map
    $psMap = [];
    foreach ($product->specifications as $psItem) {
      $psMap[$psItem->categoryId][$psItem->specificationId] = $psItem;
    }

    // Values map
    $valuesMap = [];
    $typesMap = [];
    foreach ($product->categories as $category) {
      foreach ($category->specifications as $specification) {
        $valuesMap[$category->id][$specification->id]['bg'] = $psMap[$category->id][$specification->id]->specificationValueBg ?? null;
        $valuesMap[$category->id][$specification->id]['en'] = $psMap[$category->id][$specification->id]->specificationValueEn ?? null;

        $typesMap[$specification->id] = $specification->valueType->value;
      }
    }

    if ($request->isMethod('post')) {
      foreach ($valuesMap as $categoryId => $specifications) {
        foreach ($specifications as $specificationId => $tmp) {
          $newValueBg = $request->input(['s', $categoryId, $specificationId, 'bg']) ?? '';
          $newValueEn = $request->input(['s', $categoryId, $specificationId, 'en']) ?? '';

          if ($typesMap[$specificationId] === SpecificationValueType::Decimal->value) {
            $newValueBg = (double)$newValueBg;
            $newValueEn = (double)$newValueEn;
          } elseif ($typesMap[$specificationId] === SpecificationValueType::Number->value) {
            $newValueBg = (int)$newValueBg;
            $newValueEn = (int)$newValueEn;
          }

          $valuesMap[$categoryId][$specificationId]['bg'] = $newValueBg;
          $valuesMap[$categoryId][$specificationId]['en'] = $newValueEn;

          if (strlen($newValueBg) > 255) {
            $errors->add("$categoryId.$specificationId.bg", 'Полето не може да е повече от 255 символа');
          }

          if (strlen($newValueEn) > 255) {
            $errors->add("$categoryId.$specificationId.en", 'Полето не може да е повече от 255 символа');
          }

          if (!$errors->has("$categoryId.$specificationId")) {
            /* @var $productSpecification ProductsSpecification */
            $productSpecification = $psMap[$categoryId][$specificationId] ?? new ProductsSpecification();
            $productSpecification->productId = $product->id;
            $productSpecification->categoryId = $categoryId;
            $productSpecification->specificationId = $specificationId;
            $productSpecification->specificationValueBg = $newValueBg;
            $productSpecification->specificationValueEn = $newValueEn;
            $productSpecification->save();
          }
        }
      }

      if ($errors->isEmpty()) {
        return redirect('/erp/products/specifications/' . $product->id)
          ->with('success', 'Успешно редактирахте спецификациите на продудкта.');
      }
    }

    return view('erp.products.specifications', [
      'product' => $product,
      'errors' => $errors,
      'valuesMap' => $valuesMap,
    ]);
  }

  public function related(int $productId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    if ($request->isMethod('post')) {
      $request->validate([
        'related' => ['nullable', 'array'],
        'related.*' => ['integer', 'distinct', 'exists:products,id'],
      ]);

      $relatedIds = $request->input('related') ?? [];
      $product->related()->sync($relatedIds);

      if ($errors->isEmpty()) {
        return redirect('/erp/products/related/' . $product->id)
          ->with('success', 'Успешно редактирахте свързаните продукти.');
      }
    } else {
      $relatedIds = $product->related()->pluck('relatedId')->toArray();
    }

    $selectedProducts = Product::select(['id', 'nameBg', 'mpn'])->whereIn('id', $relatedIds)->get();

    return view('erp.products.related', [
      'product' => $product,
      'selectedProducts' => $selectedProducts,
      'errors' => $errors,
    ]);
  }

  public function history(int $productId)
  {
    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    $logs = $product->logs()->orderBy('id', 'desc')->get();

    return view('erp.products.history', [
      'product' => $product,
      'logs' => $logs,
    ]);
  }

  public function storageItems(int $productId)
  {
    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    return view('erp.products.storage-items', [
      'product' => $product,
    ]);
  }

  public function preview(int $productId)
  {
    /* @var $product Product */
    $product = Product::where('id', $productId)->firstOrFail();

    print '<pre>';
    print "<h1>Sync product $product->id</h1>";

    $sync = new SyncProductsJob();
    $sync->run($product->id);

    print "<h2>Redirect to the product...</h2>";
    print "<script>document.location = '" . env('SHOP_URL') . "/index.php?route=product/product&language=bg-bg&product_id=$product->id';</script>";
    print '</pre>';
  }

  private function uploadImage(Product $product, string $url, int $sortOrder)
  {
    try {
      $response = Http::timeout(30)->get($url);
    } catch (\Exception $exception) {
      return;
    }

    if (!$response->successful()) {
      return;
    }

    $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
    $extension = Str::lower($extension);

    $tmpFile = tempnam(sys_get_temp_dir(), 'product-image-');
    $tmpFileWithExtension = $tmpFile . '.' . $extension;

    if (file_exists($tmpFileWithExtension)) {
      unlink($tmpFileWithExtension);
    }

    file_put_contents($tmpFileWithExtension, $response->body());

    if (file_exists($tmpFile)) {
      unlink($tmpFile);
    }

    $image = Image::read($tmpFileWithExtension);
    $image->scale(width: 1200, height: 1200);
    $image->save($tmpFileWithExtension);

    $mimeType = mime_content_type($tmpFileWithExtension) ?: 'image/jpeg';

    $uploadedFile = new UploadedFile(
      $tmpFileWithExtension,
      basename($tmpFileWithExtension),
      $mimeType,
      null,
      true
    );

    $groupType = UploadGroupType::Products;
    $fileName = $this->uploadsService->uploadFile($groupType, $product->fileGroupId, $uploadedFile);

    $file = new Upload();
    $file->groupType = $groupType;
    $file->groupId = $product->fileGroupId;
    $file->name = $fileName;
    $file->hash = md5_file($uploadedFile->getRealPath());
    $file->size = $uploadedFile->getSize();
    $file->originalName = 'Upload ' . $sortOrder;
    $file->extension = $uploadedFile->getClientOriginalExtension();
    $file->mimeType = $uploadedFile->getMimeType();
    $file->sortOrder = $sortOrder;
    $file->save();

    if (file_exists($uploadedFile->getRealPath())) {
      unlink($uploadedFile->getRealPath());
    }
  }
}
