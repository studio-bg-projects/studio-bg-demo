<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ImgService;
use Exception;
use Illuminate\Http\Request;

class StorageController extends Controller
{
  protected ImgService $imgService;

  public function __construct()
  {
    $this->imgService = new ImgService();
  }

  public function resizer(string $file, Request $request)
  {
    $imgPath = storage_path('app/public/uploads/' . $file);
    $hop = (int)$request->get('hop') ?? 0;

    if ($hop >= 5) {
      abort('400', 'Too many hops');
    }

    try {
      $this->imgService->resizeFromRequest($imgPath);
    } catch (Exception $e) {
      abort('400', $e->getMessage());
    }

    return redirect()->to(request()->fullUrlWithQuery(['hop' => $hop + 1]));
  }
}
