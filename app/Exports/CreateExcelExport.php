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
}

/**
 * Wrapper class to mimic the old Excel API
 */
class ExcelWrapper
{
    protected $spreadsheet;
    protected $sheets = [];

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function sheet($name, callable $callback)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        if ($name !== $sheet->getTitle()) {
            $sheet->setTitle($name);
        }
        
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

    public function __construct(Worksheet $worksheet)
    {
        $this->worksheet = $worksheet;
    }

    public function fromArray($source, $nullValue = null, $startCell = 'A1', $strictNullComparison = false, $calculateCellValues = true)
    {
        $this->worksheet->fromArray($source, $nullValue, $startCell, $strictNullComparison, $calculateCellValues);
    }

    public function setAutoSize($columns = true)
    {
        if ($columns === true) {
            foreach (range('A', $this->worksheet->getHighestColumn()) as $col) {
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

    public function loadView($view, $data = [])
    {
        $html = view($view, $data)->render();
        
        // Use PhpSpreadsheet's HTML reader to import the HTML
        try {
            $reader = new Html();
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_html_');
            file_put_contents($tempFile, $html);
            
            // Read HTML into a temporary spreadsheet
            $tempSpreadsheet = $reader->load($tempFile);
            $tempWorksheet = $tempSpreadsheet->getActiveSheet();
            
            // Copy data from temp worksheet to current worksheet
            $highestRow = $tempWorksheet->getHighestRow();
            $highestColumn = $tempWorksheet->getHighestColumn();
            
            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $tempWorksheet->getCell($col . $row)->getValue();
                    $this->worksheet->setCellValue($col . $row, $cellValue);
                    
                    // Copy styles
                    $tempStyle = $tempWorksheet->getStyle($col . $row);
                    $this->worksheet->duplicateStyle($tempStyle, $col . $row);
                }
            }
            
            unlink($tempFile);
        } catch (\Exception $e) {
            // Fallback: if HTML reader fails, just set the HTML as text in first cell
            // This is not ideal but prevents errors
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
