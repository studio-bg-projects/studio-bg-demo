<?php

namespace App\Services\Jobs;

use App\Models\DataSourceCategory;
use App\Models\DataSourceMatch;
use App\Models\DataSourceProduct;
use Illuminate\Support\Facades\DB;

class LoadDataSourcesJob extends BaseSyncJob
{
  private ?string $storageDirectory = null;

  public function run(): void
  {
    $this->loadProducts();
    $this->loadCategories();
    $this->out('All good :)');
  }

  public function loadProducts(): void
  {
    $this->out('Downloading the Icecat product archive...');
    $this->processSource(
      'https://data.icecat.biz/export/freexml/EN/files.index.xml.gz',
      'products.xml.gz',
      'products.xml',
      function (string $xmlPath): void {
        $this->out('Importing products into the database...');
        $this->importProductsFromXml($xmlPath);
      }
    );

    // Cleanup matches
    DataSourceMatch::where('hasMatch', '=', 0)->delete();
  }

  private function loadCategories(): void
  {
    $this->out('Downloading the Icecat category archive...');
    $this->processSource(
      'https://data.icecat.biz/export/freexml.int/refs/CategoriesList.xml.gz',
      'categories.xml.gz',
      'categories.xml',
      function (string $xmlPath): void {
        $this->out('Importing categories into the database...');
        $this->importCategoriesFromXml($xmlPath);
      }
    );
  }

  private function importCategoriesFromXml(string $xmlPath): void
  {
    $reader = new \XMLReader();

    if ($reader->open($xmlPath) === false) {
      throw new \RuntimeException('Failed to open the XML file.');
    }

    $batch = [];
    $batchSize = 250;
    $processed = 0;

    while ($reader->read()) {
      if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->name !== 'Category') {
        continue;
      }

      $categoryId = (int)$reader->getAttribute('ID');

      if ($categoryId === 0) {
        continue;
      }

      $parentId = null;
      $nameBg = null;
      $nameEn = null;
      $descriptionBg = null;
      $descriptionEn = null;

      $node = $reader->expand();

      if ($node instanceof \DOMElement) {
        $parentNodes = $node->getElementsByTagName('ParentCategory');

        if ($parentNodes->length > 0) {
          $parentAttribute = $parentNodes->item(0)?->getAttribute('ID');
          $parentId = $parentAttribute !== '' ? (int)$parentAttribute : null;
        }

        foreach ($node->getElementsByTagName('Name') as $nameNode) {
          if (!$nameNode instanceof \DOMElement) {
            continue;
          }

          if ($nameNode->getAttribute('langid') === '21') {
            $nameBg = $nameNode->getAttribute('Value') ?: null;
            continue;
          }

          if ($nameNode->getAttribute('langid') === '1') {
            $nameEn = $nameNode->getAttribute('Value') ?: null;
            continue;
          }
        }

        foreach ($node->getElementsByTagName('Description') as $descriptionNode) {
          if (!$descriptionNode instanceof \DOMElement) {
            continue;
          }

          if ($descriptionNode->getAttribute('langid') === '21') {
            $descriptionBg = $descriptionNode->getAttribute('Value') ?: null;
            continue;
          }

          if ($descriptionNode->getAttribute('langid') === '1') {
            $descriptionEn = $descriptionNode->getAttribute('Value') ?: null;
            continue;
          }
        }
      }

      $batch[] = [
        'categoryId' => $categoryId,
        'parentId' => $parentId,
        'nameBg' => $nameBg,
        'nameEn' => $nameEn,
        'descriptionBg' => $descriptionBg,
        'descriptionEn' => $descriptionEn,
      ];

      if (count($batch) >= $batchSize) {
        $this->storeCategoryBatch($batch);
        $processed += count($batch);
        $this->out(sprintf('Processed categories: %d', $processed));
        $batch = [];
      }
    }

    if (!empty($batch)) {
      $this->storeCategoryBatch($batch);
      $processed += count($batch);
      $this->out(sprintf('Processed categories: %d', $processed));
    }

