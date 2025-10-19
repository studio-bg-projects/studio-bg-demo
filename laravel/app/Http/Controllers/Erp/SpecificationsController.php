<?php

namespace App\Http\Controllers\Erp;

use App\Enums\SpecificationValueType;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Specification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Enum;

class SpecificationsController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $specificationsQuery = Specification::query();
    $specificationsQuery = $this->applySort($specificationsQuery);
    $specificationsQuery = $this->applyFilter($specificationsQuery);
    $specifications = $specificationsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.specifications.index', [
      'specifications' => $specifications,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $specification = new Specification();

    if ($request->isMethod('post')) {
      $specification->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'valueType' => [new Enum(SpecificationValueType::class)],
        'options' => ['nullable', 'string'],
        'isActive' => ['required', 'boolean'],
      ]);

      $validator->sometimes('options', ['required', 'string', 'min:1'], function ($input) {
        return in_array($input->valueType, [SpecificationValueType::Option->value]);
      });

      if ($request->valueType === SpecificationValueType::Option->value) {
        try {
          $specification->options = $this->makeOptions($specification->options ?? '');
        } catch (Exception $e) {
          $errors->add('options', $e->getMessage());
        }
      }

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $specification->save();

        return redirect('/erp/specifications/update/' . $specification->id)
          ->with('success', 'Успешно създадохте нова спецификация.');
      }
    } else {
      // Defaults
      $specification->isActive = true;
    }

    return view('erp.specifications.create', [
      'specification' => $specification,
      'errors' => $errors,
    ]);
  }

  public function update(int $specificationId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $specification Specification */
    $specification = Specification::where('id', $specificationId)->firstOrFail();

    if ($request->isMethod('post')) {
      $specification->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'valueType' => [new Enum(SpecificationValueType::class)],
        'options' => ['nullable', 'string'],
        'isActive' => ['required', 'boolean'],
      ]);

      $validator->sometimes('options', ['required', 'string', 'min:1'], function ($input) {
        return in_array($input->valueType, [SpecificationValueType::Option->value]);
      });

      if ($request->valueType === SpecificationValueType::Option->value) {
        try {
          $specification->options = $this->makeOptions($specification->options ?? '');
        } catch (Exception $e) {
          $errors->add('options', $e->getMessage());
        }
      }

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        if (!in_array($request->valueType, [SpecificationValueType::Option->value])) {
          $specification->options = '';
        }

        $specification->save();

        return redirect('/erp/specifications/update/' . $specification->id)
          ->with('success', 'Успешно редактирахте спецификацията.');
      }
    }

    return view('erp.specifications.update', [
      'specification' => $specification,
      'errors' => $errors,
    ]);
  }

  public function makeOptions(string $options)
  {
    $lines = preg_split("[\n|\r\n]", $options);

    foreach ($lines as $option) {
      $option = trim($option);

      if (empty($option)) {
        continue;
      }

      if (!str_contains($option, '=')) {
        throw new \InvalidArgumentException("Невалиден формат за опция: $option. Очаква се id=value.");
      }

      list($key, $title) = explode('=', $option, 2);
      $key = trim($key);
      $title = trim($title);

      if (isset($optionsMap[$key])) {
        throw new \InvalidArgumentException("Ключът '$key' вече е използван с опция '$optionsMap[$key]'. Опцията '$title' не може да бъде добавена.");
      }

      $optionsMap[$key] = $title;
    }

    return implode("\n", array_map(fn($key, $title) => "$key=$title", array_keys($optionsMap), $optionsMap));
  }

  public function delete(int $specificationId)
  {
    /* @var $specification Specification */
    $specification = Specification::where('id', $specificationId)->firstOrFail();

    $specification->delete();

    return redirect('/erp/specifications')
      ->with('success', 'Успешно изтрихте спецификацията.');
  }

  public function products(int $specificationId)
  {
    /* @var $specification Specification */
    $specification = Specification::where('id', $specificationId)->firstOrFail();

    // Products
    $productsQuery = Product::query();
    $productsQuery = $this->applySort($productsQuery);
    $productsQuery->whereHas('specifications', function ($query) use ($specification) {
      $query->where('specificationId', $specification->id);
    });
    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.specifications.products', [
      'specification' => $specification,
      'products' => $products,
    ]);
  }
}
