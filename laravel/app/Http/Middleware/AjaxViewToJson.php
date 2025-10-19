<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AjaxViewToJson
{
  private const HEADER_RETURN_HTML = 'X-Return-HTML';
  private const INPUT_RETURN_HTML = 'returnHtml';

  /**
   * Handle an incoming request.
   *
   * @param Closure(Request): (SymfonyResponse) $next
   */
  public function handle(Request $request, Closure $next): SymfonyResponse
  {
    $response = $next($request);

    if (!$request->ajax()) {
      return $this->prepareOriginalResponse($response, $request);
    }

    if ($response instanceof JsonResponse) {
      return $response;
    }

    if ($this->shouldReturnOriginalResponse($request)) {
      return $this->prepareOriginalResponse($response, $request);
    }

    if ($response instanceof ViewContract) {
      return $this->convertViewToJson($response);
    }

    if ($response instanceof Response) {
      $originalContent = $response->getOriginalContent();

      if ($originalContent instanceof ViewContract) {
        $jsonResponse = $this->convertViewToJson($originalContent);
        $jsonResponse->setStatusCode($response->getStatusCode());

        foreach ($response->headers->all() as $header => $values) {
          if (strtolower($header) === 'content-type') {
            continue;
          }

          foreach ($values as $value) {
            $jsonResponse->headers->set($header, $value, false);
          }
        }

        return $jsonResponse;
      }
    }

    return $this->prepareOriginalResponse($response, $request);
  }

  private function shouldReturnOriginalResponse(Request $request): bool
  {
    $headerValue = $request->headers->get(self::HEADER_RETURN_HTML);

    if ($this->isTruthy($headerValue)) {
      return true;
    }

    $inputValue = $request->query(self::INPUT_RETURN_HTML);

    if ($this->isTruthy($inputValue)) {
      return true;
    }

    $inputValue = $request->input(self::INPUT_RETURN_HTML);

    return $this->isTruthy($inputValue);
  }

  private function isTruthy(mixed $value): bool
  {
    if ($value === null) {
      return false;
    }

    if (is_bool($value)) {
      return $value;
    }

    if (is_numeric($value)) {
      return (int)$value !== 0;
    }

    if (is_string($value)) {
      $value = strtolower($value);

      if (in_array($value, ['1', 'true', 'yes', 'on', 'html'], true)) {
        return true;
      }

      if (in_array($value, ['0', 'false', 'no', 'off'], true)) {
        return false;
      }
    }

    return false;
  }

  private function prepareOriginalResponse(mixed $response, Request $request): SymfonyResponse
  {
    if ($response instanceof ViewContract) {
      return $response->toResponse($request);
    }

    if ($response instanceof SymfonyResponse) {
      if ($response instanceof Response) {
        $originalContent = $response->getOriginalContent();

        if ($originalContent instanceof ViewContract) {
          $response->setContent($originalContent->render());
        }
      }

      return $response;
    }

    return response($response);
  }

  protected function convertViewToJson(ViewContract $view): JsonResponse
  {
    $data = $view->getData();

    unset($data['app'], $data['__env']);

    if (isset($data['errors']) && $data['errors'] instanceof ViewErrorBag) {
      $data['errors'] = $data['errors']->toArray();
    }

    return response()->json($data);
  }
}

