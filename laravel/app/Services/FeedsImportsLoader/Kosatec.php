<?php

namespace App\Services\FeedsImportsLoader;

use Exception;

class Kosatec extends BaseLoader
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
      throw new Exception('[Kosatec] Can`t download the XML: ' . $this->url);
    }

    $xml = @simplexml_load_string($xmlStr);
    if (!$xml) {
      throw new Exception('[Kosatec] Can`t parse the XML');
    }

    $xmlItems = $xml->item ?? $xml->item ?? [];

    foreach ($xmlItems as $xmlItem) {
      $xmlItem = json_decode(json_encode($xmlItem), true);
      $xmlItem['price'] = isset($xmlItem['price']) ? (double)$xmlItem['price'] : null;
      $xmlItem['stock'] = isset($xmlItem['stock']) ? (int)$xmlItem['stock'] : null;

      $this->items[] = [
        'uniqueId' => 'kosatec:' . $xmlItem['id'],
        'name' => !empty($xmlItem['text']) ? (string)$xmlItem['text'] : null,
        'mpn' => !empty($xmlItem['mpn']) ? (string)$xmlItem['mpn'] : null,
        'ean' => !empty($xmlItem['ean']) ? (string)$xmlItem['ean'] : null,
        'price' => isset($xmlItem['price']) ? (double)$xmlItem['price'] : null,
        'quantity' => isset($xmlItem['stock']) ? (int)$xmlItem['stock'] : null,
        'originalData' => $xmlItem
      ];
    }
  }

  public function getItems(): array
  {
    return $this->items;
  }
}
