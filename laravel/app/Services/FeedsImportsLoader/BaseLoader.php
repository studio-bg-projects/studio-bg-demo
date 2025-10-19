<?php

namespace App\Services\FeedsImportsLoader;

abstract class BaseLoader
{
  abstract public function __construct(string $url);

  abstract public function load();

  abstract public function getItems(): array;
}
