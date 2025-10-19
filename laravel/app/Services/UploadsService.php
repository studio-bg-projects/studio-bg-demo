<?php

namespace App\Services;

use App\Enums\UploadGroupType;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;

class UploadsService
{
  public static function map(UploadGroupType $type): stdClass
  {
    $object = new stdClass();

    switch ($type->value) {
      case UploadGroupType::Categories->value:
      {
        $object->table = 'categories';
        $object->tableField = 'fileGroupId';
        $object->directory = 'categories';
        break;
      }
      case UploadGroupType::Documents->value:
      {
        $object->table = 'documents';
        $object->tableField = 'fileGroupId';
        $object->directory = 'documents';
        break;
      }
      case UploadGroupType::Manufacturers->value:
      {
        $object->table = 'manufacturers';
        $object->tableField = 'fileGroupId';
        $object->directory = 'manufacturers';
        break;
      }
      case UploadGroupType::ProductDownloads->value:
      {
        $object->table = 'products';
        $object->tableField = 'downloadsGroupId';
        $object->directory = 'productDownloads';
        break;
      }
      case UploadGroupType::Products->value:
      {
        $object->table = 'products';
        $object->tableField = 'fileGroupId';
        $object->directory = 'products';
        break;
      }
      case UploadGroupType::SalesRepresentatives->value:
      {
        $object->table = 'salesRepresentatives';
        $object->tableField = 'fileGroupId';
        $object->directory = 'salesRepresentatives';
        break;
      }
      case UploadGroupType::Banners->value:
      {
        $object->table = null;
        $object->tableField = null;
        $object->directory = 'banners';
        break;
      }
      case UploadGroupType::Demo->value:
      {
        $object->table = 'demos';
        $object->tableField = 'fileGroupId';
        $object->directory = 'demos';
        break;
      }
      case UploadGroupType::StorageEntriesIncomeInvoices->value:
      {
        $object->table = 'storageEntriesIncomeInvoices';
        $object->tableField = 'fileGroupId';
        $object->directory = 'storageEntriesIncomeInvoices';
        break;
      }
      case UploadGroupType::IncomeCreditMemo->value:
      {
        $object->table = 'incomeCreditMemos';
        $object->tableField = 'fileGroupId';
        $object->directory = 'incomeCreditMemos';
        break;
      }
      default:
      {
        throw new Exception(sprintf('Unknown upload type: %s', $type->value));
      }
    }

    return $object;
  }

  public function uploadFile(UploadGroupType $groupType, string $groupId, $file): string
  {
    $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();

    $subDirectory = "uploads/{$groupType->value}/{$groupId}";
    $file->storeAs($subDirectory, $fileName, 'public');

    return $fileName;
  }

  public function putFile(UploadGroupType $groupType, string $groupId, string $fileName, $content)
  {
    Storage::disk('public')->put("uploads/$groupType->value/$groupId/$fileName", $content);
  }

  public function deleteFile(UploadGroupType $groupType, string $groupId, string $fileName): void
  {
    $filePath = 'uploads/' . $groupType->value . '/' . $groupId . '/' . $fileName;

    if (Storage::disk('public')->exists($filePath)) {
      Storage::disk('public')->delete($filePath);
    }

    // Check if the parent directory is empty and remove it
    foreach ([dirname($filePath), dirname($filePath, 2)] as $dir) {
      if (Storage::disk('public')->exists($dir) && count(Storage::disk('public')->allFiles($dir)) === 0 && count(Storage::disk('public')->allDirectories($dir)) === 0) {
        Storage::disk('public')->deleteDirectory($dir);
      }
    }
  }
}