    $reader->close();
  }

  private function storeCategoryBatch(array $batch): void
  {
    DataSourceCategory::query()
      ->upsert($batch, ['categoryId'], ['parentId', 'nameBg', 'nameEn', 'descriptionBg', 'descriptionEn']);
  }

  private function processSource(string $sourceUrl, string $compressedFilename, string $xmlFilename, callable $handler): void
  {
    $storageDir = $this->getStorageDirectory();
    $compressedPath = $storageDir . DIRECTORY_SEPARATOR . $compressedFilename;
    $xmlPath = $storageDir . DIRECTORY_SEPARATOR . $xmlFilename;

    try {
      $this->downloadFile($sourceUrl, $compressedPath);
      $this->out('Extracting the XML file...');
      $this->decompressGzFile($compressedPath, $xmlPath);
      $handler($xmlPath);
    } finally {
      $this->deleteFileIfExists($compressedPath);
      $this->deleteFileIfExists($xmlPath);
    }
  }

  private function downloadFile(string $sourceUrl, string $destinationPath): void
  {
    $fileHandle = fopen($destinationPath, 'wb');

    if ($fileHandle === false) {
      throw new \RuntimeException('Failed to open the temporary file for writing.');
    }

    $curl = curl_init($sourceUrl);

    if ($curl === false) {
      fclose($fileHandle);
      throw new \RuntimeException('Failed to initialize the download.');
    }

    $username = env('ICECAT_USERNAME');
    $password = env('ICECAT_PASSWORD');

    if ($username === null || $password === null) {
      curl_close($curl);
      fclose($fileHandle);
      throw new \RuntimeException('Icecat username or password is missing.');
    }

    curl_setopt($curl, CURLOPT_FILE, $fileHandle);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));

    $result = curl_exec($curl);

    if ($result === false) {
      $errorMessage = curl_error($curl);
      curl_close($curl);
      fclose($fileHandle);
      throw new \RuntimeException(sprintf('Failed to download the archive: %s', $errorMessage));
    }

    curl_close($curl);
    fclose($fileHandle);
  }

  private function decompressGzFile(string $sourcePath, string $destinationPath): void
  {
    $gzHandle = gzopen($sourcePath, 'rb');
    $outputHandle = fopen($destinationPath, 'wb');

    if ($gzHandle === false || $outputHandle === false) {
      if (is_resource($gzHandle)) {
        gzclose($gzHandle);
      }

      if (is_resource($outputHandle)) {
        fclose($outputHandle);
      }

      throw new \RuntimeException('Failed to extract the files.');
    }

    while (!gzeof($gzHandle)) {
      $chunk = gzread($gzHandle, 1024 * 1024);

      if ($chunk === false) {
        gzclose($gzHandle);
        fclose($outputHandle);
        throw new \RuntimeException('Failed to read from the compressed file.');
      }

      if (fwrite($outputHandle, $chunk) === false) {
        gzclose($gzHandle);
        fclose($outputHandle);
        throw new \RuntimeException('Failed to write the extracted XML file.');
      }
    }

    gzclose($gzHandle);
    fclose($outputHandle);
  }

  private function importProductsFromXml(string $xmlPath): void
  {
    $reader = new \XMLReader();

    if ($reader->open($xmlPath) === false) {
      throw new \RuntimeException('Failed to open the XML file.');
    }

    $productModel = new DataSourceProduct();
    $productConnection = $productModel->getConnectionName() ?? config('database.default');
    $productTable = $productModel->getTable();

    $batch = [];
    $batchSize = 250;
    $processed = 0;

    while ($reader->read()) {
      if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->name !== 'file') {
        continue;
      }

      $productId = (int)$reader->getAttribute('Product_ID');
      $modelName = $reader->getAttribute('Model_Name');
      $categoryId = (int)$reader->getAttribute('Catid');
      $pictureUrl = $reader->getAttribute('HighPic');

      if (!$productId) {
        continue;
      }

      $identifiers = [];
      $node = $reader->expand();
      if ($node instanceof \DOMElement) {
        $eanNodes = $node->getElementsByTagName('EAN_UPC');

        foreach ($eanNodes as $eanNode) {
          $value = $eanNode->getAttribute('Value');

          if ($value !== '') {
            $identifiers[] = $value;
          }
        }
      }

      $identifiers = array_values(array_unique($identifiers));
      $identifiers = array_slice($identifiers, 0, 100);

      if (!$identifiers) {
        continue;
      }

      $batch[] = [
        'externalProductId' => $productId,
        'identifiers' => json_encode($identifiers, JSON_THROW_ON_ERROR),
        'modelName' => $modelName ? substr($modelName, 0, 255) : null,
        'categoryId' => $categoryId,
        'pictureUrl' => $pictureUrl ? substr($pictureUrl, 0, 255) : null,
      ];

      if (count($batch) >= $batchSize) {
        $this->storeBatch($productConnection, $productTable, $batch);
        $processed += count($batch);
        $this->out(sprintf('Processed products: %d', $processed));
        $batch = [];
      }
    }

    if (!empty($batch)) {
      $this->storeBatch($productConnection, $productTable, $batch);
      $processed += count($batch);
      $this->out(sprintf('Processed products: %d', $processed));
    }

    $reader->close();
  }

  private function storeBatch(string $connection, string $table, array $batch): void
  {
    DB::connection($connection)
      ->table($table)
      ->upsert($batch, ['productId'], ['identifiers', 'modelName', 'categoryId', 'pictureUrl']);
  }

  private function getStorageDirectory(): string
  {
    if ($this->storageDirectory !== null) {
      return $this->storageDirectory;
    }

    $directory = storage_path('data-sources');

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
      throw new \RuntimeException('Failed to create the data sources directory.');
    }

    $this->storageDirectory = $directory;

    return $this->storageDirectory;
  }

  private function deleteFileIfExists(string $path): void
  {
    if (is_file($path)) {
      unlink($path);
    }
  }
}
