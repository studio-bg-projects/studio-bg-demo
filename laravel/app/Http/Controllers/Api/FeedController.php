<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductUsageStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductsSpecification;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleXMLElement;

class FeedController extends Controller
{
  public function products(string $lang = 'bg', string $format = 'json', Request $request)
  {
    $lang = strtolower($lang);
    $langKey = ucfirst($lang);
    $langUrl = $lang . '-' . $lang;

    $products = Product::with(['manufacturer', 'categories', 'uploads'])
      ->where('usageStatus', ProductUsageStatus::ListedOnline->value)
      ->get()
      ->map(function (Product $product) use ($request, $langKey, $langUrl) {
        switch ($request->get('case')) {
          case 'amco':
          {
            return $this->rsAmcoFull($product, $langKey, $langUrl);
          }
          case 'jarComputers':
          default:
          {
            return $this->rsJarComputersFull($product, $langKey, $langUrl);
          }
        }
      });

    return $this->toResponse($products, 'products', $format);
  }

  public function available(string $lang = 'bg', string $format = 'json', Request $request)
  {
    $lang = strtolower($lang);
    $langKey = ucfirst($lang);
    $langUrl = $lang . '-' . $lang;

    $products = Product::all()
      ->where('usageStatus', ProductUsageStatus::ListedOnline->value)
      ->map(function (Product $product) use ($request, $langKey, $langUrl) {
        switch ($request->get('case')) {
          case 'amco':
          case 'jarComputers':
          default:
          {
            return $this->rsAvailable($product, $langKey, $langUrl);
          }
        }
      });

    return $this->toResponse($products, 'products', $format);
  }

  protected function toResponse($data, $key, $format = 'xml')
  {
    if ($format === 'json') {
      return response()->json([$key => $data]);
    } else {
      return $this->toXmlResponse($data->toArray(), $key);
    }
  }

  protected function toXmlResponse(array $data, $parentName = 'products'): \Illuminate\Http\Response
  {
    $xml = new SimpleXMLElement('<' . $parentName . '/>');
    $this->arrayToXml($data, $xml);

    return response($xml->asXML(), 200)
      ->header('Content-Type', 'application/xml');
  }


