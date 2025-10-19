<?php

namespace App\Services\Jobs;

use Illuminate\Database\Connection;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

abstract class BaseSyncJob extends BaseJob
{
  protected ?Connection $shopConn = null;
  protected ?PhpRedisConnection $redisConn = null;

  public static array $languages = [
    1 => 'En',
    2 => 'Bg',
  ];

  protected int $storeId = 0;

  const string PREFIX = 'oc_';

  public function shopConn()
  {
    if (!$this->shopConn) {
      $this->shopConn = DB::connection('mysql-shop');
    }

    return $this->shopConn;
  }

  public function redisConn()
  {
    if (!$this->redisConn) {
      $this->redisConn = Redis::connection('shop');
    }

    return $this->redisConn;
  }

  protected function dictionarizeShopRecords($table, $keyColumn, $secondaryKeyColumn = null, $thirdKeyColumn = null, $filter = []): array
  {
    $result = [];
    foreach ($this->shopConn()->table(self::PREFIX . $table)->where($filter)->get() as $row) {
      if ($thirdKeyColumn) {
        $result[$row->{$keyColumn}][$row->{$secondaryKeyColumn}][$row->{$thirdKeyColumn}] = $row;
      } elseif ($secondaryKeyColumn) {
        $result[$row->{$keyColumn}][$row->{$secondaryKeyColumn}] = $row;
      } else {
        $result[$row->{$keyColumn}] = $row;
      }
    }
    return $result;
  }

  protected function cleanupEmptyRelations($childTable, $mainTable, $fieldId): void
  {
    $cleanRs = $this->shopConn()->select('
      SELECT * FROM `' . self::PREFIX . $childTable . '`
      WHERE `' . $fieldId . '` NOT IN (SELECT `' . $fieldId . '` FROM `' . self::PREFIX . $mainTable . '`)
    ');
    foreach ($cleanRs as $row) {
      $this->out(sprintf('Cleanup %s %s', $childTable, json_encode($row)));

      $this->shopConn()->delete('
        DELETE FROM `' . self::PREFIX . $childTable . '` WHERE `' . $fieldId . '` = ' . $row->{$fieldId}
      );
    }
  }

  protected function deleteRedisKeys(string $keysPrefix): void
  {
    $redisPrefix = $this->redisConn()->getOption(\Redis::OPT_PREFIX);
    $keys = $this->redisConn()->keys($keysPrefix . '*');
    foreach ($keys as $key) {
      $key = substr($key, strlen($redisPrefix));
      $this->redisConn()->del($key);
      $this->out(sprintf('Redis delete %s', $key));
    }
  }

  function slug(string $text): string
  {
    $text = preg_replace('/[^\p{L}\p{N}\s-]+/u', '', $text);
    $text = preg_replace('/[\s-]+/', ' ', $text);
    $text = mb_strtolower($text);
    $slug = str_replace(' ', '-', $text);
    return trim($slug, '-');
  }
}
