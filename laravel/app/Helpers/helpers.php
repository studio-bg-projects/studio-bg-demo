<?php

if (!function_exists('price')) {
  function price($price, $currency = null)
  {
    $price = doubleval($price);
    $currency = $currency ?: dbConfig('currency:symbol');

    $minusPrefix = $price < 0 ? '-' : '';
    $zeroPrefix = (abs($price) < 1) ? '0' : '';
    return $minusPrefix . $zeroPrefix . number_format(abs($price), 2) . ' ' . $currency;
  }
}

if (!function_exists('pdf')) {
  function pdf(string $html): string
  {
    $pdfGeneratorUrl = env('PDF_SERVER') . 'pdf-server/generator';

    $ch = curl_init($pdfGeneratorUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
      'html' => (string)$html,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new Error($error);
    }
    curl_close($ch);

    return $response;
  }
}

if (!function_exists('dbConfig')) {
  function dbConfig(string $key)
  {
    static $config;
    if (!isset($config)) {
      foreach (\App\Models\Config::all() as $row) {
        $config[$row->key] = $row->value;

        if ($row->type === 'json') {
          $config[$row->key] = json_decode($config[$row->key]);
        } elseif ($row->type === 'number') {
          $config[$row->key] = doubleval($config[$row->key]);
        }
      }
    }

    return $config[$key] ?? null;
  }
}
