<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Html;

class CreateExcelExport
{
    use Exportable;

    protected $callback;
    protected $writerType;

    public function __construct(callable $callback, $writerType = 'xlsx')
    {
        $this->callback = $callback;
        $this->writerType = $writerType;
    }

    /**
     * Generate Excel content as string using the callback
     */
    public function getContent(): string
    {
        $spreadsheet = new Spreadsheet();
        
        // Create a wrapper that mimics the old Excel API
        $excelWrapper = new ExcelWrapper($spreadsheet);
        
        // Call the callback with the wrapper
        call_user_func($this->callback, $excelWrapper);
        
        // Write to string
        $writer = $this->getWriter($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        
        return $content;
    }

    protected function getWriter(Spreadsheet $spreadsheet)
    {
        switch (strtolower($this->writerType)) {
            case 'csv':
                return new Csv($spreadsheet);
            case 'xls':
                return new Xls($spreadsheet);
            case 'xlsx':
            default:
                return new Xlsx($spreadsheet);
        }
    }

    /**
     * Build spreadsheet via legacy callback and return a download response.
     * Use this instead of \Excel::create($fileName, $callback)->download($type).
     *
     * @param  string  $fileName  Base filename without extension
     * @param  callable  $callback  Receives ExcelWrapper, e.g. function ($excel) { $excel->sheet('Sheet', function ($sheet) { ... }); }
     * @param  string  $writerType  'xlsx', 'xls', or 'csv'
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function download(string $fileName, callable $callback, string $writerType = 'xlsx'): \Symfony\Component\HttpFoundation\Response
    {
        $export = new self($callback, $writerType);
        $content = $export->getContent();
        $ext = strtolower($writerType);
        $mimeTypes = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv',
        ];
        $mime = $mimeTypes[$ext] ?? $mimeTypes['xlsx'];
        $downloadName = $fileName . '.' . $ext;

        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
        ]);
    }
}

/**
 * Wrapper class to mimic the old Excel API
 */
class ExcelWrapper
{
    protected $spreadsheet;

    protected int $sheetIndex = 0;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function sheet($name, callable $callback)
    {
        if ($this->sheetIndex > 0) {
            $this->spreadsheet->createSheet();
        }
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($name);
        $this->sheetIndex++;

        $sheetWrapper = new SheetWrapper($sheet);
        call_user_func($callback, $sheetWrapper);

        return $this;
    }

    public function getActiveSheet()
    {
        return new SheetWrapper($this->spreadsheet->getActiveSheet());
    }
}

/**
 * Wrapper class to mimic the old Sheet API
 */
class SheetWrapper
{
    protected $worksheet;

    /** @var array{view: string, data: array}|null Last loadView args for chainable with() */
    protected $lastLoadView = null;

    public function __construct(Worksheet $worksheet)
    {
        $this->worksheet = $worksheet;
    }

    /**
     * Chainable: merge extra data and re-load the last view. Supports with('key', $value) or with(['k' => 'v']).
     */
    public function with($key, $value = null)
    {
        $extra = is_array($key) ? $key : [$key => $value];
        if ($this->lastLoadView !== null) {
            $merged = array_merge($this->lastLoadView['data'], $extra);

            return $this->loadView($this->lastLoadView['view'], $merged);
        }

        return $this;
    }

    public function fromArray($source, $nullValue = null, $startCell = 'A1', $strictNullComparison = false, $hasHeaderRow = true)
    {
        // PhpSpreadsheet's fromArray only accepts array; convert Collection to array
        if ($source instanceof \Illuminate\Support\Collection) {
            $source = $source->all();
        }
        if (!is_array($source)) {
            return;
        }
        if (empty($source)) {
            return;
        }

        // Check if this is an associative array (has string keys)
        $firstRow = reset($source);
        $isAssociative = is_array($firstRow) && !isset($firstRow[0]);
        
        if ($hasHeaderRow && $isAssociative) {
            // Extract headers from array keys and insert them first
            $headers = array_keys($firstRow);
            
            // Convert values to indexed arrays
            $dataRows = [];
            foreach ($source as $row) {
                $dataRows[] = array_values($row);
            }
            
            // Insert headers first, then data
            $this->worksheet->fromArray([$headers], $nullValue, $startCell, $strictNullComparison);
            
            // Calculate next row for data
            $coordinates = Coordinate::coordinateFromString($startCell);
            $dataStartRow = $coordinates[1] + 1;
            $dataStartCell = $coordinates[0] . $dataStartRow;
            
            $this->worksheet->fromArray($dataRows, $nullValue, $dataStartCell, $strictNullComparison);
        } else {
            // Regular indexed array, insert as-is
            $this->worksheet->fromArray($source, $nullValue, $startCell, $strictNullComparison);
        }
    }

    public function setAutoSize($columns = true)
    {
        if ($columns === true) {
            $highestColumn = $this->worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            for ($i = 1; $i <= $highestColumnIndex; $i++) {
                $col = Coordinate::stringFromColumnIndex($i);
                $this->worksheet->getColumnDimension($col)->setAutoSize(true);
            }
        } elseif (is_array($columns)) {
            foreach ($columns as $col) {
                $this->worksheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
    }

    public function setStyle(array $style)
    {
        // Apply style to entire sheet
        if (isset($style['font'])) {
            $font = $this->worksheet->getStyle('A1:Z1000')->getFont();
            if (isset($style['font']['name'])) {
                $font->setName($style['font']['name']);
            }
            if (isset($style['font']['size'])) {
                $font->setSize($style['font']['size']);
            }
        }
    }

    public function row($row, callable $callback)
    {
        $rowWrapper = new RowWrapper($this->worksheet, $row);
        call_user_func($callback, $rowWrapper);
    }

    public function cell($cell, callable $callback)
    {
        $cellWrapper = new CellWrapper($this->worksheet, $cell);
        call_user_func($callback, $cellWrapper);
    }

    public function cells($range, callable $callback)
    {
        $cellsWrapper = new CellsWrapper($this->worksheet, $range);
        call_user_func($callback, $cellsWrapper);
    }

    public function appendRow(array $row)
    {
        $highestRow = $this->worksheet->getHighestRow();
        $this->worksheet->fromArray([$row], null, 'A' . ($highestRow + 1));
    }

    public function getHighestRow()
    {
        return $this->worksheet->getHighestRow();
    }

    public function getHighestColumn()
    {
        return $this->worksheet->getHighestColumn();
    }

    public function getStyle($range)
    {
        return $this->worksheet->getStyle($range);
    }

    public function setRightToLeft($value)
    {
        $this->worksheet->setRightToLeft($value);
    }

    public function setColumnFormat(array $formats)
    {
        foreach ($formats as $column => $format) {
            $this->worksheet->getStyle($column . ':' . $column)->getNumberFormat()->setFormatCode($format);
        }
    }

    public function setWidth($column, $width)
    {
        $this->worksheet->getColumnDimension($column)->setWidth($width);
    }

    public function mergeCells($range)
    {
        $this->worksheet->mergeCells($range);
    }

    public function loadView($view, $data = [])
    {
        $this->lastLoadView = ['view' => $view, 'data' => $data];
        $html = view($view, $data)->render();

        // Use loadFromString to avoid temp file and 2048-byte minimum that load($filename) has
        try {
            $reader = new Html();
            $tempSpreadsheet = $reader->loadFromString($html);
            $tempWorksheet = $tempSpreadsheet->getActiveSheet();

            // Copy data from temp worksheet to current worksheet
            $highestRow = $tempWorksheet->getHighestRow();
            $highestColumn = $tempWorksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            for ($row = 1; $row <= $highestRow; $row++) {
                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $cellRef = $col . $row;
                    $cellValue = $tempWorksheet->getCell($cellRef)->getValue();
                    $this->worksheet->setCellValue($cellRef, $cellValue);

                    // Copy styles
                    $tempStyle = $tempWorksheet->getStyle($cellRef);
                    $this->worksheet->duplicateStyle($tempStyle, $cellRef);
                }
            }
        } catch (\Exception $e) {
            // Fallback: if HTML reader fails, set the HTML as text in first cell
            $this->worksheet->setCellValue('A1', strip_tags($html));
        }

        return $this;
    }

    public function getDelegate()
    {
        return $this->worksheet->getParent();
    }
}

class RowWrapper
{
    protected $worksheet;
    protected $row;

    public function __construct(Worksheet $worksheet, $row)
    {
        $this->worksheet = $worksheet;
        $this->row = $row;
    }

    public function setBackground($color)
    {
        $highestColumn = $this->worksheet->getHighestColumn();
        $this->worksheet->getStyle('A' . $this->row . ':' . $highestColumn . $this->row)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB(str_replace('#', '', $color));
    }

    public function setFont(array $font)
    {
        $highestColumn = $this->worksheet->getHighestColumn();
        $style = $this->worksheet->getStyle('A' . $this->row . ':' . $highestColumn . $this->row)->getFont();
        
        if (isset($font['family'])) {
            $style->setName($font['family']);
        }
        if (isset($font['size'])) {
            $style->setSize($font['size']);
        }
        if (isset($font['bold'])) {
            $style->setBold($font['bold']);
        }
    }

    public function setFontColor($color)
    {
        $highestColumn = $this->worksheet->getHighestColumn();
        $this->worksheet->getStyle('A' . $this->row . ':' . $highestColumn . $this->row)
            ->getFont()
            ->getColor()
            ->setRGB(str_replace('#', '', $color));
    }

    public function setAlignment($alignment)
    {
        $highestColumn = $this->worksheet->getHighestColumn();
        $style = $this->worksheet->getStyle('A' . $this->row . ':' . $highestColumn . $this->row);
        
        switch (strtolower($alignment)) {
            case 'left':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                break;
            case 'right':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'center':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
        }
    }
}

class CellWrapper
{
    protected $worksheet;
    protected $cell;

    public function __construct(Worksheet $worksheet, $cell)
    {
        $this->worksheet = $worksheet;
        $this->cell = $cell;
    }

    public function setValue($value)
    {
        $this->worksheet->setCellValue($this->cell, $value);
    }

    public function setFont(array $font)
    {
        $style = $this->worksheet->getStyle($this->cell)->getFont();
        
        if (isset($font['family'])) {
            $style->setName($font['family']);
        }
        if (isset($font['size'])) {
            $style->setSize($font['size']);
        }
        if (isset($font['bold'])) {
            $style->setBold($font['bold']);
        }
    }

    public function setAlignment($alignment)
    {
        $style = $this->worksheet->getStyle($this->cell);
        
        switch (strtolower($alignment)) {
            case 'left':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                break;
            case 'right':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'center':
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
        }
    }
}

class CellsWrapper
{
    protected $worksheet;
    protected $range;

    public function __construct(Worksheet $worksheet, $range)
    {
        $this->worksheet = $worksheet;
        $this->range = $range;
    }

    public function setFont(array $font)
    {
        $style = $this->worksheet->getStyle($this->range)->getFont();
        
        if (isset($font['name'])) {
            $style->setName($font['name']);
        }
        if (isset($font['size'])) {
            $style->setSize($font['size']);
        }
        if (isset($font['bold'])) {
            $style->setBold($font['bold']);
        }
    }
}
