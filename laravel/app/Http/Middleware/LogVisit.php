<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class LogVisit
{
  /**
   * Handle an incoming request.
   *
   * @param Closure(Request): (SymfonyResponse) $next
   */
  public function handle(Request $request, Closure $next): SymfonyResponse
  {
    $response = $next($request);

    if ($this->shouldLogRequest($request)) {
      $this->storeVisit($request);
    }

    return $response;
  }

  private function shouldLogRequest(Request $request): bool
  {
    if (app()->runningInConsole()) {
      return false;
    }

    return true;
  }

  private function storeVisit(Request $request): void
  {
    try {
      VisitLog::create([
        //'ipAddress' => $request->getClientIp() ?? '0.0.0.0',
        'ipAddress' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $request->getClientIp() ?? '0.0.0.0', // @todo make it better
        'path' => ltrim($request->getPathInfo(), '/'),
        'requestMethod' => strtoupper($request->getMethod()),
        'referrer' => $request->headers->get('referer'),
        'userAgent' => $request->userAgent(),
        'locale' => app()->getLocale(),
        'sessionId' => $request->hasSession() ? $request->session()->getId() : null,
        'visitedAt' => now()
      ]);
    } catch (Throwable $exception) {
      Log::warning('Failed to store visit log entry.', [
        'exception' => $exception->getMessage()
      ]);
    }
  }
}
