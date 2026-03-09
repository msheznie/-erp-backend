<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class DynamicExcelExport implements FromArray, WithStyles, WithColumnFormatting, WithCustomStartCell, WithEvents
{
    protected $data;
    protected $array;
    protected $fontFamily;
    protected $columnFormat;
    protected $startCell;
    protected $dataType;

    public function __construct($data, $array = null, $fontFamily = 'Calibri', $columnFormat = null, $startCell = 'A1', $dataType = 1)
    {
        $this->data = $data;
        $this->array = $array ?? [];
        $this->fontFamily = $fontFamily;
        $this->columnFormat = $columnFormat;
        $this->startCell = $startCell;
        $this->dataType = $dataType;
    }

    public function array(): array
    {
        // Sanitize data - remove '=' characters to prevent formula errors
        $sanitized = [];
        foreach ($this->data as $record) {
            $sanitizedRecord = [];
            foreach ($record as $key => $value) {
                if (is_string($value)) {
                    $sanitizedRecord[$key] = str_replace(['='], '', $value);
                } else {
                    $sanitizedRecord[$key] = $value;
                }
            }
            $sanitized[] = $sanitizedRecord;
        }
        return $sanitized;
    }

    public function startCell(): string
    {
        return $this->startCell;
    }

    public function columnFormats(): array
    {
        return $this->columnFormat ?? [];
    }

    public function styles(Worksheet $sheet)
    {
        // Set default font for entire sheet
        $sheet->getStyle('A1:Z1000')->getFont()->setName($this->fontFamily)->setSize(11);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                if ($lastRow > 0 && $lastColumn) {
                    // Apply font to all cells
                    $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($this->fontFamily);
                    
                    // Apply header row styling if dataType is 1
                    if ($this->dataType == 1 && isset($this->array['title'])) {
                        $startRow = Coordinate::columnIndexFromString($this->startCell[1] ?? '1');
                        if ($startRow > 0) {
                            $headerRow = $startRow;
                            $headerRange = 'A' . $headerRow . ':' . $lastColumn . $headerRow;
                            
                            $sheet->getStyle($headerRange)->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'size' => 12,
                                    'name' => $this->fontFamily,
                                ],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '827e7e'],
                                ],
                            ]);
                        }
                    }
                    
                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }
                }
            },
        ];
    }
}
