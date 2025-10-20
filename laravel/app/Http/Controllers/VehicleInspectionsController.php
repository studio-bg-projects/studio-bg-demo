<?php

namespace App\Http\Controllers;

use App\Helpers\FilterAndSort;
use App\Models\GptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class VehicleInspectionsController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $gptRequests = GptRequest::orderBy('id', 'desc')->get();

    return view('vehicle-inspections.index', [
      'gptRequests' => $gptRequests,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $gptRequest = new GptRequest();

    if ($request->isMethod('post')) {
      $gptRequest->fill($request->all());

      $uploadedPhotos = $request->file('photos', []);

      if (!is_array($uploadedPhotos)) {
        $uploadedPhotos = $uploadedPhotos ? [$uploadedPhotos] : [];
      }

      $validPhotos = collect($uploadedPhotos)
        ->filter(fn ($file) => $file instanceof \Illuminate\Http\UploadedFile && $file->isValid())
        ->filter(function ($file) {
          $mimeType = $file->getMimeType();

          return is_string($mimeType) && str_starts_with($mimeType, 'image/');
        })
        ->values();

      $uploadsCount = $validPhotos->count();

      if ($uploadsCount <= 0) {
        $errors->add('fileGroupId', 'Трябва да прикачите поне едно изображение');
      }

      if ($uploadsCount > 10) {
        $errors->add('fileGroupId', 'Към момента не може да качвате повече от 10 изображения');
      }

      if ($errors->isEmpty()) {
        $gptRequest->save();

        $files = [];
        $storageDisk = Storage::disk('public');
        $baseDirectory = 'uploads/vehicle-inspections/' . $gptRequest->id;

        $storageDisk->deleteDirectory($baseDirectory);

        try {
          foreach ($validPhotos as $file) {
            $image = Image::read($file->getRealPath());
            $image->scale(width: 1000, height: 1000);

            $filename = (string)Str::uuid() . '.jpg';
            $relativePath = $baseDirectory . '/' . $filename;

            $stored = $storageDisk->put($relativePath, (string)$image->toJpeg());

            if (!$stored) {
              throw new \RuntimeException('Unable to store the processed image.');
            }

            $files[] = [
              'groupType' => 'vehicle-inspections',
              'groupId' => $gptRequest->id,
              'name' => $filename,
              'urls' => [
                'preview' => $storageDisk->url($relativePath),
                'analize' => $storageDisk->url($relativePath),
              ],
            ];
          }

          $gptRequest->files = [
            'uploads' => $files,
          ];
          $gptRequest->save();

          return redirect('/vehicle-inspections/view/' . $gptRequest->id)
            ->with('success', 'Успешно създадохте нов запис.');
        } catch (\Throwable $exception) {
          $storageDisk->deleteDirectory($baseDirectory);

          if ($gptRequest->exists) {
            $gptRequest->delete();
          }

          $errors->add('fileGroupId', 'Възникна грешка при обработката на изображенията. Моля, опитайте отново.');
        }
      }
    } else {
      $gptRequest->fileGroupId = Str::random(50);
    }

    return view('vehicle-inspections.create', [
      'gptRequest' => $gptRequest,
      'errors' => $errors,
    ]);
  }

  public function view(int $id)
  {
    /* @var $gptRequest GptRequest */
    $gptRequest = GptRequest::where('id', $id)->firstOrFail();

    // View
    return view('vehicle-inspections.view', [
      'gptRequest' => $gptRequest,
      'response' => [],
    ]);
  }

  public function reset(int $id)
  {
    /* @var $gptRequest GptRequest */
    $gptRequest = GptRequest::where('id', $id)->firstOrFail();

    $gptRequest->request = null;
    $gptRequest->response = null;
    $gptRequest->systemMessage = null;
    $gptRequest->responseFormat = null;
    $gptRequest->progressStatus = 0;
    $gptRequest->save();

    return redirect('/vehicle-inspections/view/' . $gptRequest->id)
      ->with('success', 'Записът е нулиран.');
  }

  public function process(int $id)
  {
    $apiUrl = 'https://api.openai.com/v1/responses';
    $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

    /* @var $gptRequest GptRequest */
    $gptRequest = GptRequest::where('id', $id)->firstOrFail();

//    if ($gptRequest->progressStatus) {
//      return redirect('/erp/visual-detector/view/' . $gptRequest->id)
//        ->withErrors(['msg' => 'Този запис е вече анализиран!']);
//    }

    // Fill
    $gptRequest->systemMessage = file_get_contents(storage_path('ai-prompts/system-message.md'));
    $gptRequest->systemMessage .= "\n" . dbConfig('model:additionalPrompt');
    $gptRequest->responseFormat = file_get_contents(storage_path('ai-prompts/response-format.json'));

    // Files
    $photos = [];

    foreach ($gptRequest->uploads as $upload) {
      $imageFile = storage_path('app/public/uploads/' . $upload->groupType . '/' . $upload->groupId . '/' . $upload->name);
      $image = Image::read($imageFile);
      $image->scale(width: 1500, height: 1500);

      $photos[] = [
        'type' => 'input_image',
        'image_url' => 'data:image/jpeg;base64,' . base64_encode($image->encode())
      ];
    }

    // Request
    $request = [
//      'model' => 'gpt-4o-mini',
      'model' => 'gpt-4.5-preview',
//      'temperature' => 2,
//      'max_output_tokens' => 2048,
//      'top_p' => 1,
//      'store' => true,
      'input' => [
        [
          'role' => 'system',
          'content' => [
            [
              'type' => 'input_text',
              'text' => $gptRequest->systemMessage
            ]
          ]
        ],
        [
          'role' => 'assistant',
          'content' => [
            [
              'type' => 'output_text',
              'text' => $gptRequest->responseFormat
            ]
          ]
        ],
        [
          'role' => 'user',
          'content' => $photos,
        ]
      ],
    ];

    // Mark it in progress
    $gptRequest->progressStatus = 1;
    $gptRequest->save();

    // Curl
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    $response = curl_exec($ch);

    if ($response === false) {
      $error = curl_error($ch);
      dd($error);
    } else {
      $gptRequest->request = $request;
      $gptRequest->response = json_decode($response);
      $gptRequest->progressStatus = 2;
      $gptRequest->save();
    }
    curl_close($ch);

    return redirect('/vehicle-inspections/view/' . $gptRequest->id)
      ->with('success', 'Записът беше пуснат за анализ.');
  }

  public function delete(int $id)
  {
    /* @var $gptRequest GptRequest */
    $gptRequest = GptRequest::where('id', $id)->firstOrFail();

    $storageDisk = Storage::disk('public');
    $storageDisk->deleteDirectory('uploads/vehicle-inspections/' . $gptRequest->id);

    $gptRequest->delete();

    return redirect('/vehicle-inspections')
      ->with('success', 'Успешно изтрихте записа.');
  }
}
