<?php

namespace App\Services\Jobs;

use App\Models\Upload;
use App\Services\UploadsService;
use FilesystemIterator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupUploadsJob extends BaseJob
{
  protected UploadsService $uploadsService;

  public function __construct()
  {
    $this->uploadsService = new UploadsService();
  }

  public function run(): void
  {
    $this->cleanupDb();

    $directory = 'uploads';
    $this->cleanupFiles($directory);
    $this->cleanupDirs($directory);

    $this->out('All good :)');
  }

  protected function cleanupDb(): void
  {
    foreach (\App\Enums\UploadGroupType::cases() as $type) {
      $upload = UploadsService::map($type);

      if (!$upload->table) {
        continue;
      }

      $query = 'DELETE FROM `uploads`
        WHERE `groupType` = "' . $type->value . '"
        AND `groupId` NOT IN (SELECT `' . $upload->tableField . '` FROM `' . $upload->table . '`)
        AND `createdAt` < (DATE_SUB(CURDATE(), INTERVAL 12 HOUR));';
      $affected = DB::delete($query);
      if ($affected) {
        $this->out(sprintf('Delete %s records without parent relation for %s', $affected, $type->value));
      }
    }
  }

  protected function cleanupFiles($directory): void
  {
    /* @var $dbFiles Upload[] */
    $dbFiles = Upload::all(['groupType', 'groupId', 'name'])->toArray();

    // Map exists
    $existFiles = [];
    foreach ($dbFiles as $dbFile) {
      $existFiles[$dbFile['groupType']][$dbFile['groupId']][$dbFile['name']] = true;
    }

    // Get uploads
    $uploads = [];

    foreach (Storage::disk('public')->allFiles($directory) as $filePath) {
      $chunks = explode('/', $filePath);
      if (count($chunks) !== 4) {
        continue;
      }

      list(, $groupType, $groupId, $fileName) = $chunks;

      $uploads[] = [
        'path' => $filePath,
        'groupType' => $groupType,
        'groupId' => $groupId,
        'fileName' => $fileName,
      ];
    }

    // Collect deletes
    $delete = [];
    foreach ($uploads as $upload) {
      if (isset($existFiles[$upload['groupType']][$upload['groupId']][$upload['fileName']])) {
        continue;
      }

      $delete[] = $upload;
    }

    // Delete files
    foreach ($delete as $item) {
      // @todo delete the files older than 1 hour
      Storage::disk('public')->delete($item['path']);
    }
  }

  protected function cleanupDirs($directory): void
  {
    $directories = Storage::disk('public')->allDirectories($directory);
    foreach ($directories as $directory) {
      $fi = new FilesystemIterator(Storage::disk('public')->path($directory), FilesystemIterator::SKIP_DOTS);
      if (!iterator_count($fi)) {
        Storage::disk('public')->deleteDirectory($directory);
      }
    }
  }
}
