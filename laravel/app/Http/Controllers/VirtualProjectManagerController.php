<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VirtualProjectManagerController extends Controller
{
  public function index()
  {
    return view('virtual-project-manager.index');
  }

  public function session(Request $request): JsonResponse
  {
    $instructions = $request->input('instructions');
    $model = $request->input('model', 'gpt-realtime-mini');
    $voice = $request->input('voice', 'ash');

    $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

    if (empty($apiKey)) {
      return response()->json([
        'message' => 'OpenAI API key is not configured.'
      ], 500);
    }

    $response = Http::withToken($apiKey)
      ->acceptJson()
      ->post('https://api.openai.com/v1/realtime/sessions', [
        'model' => $model,
        'instructions' => $instructions,
        'voice' => $voice
      ]);

    if ($response->failed()) {
      return response()->json($response->json() ?? [
        'message' => 'Unable to create OpenAI session.'
      ], $response->status() ?: 500);
    }

    return response()->json($response->json());
  }
}
