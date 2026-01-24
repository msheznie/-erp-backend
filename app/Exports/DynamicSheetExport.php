<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class DynamicSheetExport implements FromArray, WithStyles, WithEvents
{
    protected $data;
    protected $array;
    protected $fontFamily;
    protected $sheetName;
    protected $parentIdList;
    protected $nonParentIdList;

    public function __construct($data, $array = null, $fontFamily = 'Calibri', $sheetName = 'Sheet1')
    {
        $this->data = $data;
        $this->array = $array ?? [];
        $this->fontFamily = $fontFamily;
        $this->sheetName = $sheetName;
        $this->parentIdList = $array['parentIdList'] ?? [];
        $this->nonParentIdList = $array['nonParentIdList'] ?? [];
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
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
                    
                    // Style header row
                    $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
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
                    
                    // Style parent rows
                    foreach ($this->parentIdList as $rowNum) {
                        if ($rowNum <= $lastRow) {
                            $sheet->getStyle('A' . $rowNum . ':' . $lastColumn . $rowNum)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'CCCCCC'],
                                ],
                                'font' => [
                                    'name' => $this->fontFamily,
                                    'size' => 12,
                                ],
                            ]);
                        }
                    }
                    
                    // Style non-parent rows
                    foreach ($this->nonParentIdList as $rowNum) {
                        if ($rowNum <= $lastRow) {
                            $sheet->getStyle('A' . $rowNum . ':' . $lastColumn . $rowNum)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'ebdfdf'],
                                ],
                                'font' => [
                                    'name' => $this->fontFamily,
                                    'size' => 12,
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
