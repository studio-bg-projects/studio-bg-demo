<?php

namespace App\Http\Controllers;

use App\Helpers\FilterAndSort;
use App\Models\VehicleInspection;
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
    $vehicleInspections = VehicleInspection::orderBy('id', 'desc')->get();

    return view('vehicle-inspections.index', [
      'vehicleInspections' => $vehicleInspections,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $vehicleInspection = new VehicleInspection();

    if ($request->isMethod('post')) {
      $vehicleInspection->fill($request->all());

      $uploadedPhotos = $request->file('photos', []);

      if (!is_array($uploadedPhotos)) {
        $uploadedPhotos = $uploadedPhotos ? [$uploadedPhotos] : [];
      }

      $validPhotos = collect($uploadedPhotos)
        ->filter(fn($file) => $file instanceof \Illuminate\Http\UploadedFile && $file->isValid())
        ->filter(function ($file) {
          $mimeType = $file->getMimeType();

          return is_string($mimeType) && str_starts_with($mimeType, 'image/');
        })
        ->values();

      $uploadsCount = $validPhotos->count();

      if ($uploadsCount <= 0) {
        $errors->add('photos', 'You must attach at least one image.');
      }

      if ($uploadsCount > 10) {
        $errors->add('photos', 'You cannot upload more than 10 images at the moment.');
      }

      if ($errors->isEmpty()) {
        $vehicleInspection->save();

        $files = [];
        $storageDisk = Storage::disk('public');
        $baseDirectory = 'uploads/vehicle-inspections/' . $vehicleInspection->id;

        $storageDisk->deleteDirectory($baseDirectory);

        try {
          foreach ($validPhotos as $file) {
            $image = Image::read($file->getRealPath());
            $image->scale(width: 1500, height: 1500);

            $filename = Str::uuid() . '.jpg';
            $relativePath = $baseDirectory . '/' . $filename;

            $stored = $storageDisk->put($relativePath, (string)$image->toJpeg());

            if (!$stored) {
              throw new \RuntimeException('Unable to store the processed image.');
            }

            $files[] = $filename;
          }

          $vehicleInspection->files = $files;
          $vehicleInspection->save();

          return redirect('/vehicle-inspections/view/' . $vehicleInspection->id)
            ->with('success', 'You successfully created a new record.');
        } catch (\Throwable $exception) {
          $storageDisk->deleteDirectory($baseDirectory);

          if ($vehicleInspection->exists) {
            $vehicleInspection->delete();
          }

          $errors->add('photos', 'An error occurred while processing the images. Please try again.');
        }
      }
    }

    return view('vehicle-inspections.create', [
      'vehicleInspection' => $vehicleInspection,
      'errors' => $errors,
    ]);
  }

  public function view(int $id)
  {
    /* @var $vehicleInspection VehicleInspection */
    $vehicleInspection = VehicleInspection::where('id', $id)->firstOrFail();

    // View
    return view('vehicle-inspections.view', [
      'vehicleInspection' => $vehicleInspection,
      'response' => [],
    ]);
  }

  public function reset(int $id)
  {
    /* @var $vehicleInspection VehicleInspection */
    $vehicleInspection = VehicleInspection::where('id', $id)->firstOrFail();

    $vehicleInspection->request = null;
    $vehicleInspection->response = null;
    $vehicleInspection->systemMessage = null;
    $vehicleInspection->responseFormat = null;
    $vehicleInspection->progressStatus = 0;
    $vehicleInspection->save();

    return redirect('/vehicle-inspections/view/' . $vehicleInspection->id)
      ->with('success', 'The record has been reset.');
  }

  public function process(int $id)
  {
    $apiUrl = 'https://api.openai.com/v1/responses';
    $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

    /* @var $vehicleInspection VehicleInspection */
    $vehicleInspection = VehicleInspection::where('id', $id)->firstOrFail();

    // Fill
    // @todo move out from here
    $vehicleInspection->systemMessage = file_get_contents(storage_path('ai-prompts/vehicle-inspections/system-message.md'));
    $vehicleInspection->responseFormat = file_get_contents(storage_path('ai-prompts/vehicle-inspections/response-format.json'));

    // Files
    $photos = [];

    $storageDisk = Storage::disk('public');
    $baseDirectory = 'uploads/vehicle-inspections/' . $vehicleInspection->id;

    foreach ($vehicleInspection->files as $file) {
      $imageFile = $storageDisk->path($baseDirectory . '/' . $file);
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
      'model' => 'gpt-5-pro',
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
              'text' => $vehicleInspection->systemMessage
            ]
          ]
        ],
        [
          'role' => 'assistant',
          'content' => [
            [
              'type' => 'output_text',
              'text' => $vehicleInspection->responseFormat
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
    $vehicleInspection->progressStatus = 1;
    $vehicleInspection->save();

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
      $vehicleInspection->request = $request;
      $vehicleInspection->response = json_decode($response);
      $vehicleInspection->progressStatus = 2;
      $vehicleInspection->save();
    }
    curl_close($ch);

    return redirect('/vehicle-inspections/view/' . $vehicleInspection->id)
      ->with('success', 'The record has been submitted for analysis.');
  }

  public function delete(int $id)
  {
    /* @var $vehicleInspection VehicleInspection */
    $vehicleInspection = VehicleInspection::where('id', $id)->firstOrFail();

    $storageDisk = Storage::disk('public');
    $storageDisk->deleteDirectory('uploads/vehicle-inspections/' . $vehicleInspection->id);

    $vehicleInspection->delete();

    return redirect('/vehicle-inspections')
      ->with('success', 'You successfully deleted the record.');
  }
}