  protected function arrayToXml(array $data, SimpleXMLElement &$xml): void
  {
    foreach ($data as $key => $value) {
      // If the starts with @ add it as a attribute
      if (is_string($key) && str_starts_with($key, '@')) {
        $attrName = substr($key, 1);
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
          $xml->addAttribute($attrName, (string)$value);
        }

        // Skip the element if can not be added as attribute
        continue;
      }

      $key = is_numeric($key) ? (Str::singular($xml->getName()) ?: 'item') : $key;

      if (is_array($value)) {
        $child = $xml->addChild($key);
        $this->arrayToXml($value, $child);
      } else {
        if (is_bool($value)) {
          $value = (int)$value;
        }
        $xml->addChild($key, htmlspecialchars((string)$value));
      }
    }
  }

  //////////////////////////////////////
  //////////////////////////////////////

  protected function notAvailableReason(Product $product): ?string
  {
    if ($product->quantity <= 0) {
      return 'Insufficient quantity available [quantity]';
    } elseif (!$product->onStock && $product->quantity <= 0) {
      return 'Product is not currently in stock and quantity is 0 [onStock & quantity]';
    } elseif ($product->usageStatus->value != ProductUsageStatus::ListedOnline->value) {
      return 'Product is inactive [usageStatus] = ' . $product->usageStatus->value;
    }

    return null;
  }

  protected function rsJarComputersFull(Product $product, string $langKey, string $langUrl)
  {
    $category = $product->categories ? $product->categories->first() : null;
    $parentCategory = $category->parent ?? null;

    return [
      'product_id' => $product->id,
      // 'product_code' => '____',
      'product_name' => $product->{'name' . $langKey},
      'product_brand' => $product->manufacturer->name ?? null,
      // 'product_model' => '____',
      // 'product_gtin' => '____',
      'category_path' => ($parentCategory ? $parentCategory->{'name' . $langKey} . ' | ' : '') . ($category->{'name' . $langKey} ?? ''),
      'category' => $category->{'name' . $langKey} ?? null,
      'description' => strip_tags($product->{'description' . $langKey}),
      'available' => !$this->notAvailableReason($product),
      'not_available_reason' => $this->notAvailableReason($product),
      'available_count' => $product->quantity,
      'warranty' => $product->warrantyPeriod,
      'delivery_days' => $product->deliveryDays,
      'weight' => $product->weight,
      'sale_price' => 0,
      'sale_currency' => null,
      'dealer_price' => $product->price,
      'dealer_currency' => 'EUR',
      'product_created' => $product->createdAt,
      'product_last_change' => $product->updatedAt,
      'product_url' => env('SHOP_URL') . 'index.php?route=product/product&language=' . $langUrl . '&product_id=' . $product->id . '&ref=jar',
      'mpn' => $product->mpn,
      'ean' => $product->ean,
      'width' => $product->width,
      'height' => $product->height,
      'length' => $product->length,
      'linked_products' => $product->related->pluck('id')->toArray(),
      'pictures' => $product->uploads->map(function (Upload $upload) {
        return [
          'picture_url' => env('SHOP_URL') . 'image/erp/products/' . $upload->groupId . '/' . $upload->name,
          'picture_order' => $upload->sortOrder,
          'picture_text' => $upload->originalName,
          'picture_last_change' => $upload->updatedAt,
        ];
      })->toArray(),
      'documents' => $product->downloads->map(function (Upload $download) {
        return [
          'document_url' => env('SHOP_URL') . 'image/erp/productDownloads/' . $download->groupId . '/' . $download->name,
          'document_description' => 'Документ - ' . $download->sortOrder,
        ];
      })->toArray(),
      'characteristics' => $product->specifications->filter(function (ProductsSpecification $pSpecification) use ($langKey) {
        return !!$pSpecification->{'specificationValue' . $langKey};
      })->map(function (ProductsSpecification $pSpecification) use ($langKey) {
        return [
          'name' => $pSpecification->specification->{'name' . $langKey},
          'value' => $pSpecification->{'specificationValue' . $langKey},
          'filter' => null,
        ];
      })->toArray(),
      'promotion' => [
        // 'campaign_name' => '____Национална ',
        // 'campaign_start' => '____2014-09-01',
        // 'campaign_end' => '____2014-09-',
        // 'campaign_text' => '____Хайде на училище',
      ],
    ];
  }

  protected function rsAmcoFull(Product $product, string $langKey, string $langUrl)
  {
    $category = $product->categories ? $product->categories->first() : null;
    $parentCategory = $category->parent ?? null;

    return [
      'id' => $product->id,
      'product_code' => null,
      'barcode' => null,
      'title' => $product->{'name' . $langKey},
      'short_description' => strip_tags($product->{'description' . $langKey}),
      'description' => $product->{'description' . $langKey},
      'manufacturer' => $product->manufacturer->name ?? null,
      'width' => $product->width, // Not requested
      'height' => $product->height, // Not requested
      'length' => $product->length, // Not requested
      'weight' => $product->weight,
      'sku' => $product->mpn,
      'mpn' => $product->mpn, // Not requested
      'ean' => $product->ean, // Not requested
      'meta_title' => $product->nameBg,
      'meta_description' => strip_tags($product->{'description' . $langKey}),
      'url' => env('SHOP_URL') . 'index.php?route=product/product&language=' . $langUrl . '&product_id=' . $product->id . '&ref=amco',
      'category' => $parentCategory ? $parentCategory->{'name' . $langKey} : null,
      'sub_category' => $category->{'name' . $langKey} ?? null,
      'sub_sub_category' => null,
      'price' => $product->price,
      'price_currency' => 'EUR', // Not requested
      'original_price' => $product->price,
      'original_price_currency' => 'EUR', // Not requested
      'minimum' => 1,
      'quantity' => $product->quantity,
      'available' => !$this->notAvailableReason($product), // Not requested
      'not_available_reason' => $this->notAvailableReason($product), // Not requested
      'product_created' => $product->createdAt, // Not requested
      'product_last_change' => $product->updatedAt, // Not requested
      'linked_products' => $product->related->pluck('id')->toArray(), // Not requested
      'category_properties' => $product->specifications->filter(function (ProductsSpecification $pSpecification) use ($langKey) {
        return !!$pSpecification->{'specificationValue' . $langKey};
      })->map(function (ProductsSpecification $pSpecification) use ($langKey) {
        return [
          '@name' => $pSpecification->specification->{'name' . $langKey},
          'values' => [
            'value' => [
              'name' => $pSpecification->{'specificationValue' . $langKey}
            ]
          ]
        ];
      })->toArray(),
      'images' => $product->uploads->map(function (Upload $upload) {
        return [
          'image' => env('SHOP_URL') . 'image/erp/products/' . $upload->groupId . '/' . $upload->name,
          'image_order' => $upload->sortOrder, // Not requested
          'image_text' => $upload->originalName, // Not requested
          'image_last_change' => $upload->updatedAt, // Not requested
        ];
      })->toArray(),
    ];
  }

  protected function rsAvailable(Product $product, string $langKey, string $langUrl)
  {
    return [
      'product_id' => $product->id,
      'available' => !$this->notAvailableReason($product),
      'not_available_reason' => $this->notAvailableReason($product),
      'available_count' => $product->quantity,
      'delivery_days' => $product->deliveryDays,
      'sale_price' => 0,
      'sale_currency' => 'EUR',
      'dealer_price' => $product->price,
      'dealer_currency' => 'EUR',
    ];
  }
}
