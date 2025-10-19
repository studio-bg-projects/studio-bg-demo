<?php

namespace App\Services;

use App\Models\FeedImportItem;
use App\Models\Product;

class ProductSyncService
{
  public function updateProductFromFeed(Product $product, FeedImportItem $feedItem)
  {
    $purchasePrice = $feedItem->itemPrice <= 0 ? null : $feedItem->itemPrice;
    $markupPercent = $feedItem->feedImport?->markupPercent ?? 0.0;

    if ($purchasePrice !== null) {
      $price = $purchasePrice * (1 + $markupPercent / 100);
      $price = round($price, 2);
    } else {
      $price = $product->getOriginal('price');
    }

    $quantity = $feedItem->itemQuantity < 0 ? 0 : $feedItem->itemQuantity;

    // Check for differences
    if (
      $product->getOriginal('purchasePrice') == $purchasePrice
      && $product->getOriginal('price') == $price
      && $product->getOriginal('quantity') == $quantity
    ) {
      return [];
    }

    $product->purchasePrice = $purchasePrice;
    $product->price = $price;
    $product->quantity = $quantity;

    $changes = $product->getDirty();

    $product->save();

    return $changes;
  }
}
