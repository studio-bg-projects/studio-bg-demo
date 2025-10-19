<?php

namespace App\Services\FeedsImportsLoader;

use Exception;

class T1Distribution extends BaseLoader
{
  protected string $url;
  protected array $items = [];

  public function __construct(string $url)
  {
    $this->url = $url;
  }

  public function load()
  {
    $xmlStr = @file_get_contents($this->url);
    if ($xmlStr === false) {
      throw new Exception('[T1Distribution] Can`t download the XML: ' . $this->url);
    }

    $xml = @simplexml_load_string($xmlStr);
    if (!$xml) {
      throw new Exception('[T1Distribution] Can`t parse the XML');
    }

    $xmlItems = $xml->channel->item ?? $xml->item ?? [];

    foreach ($xmlItems as $xmlItem) {
      $xmlItem = json_decode(json_encode($xmlItem), true);
      $xmlItem['price'] = isset($xmlItem['price']) ? (double)$xmlItem['price'] : null;
      $xmlItem['quantity'] = isset($xmlItem['quantity']) ? (int)$xmlItem['quantity'] : null;
      $this->items[] = [
        'uniqueId' => 't1dist:' . $xmlItem['sku'],
        'name' => !empty($xmlItem['name']) ? (string)$xmlItem['name'] : null,
        'mpn' => !empty($xmlItem['model_number']) ? (string)$xmlItem['model_number'] : null,
        'ean' => !empty($xmlItem['ean']) ? (string)$xmlItem['ean'] : null,
        'price' => isset($xmlItem['price']) ? (double)$xmlItem['price'] : null,
        'quantity' => isset($xmlItem['qty']) ? (int)$xmlItem['qty'] : null,
        'originalData' => $xmlItem
      ];
    }
  }

  public function getItems(): array
  {
    return $this->items;
  }
}
