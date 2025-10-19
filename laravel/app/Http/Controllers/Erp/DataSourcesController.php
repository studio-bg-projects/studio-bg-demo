<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\DataSourceMatch;
use App\Models\DataSourceProduct;
use App\Models\Product;

class DataSourcesController extends Controller
{
  public function products()
  {
    $matches = DataSourceMatch::query()
      ->where('hasMatch', 1)
      ->whereNotNull('erpProductId')
      ->pluck('erpProductId')
      ->all();

    $products = Product::orderBy('nameBg')
      ->whereIn('id', $matches)
      ->orderBy('id', 'desc')
      ->get();

    return view('erp.data-sources.index', compact('products'));
  }

  public function productInfo(int $productId)
  {
    /* @var $match DataSourceMatch */
    $match = DataSourceMatch::where('erpProductId', $productId)->first();

    if (!$match) {
      return [];
    }

    $externalData = [];

    foreach ($match->matches as $externalProductId) {
      /* @var $externalProduct DataSourceProduct */
      $externalProduct = DataSourceProduct::where('externalProductId', $externalProductId)->first();

      if (!$externalProduct) {
        continue;
      }

      if (is_null($externalProduct->data)) {
        $externalProduct->data = $this->getProductInfo($externalProductId, 'EN');
        $externalProduct->save();
      }

      $externalData[] = $externalProduct->data;
    }

    return $externalData;
  }

  protected function getProductInfo(int $externalProductId, $lang)
  {
    $user = env('ICECAT_USERNAME');
    $pass = env('ICECAT_PASSWORD');

    $url = 'https://live.icecat.biz/api?lang=' . $lang . '&shopname=' . $user . '&icecat_id=' . $externalProductId;

    $headers = [];
    /*
    $credentials = $user . ':' . $pass;
    $basicAuth = 'Basic ' . base64_encode($credentials);

    $headers = [
      'Authorization: ' . $basicAuth,
      'api-token: 7e9de885-8b41-46ff-afc4-499900c0e4f8',
      'content-token: c134950f-d83d-4527-bf98-5a5eafb3d29a',
      'Accept: application/json'
    ];
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $response = curl_exec($ch);

    if ($response === false) {
      $err = curl_error($ch);
      curl_close($ch);
      return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
      $data = json_decode($response, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
      }
    }

    return null;
  }
}
