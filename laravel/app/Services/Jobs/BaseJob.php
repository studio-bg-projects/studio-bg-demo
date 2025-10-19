<?php

namespace App\Services\Jobs;

abstract class BaseJob
{
  abstract public function run(): void;

  protected function out(string $str): void
  {
    print $str . PHP_EOL;
  }
}
