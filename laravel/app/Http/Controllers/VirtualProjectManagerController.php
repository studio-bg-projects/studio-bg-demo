<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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
//    $model = $request->input('model', 'gpt-realtime');
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

  public function listTasks(): JsonResponse
  {
    $tasks = Task::orderBy('priority')->orderBy('id')->get();

    return response()->json([
      'success' => true,
      'tasks' => $tasks
    ]);
  }

  public function storeTask(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'title' => ['required', 'string', 'max:255'],
      'priority' => ['nullable', 'integer', 'min:0']
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Unable to create the task with the provided data.',
        'errors' => $validator->errors()
      ], 422);
    }

    $data = $validator->validated();
    $task = Task::create([
      'title' => $data['title'],
      'priority' => $data['priority'] ?? 0
    ]);

    return response()->json([
      'success' => true,
      'task' => $task->fresh()
    ], 201);
  }

  public function updateTask(Request $request, Task $task): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'title' => ['sometimes', 'required', 'string', 'max:255'],
      'priority' => ['sometimes', 'required', 'integer', 'min:0']
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Unable to update the task with the provided data.',
        'errors' => $validator->errors()
      ], 422);
    }

    $data = $validator->validated();

    if (empty($data)) {
      return response()->json([
        'success' => false,
        'message' => 'No changes were provided for the task.'
      ], 422);
    }

    $task->fill($data);
    $task->save();

    return response()->json([
      'success' => true,
      'task' => $task->fresh()
    ]);
  }

  public function deleteTask(Task $task): JsonResponse
  {
    $task->delete();

    return response()->json([
      'success' => true
    ]);
  }
}
