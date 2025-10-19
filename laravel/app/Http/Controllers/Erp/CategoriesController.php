<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $categoriesQuery = Category::query();
    $categoriesQuery->whereNull('parentId');
    $categoriesQuery->orderBy('sortOrder');

    /* @var $mainCategories Category[] */
    $mainCategories = $categoriesQuery->get();

    $categories = [];
    foreach ($mainCategories as $category) {
      $categories[] = $category;

      foreach ($category->children as $child) {
        $child->parent = $category;
        $categories[] = $child;
      }
    }

    return view('erp.categories.index', [
      'categories' => $categories,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $category = new Category();

    if ($request->isMethod('post')) {
      $category->fill($request->all());

      $validator = Validator::make($request->all(), [
        'parentId' => ['nullable', 'integer', 'exists:categories,id'],
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'sortOrder' => ['required', 'integer'],
        'isActive' => ['required', 'boolean'],
        'isHidden' => ['required', 'boolean'],
        'isHomeSlider' => ['required', 'boolean'],
      ]);

      if ($request->filled('parentId')) {
        /* @var $parent Category */
        $parent = Category::find($request->parentId);
        if (!$parent || $parent->id === $category->id || $parent->parentId !== null) {
          $errors->add('parentId', 'Не може да изберете родителската категория');
        }
      }

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $category->save();

        return redirect('/erp/categories/update/' . $category->id)
          ->with('success', 'Успешно създадохте нова категория.');
      }
    } else {
      // Defaults
      $category->isActive = true;
      $category->sortOrder = 0;
    }

    /* @var $categories Category[] */
    $categories = Category::whereNull('parentId')
      ->orderBy('sortOrder')
      ->get();

    return view('erp.categories.create', [
      'category' => $category,
      'errors' => $errors,
      'categories' => $categories,
    ]);
  }

  public function update(int $categoryId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $category Category */
    $category = Category::where('id', $categoryId)->firstOrFail();

    if ($request->isMethod('post')) {
      $category->fill($request->all());

      $validator = Validator::make($request->all(), [
        'parentId' => ['nullable', 'integer', 'exists:categories,id'],
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'sortOrder' => ['required', 'integer'],
        'isActive' => ['required', 'boolean'],
        'isHidden' => ['required', 'boolean'],
        'isHomeSlider' => ['required', 'boolean'],
      ]);

      if ($request->filled('parentId')) {
        /* @var $parent Category */
        $parent = Category::find($request->parentId);
        if (!$parent || $parent->id === $category->id || $parent->parentId !== null) {
          $errors->add('parentId', 'Не може да изберете родителската категория.');
        }
      }

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $category->save();

        return redirect('/erp/categories/update/' . $category->id)
          ->with('success', 'Успешно редактирахте категорията.');
      }
    }

    /* @var $categories Category[] */
    $categories = Category::whereNull('parentId')
      ->where('id', '!=', $category->id)
      ->orderBy('sortOrder')
      ->get();

    return view('erp.categories.update', [
      'category' => $category,
      'errors' => $errors,
      'categories' => $categories,
    ]);
  }

  public function specifications(int $categoryId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $category Category */
    $category = Category::where('id', $categoryId)->firstOrFail();

    if ($request->isMethod('post')) {
      $request->validate([
        'ids' => ['nullable', 'array'],
        'ids.*' => ['integer', 'distinct', 'exists:specifications,id'],
      ]);

      $ids = $request->input('ids') ?? [];

      $syncData = [];
      foreach ($ids as $index => $specificationId) {
        $syncData[$specificationId] = ['sortOrder' => $index + 1];
      }
      $category->specifications()->sync($syncData);
    }

    // Get category specifications
    $assignedSpecification = $category->specifications()->get();
    $assignedSpecificationIds = $assignedSpecification->map(function ($specificationId) {
      return $specificationId->id;
    })->toArray();

    // Get all specifications (and exclude assigned)
    /* @var $specifications Specification[] */
    $specifications = Specification::query()
      ->whereNotIn('id', $assignedSpecificationIds)
      ->orderBy('nameBg')
      ->get();

    return view('erp.categories.specifications', [
      'category' => $category,
      'errors' => $errors,
      'specifications' => $specifications,
      'assignedSpecification' => $assignedSpecification,
    ]);
  }

  public function delete(int $categoryId)
  {
    /* @var $category Category */
    $category = Category::where('id', $categoryId)->firstOrFail();

    $category->delete();

    return redirect('/erp/categories')
      ->with('success', 'Успешно изтрихте категорията.');
  }

  public function products(int $categoryId)
  {
    /* @var $category Category */
    $category = Category::where('id', $categoryId)->firstOrFail();

    // Products
    $productsQuery = Product::query();
    $productsQuery = $this->applySort($productsQuery);
    $productsQuery->whereHas('categories', function ($query) use ($category) {
      $query->where('categoryId', $category->id);
    });
    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.categories.products', [
      'category' => $category,
      'products' => $products,
    ]);
  }
}
