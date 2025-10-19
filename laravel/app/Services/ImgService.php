<?php

namespace App\Services;

use Exception;
use Intervention\Image\Laravel\Facades\Image;

class ImgService
{
  public array $extensions = ['jpg', 'jpeg', 'gif', 'png', 'tiff'];

  public function resizeFromRequest(string $imgPath)
  {
    $sizes = [
      'tiny' => ['width' => 80, 'height' => 80],
      'preview' => ['width' => 500, 'height' => 500],
    ];

    $info = pathinfo($imgPath);

    $fileParts = explode('-', $info['filename']);
    $sizeId = array_pop($fileParts);
    $fileName = implode('-', $fileParts);

    $originalFilePath = $info['dirname'] . '/' . $fileName . '.' . $info['extension'];

    if (!is_file($originalFilePath)) {
      throw new Exception(sprintf('Original file not found'));
    }

    if (is_file($imgPath)) {
      throw new Exception(sprintf('New file is already generated'));
    }

    if (!in_array($info['extension'], $this->extensions)) {
      throw new Exception(sprintf('Invalid image extension: %s', $info['extension']));
    }

    if (!isset($sizes[$sizeId])) {
      throw new Exception(sprintf('Invalid image size: %s', $sizeId));
    }

    $width = $sizes[$sizeId]['width'];
    $height = $sizes[$sizeId]['height'];
    $image = Image::read($originalFilePath);
    $image->scale(width: $width, height: $height);

    $image->save($imgPath);
  }

  public static function url(?string $url, $suffix = 'preview')
  {
    return preg_replace('/\.(jpg|jpeg|png|gif)$/i', '-' . $suffix . '.$1', $url);
  }
}
