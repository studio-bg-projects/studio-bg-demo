<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdReservationController extends Controller
{
  protected array $allowedTables = [
    'orders',
    'customers',
    'customersAddresses',
  ];

  public function reserve(Request $request)
  {
    $data = $request->validate([
      'table' => ['required', 'string'],
    ]);

    $table = $data['table'];

    if (!in_array($table, $this->allowedTables, true)) {
      abort(400, 'Unknown table');
    }

    DB::statement('LOCK TABLES `' . $table . '` WRITE');

    try {
      $next = DB::selectOne('SELECT IFNULL(MAX(`id`), 0) AS `maxId` FROM  ' . $table);
      $reservedId = $next->maxId + 1;
      $nextId = $reservedId + 1;

      DB::statement('ALTER TABLE `' . $table . '` AUTO_INCREMENT = ' . $nextId);
    } finally {
      DB::statement('UNLOCK TABLES');
    }

    return [
      'table' => $table,
      'reservedId' => $reservedId,
      'nextAutoIncrement' => $nextId,
    ];
  }
}
