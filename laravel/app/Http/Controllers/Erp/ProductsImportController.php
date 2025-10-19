<?php

namespace App\Http\Controllers\Erp;

use App\Enums\ProductUsageStatus;
use App\Enums\ProductSource;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Models\Product;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsImportController extends Controller
{
  protected array $columns = [
    'nameBg' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'nameEn' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'descriptionBg' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'descriptionEn' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'quantity' => [
      'type' => 'number',
      'forceCast' => 'int',
      'nullable' => false,
      'customAction' => null,
    ],
    'price' => [
      'type' => 'price',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => 'skipNa', // Skip (set current value) when the value is N/A
    ],
    'purchasePrice' => [
      'type' => 'price',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => 'skipNa', // Skip (set current value) when the value is N/A
    ],
    'mpn' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'ean' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'weight' => [
      'type' => 'decimal',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'width' => [
      'type' => 'decimal',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'height' => [
      'type' => 'decimal',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'length' => [
      'type' => 'decimal',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'warrantyPeriod' => [
      'type' => 'number',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'deliveryDays' => [
      'type' => 'number',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'onStock' => [
      'type' => 'number',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
    'manufacturer' => [
      'type' => 'text',
      'forceCast' => null,
      'nullable' => true,
      'customAction' => null,
    ],
  ];

  public function index()
  {
    return redirect('/erp/products-import/from-excel');
  }

  public function fromExcel(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    if ($request->file('importFile') || $request->has('base64')) {
      // Get file
      {
        if ($request->file('importFile')) {
          $file = $request->file('importFile');
          $fileContent = file_get_contents($file->getRealPath());
          $base64 = base64_encode($fileContent);
          $applyChanges = false;
        } else {
          $base64 = $request->input('base64');
          $fileContent = base64_decode($base64);
          $applyChanges = true;
        }

        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $meta = stream_get_meta_data($tempFile);
        $tempPath = $meta['uri'];
      }

      $spreadsheet = IOFactory::load($tempPath);
      $sheet = $spreadsheet->getActiveSheet();
      $rows = $sheet->toArray(null, true, false, true);
      fclose($tempFile); // Cleanup the file

      $dbNow = DB::select('SELECT NOW() AS now')[0]->now;

      $headers = array_map('trim', array_shift($rows));
      $rs = [];

      $manufacturersMap = Manufacturer::all()->keyBy(function ($item) {
        return mb_strtolower($item->name);
      });

      foreach ($rows as $row) {
        $action = null;
        $data = array_combine($headers, array_values($row));
        $changes = [];
        $originals = [];
        $error = null;

        if (empty($data['ean']) && empty($data['mpn'])) {
          $action = 'error';
          $error = 'Липсва ean или mpn';
        } else {
          // Try to find existing product
          /* @var $product Product */
          if (!empty($data['ean'])) {
            $product = Product::where('ean', $data['ean'])->first();
          } elseif (!empty($data['mpn'])) {
            $product = Product::where('mpn', $data['mpn'])->first();
          } else {
            $product = null;
          }

          if (!$product) {
            $product = new Product();
            $product->usageStatus = ProductUsageStatus::Draft->value;
            $product->source = ProductSource::ProductsImport->value;
          }

          /*
          if ($product->ean && empty($data['ean'])) {
            unset($data['ean']);
          }
          if ($product->mpn && empty($data['mpn'])) {
            unset($data['mpn']);
          }
          */

          // Cast & fix
          foreach ($this->columns as $columnKey => $columnSpec) {
            // Make it not null
            if (empty($columnSpec['nullable'])) {
              if (!isset($data[$columnKey])) {
                $data[$columnKey] = '';
              }
            }

            // Continue when there is no data
            if (!array_key_exists($columnKey, $data)) {
              continue;
            }

            // Custom action
            if (!empty($columnSpec['customAction'])) {
              // Skip (set current value) when the value is N/A
              if ($columnSpec['customAction'] === 'skipNa') {
                if (in_array(strtoupper($data[$columnKey]), ['#N/A', 'N/A', 'NA'])) {
                  $data[$columnKey] = $product->{$columnKey};
                }
              }
            }

            // Cast
            if (!empty($columnSpec['forceCast'])) {
              settype($data[$columnKey], $columnSpec['forceCast']);
            }
          }

          // Fill the changes
          $product->fill($data);
          $changes = $product->getDirty();

          // Set originals
          foreach ($changes as $key => $value) {
            $originals[$key] = $product->getOriginal($key);
          }

          // Manufacturer
          $data['manufacturer'] = trim($data['manufacturer']);

          if (array_key_exists('manufacturer', $data)) {
            $manufacturersKey = strtolower($data['manufacturer']);

            if ($data['manufacturer'] && !isset($manufacturersMap[$manufacturersKey])) {
              $manufacturer = new Manufacturer();
              $manufacturer->name = $data['manufacturer'];
              $manufacturer->save();
              $manufacturersMap[strtolower($manufacturer->name)] = $manufacturer;
            }

            $manufacturer = $manufacturersMap[$manufacturersKey] ?? null;
            $oldManufacturerName = $product->manufacturer?->name;
            $product->manufacturerId = $manufacturer?->id;

            if (array_key_exists('manufacturerId', $product->getDirty())) {
              $changes['manufacturer'] = $oldManufacturerName;
            }
          }

          if ($product->id) {
            if ($changes) {
              $action = 'update';
            }
          } else {
            $action = 'create';
            $changes = $headers;
          }

          // Seva/add
          if ($applyChanges) {
            $product->save();
          }
        }

        if (!$action) {
          continue;
        }

        $rs[] = [
          'data' => $data,
          'changes' => $changes,
          'originals' => $originals,
          'error' => $error,
          'action' => $action,
        ];
      }

      if ($applyChanges) {
        return redirect('/erp/products?filter[updatedAt]=' . $dbNow . '&op[updatedAt]=gte')
          ->with('success', 'Успешно нанесохте промените.');
      }

      return view('erp.products-import.from-excel-set-data', [
        'headers' => $headers,
        'rs' => $rs,
        'errors' => $errors,
        'base64' => $base64,
      ]);
    }

    // Default step - form
    return view('erp.products-import.from-excel-form', [
      'errors' => $errors,
    ]);
  }

  public function exportAll()
  {
    /* @var $products Product[] */
    $products = Product::with('manufacturer')->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $currency = dbConfig('currency:symbol');

    $columnKeys = array_keys($this->columns);

    // Headings
    $sheet->fromArray([$columnKeys], null, 'A1');
    $sheet->freezePane('A2'); // Sticky freeze

    // Data
    $rowIndex = 2;
    foreach ($products as $product) {
      $sheet->fromArray([[
        $product->nameBg,
        $product->nameEn,
        $product->descriptionBg,
        $product->descriptionEn,
        $product->quantity,
        $product->price,
        $product->purchasePrice,
        $product->mpn,
        $product->ean,
        $product->weight,
        $product->width,
        $product->height,
        $product->length,
        $product->warrantyPeriod,
        $product->deliveryDays,
        $product->onStock ? '1' : '0',
        optional($product->manufacturer)->name,
      ]], null, 'A' . $rowIndex);

      $rowIndex++;
    }

    // Style
    $lastColumn = $sheet->getHighestColumn();
    $lastRow = $sheet->getHighestRow();

    // Heading style
    $headingStyle = [
      'font' => ['bold' => true],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFDDEBF7'],
      ],
    ];
    $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headingStyle);

    // Border & alignment
    $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
      'alignment' => [
        'wrapText' => true,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ],
      'borders' => [
        'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
        ],
      ],
    ]);

    // Width
    foreach (range(1, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn)) as $colIndex) {
      $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
      $sheet->getColumnDimension($col)->setWidth(33);  // ~200px = 33 Excel units
    }

    // Height
    for ($row = 1; $row <= $lastRow; $row++) {
      $sheet->getRowDimension($row)->setRowHeight(45);
    }

    // Cast / Set type
    foreach ($this->columns as $columnKey => $columnSpec) {
      $colIndex = array_search($columnKey, $columnKeys); // 0-based
      $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
      $range = "{$columnLetter}2:{$columnLetter}{$lastRow}";
      $style = $sheet->getStyle($range);

      switch ($columnSpec['type']) {
        case 'price':
        {
          $style->getNumberFormat()->setFormatCode("#,##0.00 \"" . $currency . "\"");
          break;
        }
        case 'number':
        {
          $style->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
          break;
        }
        case 'decimal':
        {
          $style->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
          break;
        }
        default:
        {
          for ($row = 2; $row <= $lastRow; $row++) {
            $cell = $sheet->getCell("{$columnLetter}{$row}");
            $cell->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          }
        }
      }
    }

    // Download
    $fileName = 'products-export-' . date('Ymdhis') . '.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
  }
}
