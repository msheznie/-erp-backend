<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Common Laravel Excel 3.1 export for Blade view-based reports.
 * Replaces the legacy Excel::create() + loadView() pattern with consistent
 * styling: title/date rows, header row bold, font family, RTL support, column auto-size.
 */
class BladeViewExcelExport implements FromView, WithEvents
{
    protected string $viewName;

    /**
     * @var array<string, mixed>
     */
    protected array $reportData;

    protected string $fontFamily;

    protected bool $isRtl;

    /** Last column letter for title/date merge (e.g. 'I'). Null = use sheet highest column. */
    protected ?string $titleColumnLast;

    /** Header row index when row 2 (date) is empty. Default 4 (layout: title, empty, empty, header). */
    protected int $headerRowWhenNoDate;

    /** Header row index when row 2 has date. Default 5 (layout: title, date, empty, empty, header). */
    protected int $headerRowWhenDate;

    protected int $bodyFontSize;

    /**
     * @param  array<string, mixed>  $reportData  Data passed to the Blade view (e.g. data, fromDate, toDate)
     * @param  string|null  $titleColumnLast  Last column for title/date merge; null = use sheet's highest column
     * @param  int|null  $headerRowWhenNoDate  Header row when there is no date row (default 4)
     * @param  int|null  $headerRowWhenDate  Header row when date row is present (default 5)
     */
    public function __construct(
        string $viewName,
        array $reportData,
        string $fontFamily = 'Calibri',
        bool $isRtl = false,
        ?string $titleColumnLast = 'I',
        ?int $headerRowWhenNoDate = 4,
        ?int $headerRowWhenDate = 5,
        int $bodyFontSize = 11
    ) {
        $this->viewName = $viewName;
        $this->reportData = $reportData;
        $this->fontFamily = $fontFamily;
        $this->isRtl = $isRtl;
        $this->titleColumnLast = $titleColumnLast;
        $this->headerRowWhenNoDate = $headerRowWhenNoDate ?? 4;
        $this->headerRowWhenDate = $headerRowWhenDate ?? 5;
        $this->bodyFontSize = $bodyFontSize;
    }

    public function view(): View
    {
        return view($this->viewName, $this->reportData);
    }

    /**
     * @return array<string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle(trans('custom.new_sheet'));

                $this->applyBodyFont($sheet);
                $this->applyTitleAndDateStyles($sheet);
                $this->applyHeaderRowStyle($sheet);
                $this->applyColumnAutoSize($sheet);
                $this->applyRtlIfNeeded($sheet);
            },
        ];
    }

    private function getTitleColumnLast(Worksheet $sheet): string
    {
        return $this->titleColumnLast ?? $sheet->getHighestColumn();
    }

    private function applyTitleAndDateStyles(Worksheet $sheet): void
    {
        $lastCol = $this->getTitleColumnLast($sheet);
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setName($this->fontFamily)->setSize(16)->setBold(true);

        $row2Value = $sheet->getCell('A2')->getValue();
        if ($row2Value !== null && (string) $row2Value !== '') {
            $sheet->mergeCells('A2:' . $lastCol . '2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2')->getFont()->setName($this->fontFamily)->setSize(12);
        }
    }

    private function applyHeaderRowStyle(Worksheet $sheet): void
    {
        $headerRow = $this->detectHeaderRow($sheet);
        $lastCol = $sheet->getHighestColumn();
        $headerRange = 'A' . $headerRow . ':' . $lastCol . $headerRow;
        $sheet->getStyle($headerRange)->getFont()->setName($this->fontFamily)->setSize($this->bodyFontSize)->setBold(true);
    }

    private function detectHeaderRow(Worksheet $sheet): int
    {
        $row2Value = $sheet->getCell('A2')->getValue();

        return ($row2Value !== null && (string) $row2Value !== '')
            ? $this->headerRowWhenDate
            : $this->headerRowWhenNoDate;
    }

    private function applyBodyFont(Worksheet $sheet): void
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        if ($lastRow > 0 && $lastColumn) {
            $range = 'A1:' . $lastColumn . $lastRow;
            $sheet->getStyle($range)->getFont()->setName($this->fontFamily)->setSize($this->bodyFontSize);
        }
    }

    private function applyColumnAutoSize(Worksheet $sheet): void
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
    }

    private function applyRtlIfNeeded(Worksheet $sheet): void
    {
        if ($this->isRtl) {
            $sheet->setRightToLeft(true);
            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();
            if ($lastRow > 0 && $lastColumn) {
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row2Value = $sheet->getCell('A2')->getValue();
            if ($row2Value !== null && (string) $row2Value !== '') {
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
    }
}
