<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Builds the Customer Invoice upload template programmatically.
 *
 * Row layout (matches upload job's $startRow = 13 and groupBy(6) for column G):
 *   Rows  1–6  : Instructions
 *   Row   7    : empty spacer
 *   Row   8    : empty spacer
 *   Row   9    : empty spacer
 *   Row  10    : Section header  (Header | Details)
 *   Row  11    : Mandatory flags (M / blank)
 *   Row  12    : Column labels
 *   Row  13+   : Empty data rows (ready for user input)
 */
class CustomerInvoiceTemplateExport implements WithEvents
{
    use Exportable;

    public function __construct(
        private readonly bool $isProjectBase,
        private readonly bool $isVATEligible,
        private readonly string $companyName = '',
    ) {}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $columns  = $this->buildColumns();
                $lastCol  = $this->columnLetter(count($columns));
                $totalCols = count($columns);

                // ── Instructions block (rows 1–6) ──────────────────────────────
                $instructions = [
                    '"M" refers to Mandatory field. If a mandatory field is empty in any row, the upload will not be successful.',
                    'Do not amend (delete, move, edit, rename) any of the columns or rows in the provided template.',
                    'To add multiple details to a single invoice, repeat the same Customer Invoice Number on multiple rows.',
                    'The invoice header details will be extracted from the first row for invoices with multiple line items.',
                    'Confirmed By and Approved By are optional; if blank the system will use the uploader\'s name.',
                    'In Customer Code / CR Number, at least one field must have a value. Both cannot be blank.',
                ];

                $sheet->setCellValue('A1', 'Instructions to Populate Data');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                foreach ($instructions as $idx => $text) {
                    $row = $idx + 2; // rows 2-7
                    $sheet->setCellValue("A{$row}", $text);
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(30);
                }

                // ── Spacer rows 8–9 ────────────────────────────────────────────
                // (nothing to set; blank rows provide visual separation)

                // ── Row 10: section group header (Header | Details) ────────────
                $headerCols  = $this->headerColumnCount();
                $detailCols  = $totalCols - $headerCols;
                $headerEnd   = $this->columnLetter($headerCols);
                $detailStart = $this->columnLetter($headerCols + 1);

                $sheet->setCellValue('A10', 'Header');
                $sheet->mergeCells("A10:{$headerEnd}10");
                $sheet->getStyle("A10:{$headerEnd}10")->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->setCellValue("{$detailStart}10", 'Details');
                $sheet->mergeCells("{$detailStart}10:{$lastCol}10");
                $sheet->getStyle("{$detailStart}10:{$lastCol}10")->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Row 11: mandatory markers ──────────────────────────────────
                foreach ($columns as $colIdx => $col) {
                    $letter = $this->columnLetter($colIdx + 1);
                    $sheet->setCellValue("{$letter}11", $col['mandatory'] ? 'M' : '');
                    $sheet->getStyle("{$letter}11")->applyFromArray([
                        'font'      => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // ── Row 12: column label headers ───────────────────────────────
                foreach ($columns as $colIdx => $col) {
                    $letter = $this->columnLetter($colIdx + 1);
                    $sheet->setCellValue("{$letter}12", $col['label']);
                    $sheet->getColumnDimension($letter)->setWidth($col['width'] ?? 20);
                }
                $sheet->getStyle("A12:{$lastCol}12")->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(12)->setRowHeight(30);

                // ── Border around the header block (rows 10–12) ───────────────
                $sheet->getStyle("A10:{$lastCol}12")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'BFBFBF'],
                        ],
                    ],
                ]);

                // ── Freeze panes so header rows stay visible while scrolling ──
                $sheet->freezePane('A13');

                // ── Row 1 height ───────────────────────────────────────────────
                $sheet->getRowDimension(1)->setRowHeight(25);

                $sheet->setTitle('Customer Invoice Template');
            },
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns the ordered column definitions.
     * Column G (index 6, 0-based) MUST be "Customer Invoice No" — the upload
     * job groups rows by this column using groupBy(6).
     */
    private function buildColumns(): array
    {
        $columns = [
            // ── Header columns (A–K fixed) ────────────────────────────────
            ['label' => 'Customer Code',      'mandatory' => true,  'width' => 18],
            ['label' => 'CR Number',           'mandatory' => false, 'width' => 18],
            ['label' => 'Currency',            'mandatory' => true,  'width' => 12],
            ['label' => 'Comments',            'mandatory' => true,  'width' => 25],
            ['label' => 'Document Date',       'mandatory' => true,  'width' => 18],
            ['label' => 'Invoice Due Date',    'mandatory' => true,  'width' => 18],
            ['label' => 'Customer Invoice No', 'mandatory' => true,  'width' => 22], // ← col G, index 6
            ['label' => 'Bank',                'mandatory' => true,  'width' => 20],
            ['label' => 'Account No',          'mandatory' => true,  'width' => 20],
            ['label' => 'Confirmed By',        'mandatory' => false, 'width' => 18],
            ['label' => 'Approved By',         'mandatory' => false, 'width' => 18],
        ];

        // ── Detail columns (L onward, order must match upload job) ────────
        $columns[] = ['label' => 'GL Account', 'mandatory' => true,  'width' => 20];

        if ($this->isProjectBase) {
            $columns[] = ['label' => 'Project', 'mandatory' => false, 'width' => 18];
        }

        $columns[] = ['label' => 'Segment',         'mandatory' => true,  'width' => 18];
        $columns[] = ['label' => 'Detail Comments',  'mandatory' => false, 'width' => 25];
        $columns[] = ['label' => 'UOM',              'mandatory' => true,  'width' => 12];
        $columns[] = ['label' => 'Qty',              'mandatory' => true,  'width' => 10];
        $columns[] = ['label' => 'Sales Price',      'mandatory' => true,  'width' => 15];
        $columns[] = ['label' => 'Discount Amount',  'mandatory' => false, 'width' => 18];

        if ($this->isVATEligible) {
            $columns[] = ['label' => 'VAT Amount', 'mandatory' => false, 'width' => 15];
        }

        return $columns;
    }

    /** Number of fixed header-section columns (A–K). */
    private function headerColumnCount(): int
    {
        return 11;
    }

    /** Convert a 1-based column index to a spreadsheet letter (1→A, 26→Z, 27→AA …). */
    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $remainder = ($index - 1) % 26;
            $letter    = chr(65 + $remainder) . $letter;
            $index     = (int) (($index - $remainder - 1) / 26);
        }
        return $letter;
    }
}
