<?php

namespace App\Http\Controllers\Erp;

use App\Enums\UploadGroupType;
use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Services\UploadsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class UploadsController extends Controller
{
  protected array $forbiddenExtensions = ['php'];

  protected UploadsService $uploadsService;

  public function __construct()
  {
    $this->uploadsService = new UploadsService();
    parent::__construct();
  }

  public function index($groupType, string $groupId)
  {
    /* @var $files Upload[] */
    $files = Upload::where('groupType', $groupType)
      ->where('groupId', $groupId)
      ->orderBy('sortOrder', 'asc')
      ->get();

    return $files;
  }

  public function sort(Request $request, string $groupType, string $groupId)
  {
    $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'integer|min:1',
    ]);

    $ids = $request->input('ids');

    /* @var $files Upload[] */
    $files = Upload::where('groupType', $groupType)
      ->where('groupId', $groupId)
      ->whereIn('id', $ids)
      ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
      ->get();

    foreach ($files as $idx => $file) {
      $file->update(['sortOrder' => $idx + 1]);
    }
  }

  public function upload(Request $request, string $groupType, string $groupId)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $file = new Upload();

    $validator = Validator::make($request->all(), [
      'up' => ['required', 'file'],
    ]);

    $errors->merge($validator->errors());

    if ($errors->isEmpty()) {
      $uploadGroupType = UploadGroupType::from($groupType);

      // File
      $uploadedFile = $request->file('up');
      $fileName = $this->uploadsService->uploadFile($uploadGroupType, $groupId, $uploadedFile);

      $file->groupType = $uploadGroupType;
      $file->groupId = $groupId;
      $file->name = $fileName;
      $file->hash = md5_file($uploadedFile->getRealPath());
      $file->size = $uploadedFile->getSize();
      $file->originalName = $uploadedFile->getClientOriginalName();
      $file->extension = $uploadedFile->getClientOriginalExtension();
      $file->mimeType = $uploadedFile->getMimeType();
      $file->sortOrder = $request->input('sortOrder', 0);

      if (in_array($file->extension, $this->forbiddenExtensions)) {
        $errors->add('up', 'Forbidden file type.');
      }

      // Store
      if ($errors->isEmpty()) {
        $file->save();
      }
    }

    return response()->json([
      'errors' => $errors,
      'file' => $file,
    ], $errors->isEmpty() ? 201 : 400);
  }

  public function delete(string $groupType, string $groupId, int $fileId)
  {
    /* @var $file Upload */
    $file = Upload::where('groupType', $groupType)
      ->where('groupId', $groupId)
      ->where('id', $fileId)
      ->firstOrFail();

    $file->delete();

    $this->uploadsService->deleteFile($file->groupType, $file->groupId, $file->name);
  }
}
