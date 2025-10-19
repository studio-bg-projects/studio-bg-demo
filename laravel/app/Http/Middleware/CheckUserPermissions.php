<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserPermissions
{
  /**
   * Handle an incoming request.
   *
   * @param Closure(Request): (Response) $next
   */
  public function handle(Request $request, Closure $next, array|string $requiredPermission): Response
  {
    if (is_string($requiredPermission)) {
      $requiredPermission = explode(';', $requiredPermission);
    }

    $user = Auth::user();

    if ($user && $user->isAdmin) {
      return $next($request);
    }

    $diff = array_diff($requiredPermission, $user->permissions);
    if ($user && empty($diff)) {
      return $next($request);
    }

    abort(403, 'Нямате достъп до тази функционалност. Трябват ви следните права: ' . implode(', ', $diff) . '.');
  }
}
