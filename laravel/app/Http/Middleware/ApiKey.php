<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKey
{
  /**
   * Handle an incoming request.
   *
   * @param Closure(Request): (Response) $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $apiKey = $request->header('x-api-key') ?: $request->input('api-key');

    if (!$apiKey) {
      abort(response()->json([
        'errors' => [
          'api-key' => 'Missing parameter: x-api-key or api-key',
        ]
      ], 403));
    }

    /* @var $dbKey \App\Models\ApiKey */
    $dbKey = \App\Models\ApiKey::where(['key' => $apiKey])->first();

    if (!$dbKey) {
      abort(response()->json([
        'errors' => [
          'api-key' => 'Wrong api key',
        ]
      ], 403));
    }

    $dbKey->requestsCount++;
    $dbKey->latestRequest = Carbon::now();

    $data = $dbKey->requestsLog ?? [];
    array_unshift($data, [
      'timestamp' => Carbon::now(),
      'requestUri' => $request->getRequestUri(),
      'method' => $request->getMethod(),
      'json' => $request->isJson() ? $request->json()->all() : null,
      'post' => $request->post(),
    ]);
    $data = array_slice($data, 0, 10);
    $dbKey->requestsLog = $data;

    $dbKey->save();

    return $next($request);
  }
}
