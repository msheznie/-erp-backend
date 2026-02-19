<?php

namespace App\helper;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Reads Excel sheets using PhpSpreadsheet (replacement for legacy Maatwebsite Excel 2.x load/selectSheetsByIndex).
 */
class ExcelSheetReader
{
    /**
     * Load first sheet from file and return rows as array of associative arrays (first row = keys).
     *
     * @param  string  $filePath  Absolute path to xlsx/xls file
     * @param  int|string  $sheetIndexOrName  Sheet index (0-based) or sheet title, default 0
     * @param  array|null  $selectColumns  If set, each row only includes these keys (by header name)
     * @return array<int, array<string, mixed>>
     */
    public static function sheetToAssocArray(string $filePath, int|string $sheetIndexOrName = 0, ?array $selectColumns = null): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = is_int($sheetIndexOrName)
            ? $spreadsheet->getSheet($sheetIndexOrName)
            : $spreadsheet->getSheetByName($sheetIndexOrName);

        if ($sheet === null) {
            return [];
        }

        $data = $sheet->toArray();
        if (empty($data)) {
            return [];
        }

        $headers = array_map([self::class, 'normalizeHeader'], $data[0]);

        $rows = [];
        for ($i = 1; $i < count($data); $i++) {
            $row = [];
            foreach ($headers as $j => $key) {
                if ($key !== '') {
                    $row[$key] = $data[$i][$j] ?? null;
                }
            }
            if ($selectColumns !== null) {
                $row = array_intersect_key($row, array_flip($selectColumns));
                $row = array_merge(array_fill_keys($selectColumns, null), $row);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Normalize header to lowercase with underscores (e.g. "Item Code" -> "item_code", "Cost/Unit" -> "cost_unit").
     */
    private static function normalizeHeader(mixed $h): string
    {
        if (! is_scalar($h)) {
            return '';
        }
        $s = trim((string) $h);
        $s = strtolower($s);
        $s = preg_replace('/[^a-z0-9]+/', '_', $s);
        $s = trim($s, '_');

        return $s;
    }

    /**
     * Convert raw 2D array (first row = headers) to array of associative rows. Use when you already have sheet data.
     *
     * @param  array<int, array<int, mixed>>  $data  Output of Worksheet::toArray()
     * @param  array|null  $selectColumns  If set, each row only includes these keys
     * @return array<int, array<string, mixed>>
     */
    public static function rawSheetToAssocArray(array $data, ?array $selectColumns = null): array
    {
        if (empty($data)) {
            return [];
        }

        $headers = array_map([self::class, 'normalizeHeader'], $data[0]);

        $rows = [];
        for ($i = 1; $i < count($data); $i++) {
            $row = [];
            foreach ($headers as $j => $key) {
                if ($key !== '') {
                    $row[$key] = $data[$i][$j] ?? null;
                }
            }
            if ($selectColumns !== null) {
                $row = array_intersect_key($row, array_flip($selectColumns));
                $row = array_merge(array_fill_keys($selectColumns, null), $row);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Load first sheet and return first data row as associative array, or null if empty.
     */
    public static function sheetFirstRow(string $filePath, int|string $sheetIndexOrName = 0): ?array
    {
        $rows = self::sheetToAssocArray($filePath, $sheetIndexOrName);
        return $rows[0] ?? null;
    }
}
