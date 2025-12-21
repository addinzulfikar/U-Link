<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetPreviewService
{
    private const DEFAULT_MAX_DATA_ROWS = 20;
    private const DEFAULT_MAX_COLUMNS = 15;

    private const HEADER_KEYWORDS = [
        'nama', 'name', 'tipe', 'type', 'kategori', 'category',
        'harga', 'price', 'stok', 'stock', 'deskripsi', 'description',
        'status', 'jumlah', 'qty', 'quantity', 'tanggal', 'date',
    ];

    public function preview(string $storagePath, int $maxDataRows = self::DEFAULT_MAX_DATA_ROWS, int $maxColumns = self::DEFAULT_MAX_COLUMNS): array
    {
        $fullPath = Storage::path($storagePath);
        $spreadsheet = IOFactory::load($fullPath);

        $result = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (! $sheet) {
                continue;
            }

            $highestRow = (int) $sheet->getHighestRow();
            $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());
            $highestColumnIndex = min($highestColumnIndex, $maxColumns);

            $headerRow = $this->detectHeaderRow($sheet, $highestRow, $highestColumnIndex);

            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[] = $this->cellToString($sheet->getCell(Coordinate::stringFromColumnIndex($col).$headerRow)->getValue());
            }

            // Trim trailing empty headers
            while (count($headers) > 1 && trim((string) end($headers)) === '') {
                array_pop($headers);
                $highestColumnIndex = count($headers);
            }

            $rows = [];
            $startRow = $headerRow + 1;
            $endRow = min($highestRow, $startRow + $maxDataRows - 1);

            for ($row = $startRow; $row <= $endRow; $row++) {
                $rowData = [];
                $hasAny = false;

                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $value = $sheet->getCell(Coordinate::stringFromColumnIndex($col).$row)->getValue();
                    $str = $this->cellToString($value);
                    if (trim($str) !== '') {
                        $hasAny = true;
                    }
                    $rowData[] = $str;
                }

                if ($hasAny) {
                    $rows[] = $rowData;
                }
            }

            $result[$sheetName] = [
                'header_row' => $headerRow,
                'headers' => $headers,
                'rows' => $rows,
            ];
        }

        return $result;
    }

    private function detectHeaderRow($sheet, int $highestRow, int $highestColumnIndex): int
    {
        $maxRowsToCheck = min(20, $highestRow);

        for ($row = 1; $row <= $maxRowsToCheck; $row++) {
            $nonEmpty = 0;
            $values = [];

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $value = $sheet->getCell(Coordinate::stringFromColumnIndex($col).$row)->getValue();
                $value = $this->cellToString($value);
                if ($value !== '') {
                    $nonEmpty++;
                    $values[] = strtolower($value);
                }
            }

            if ($nonEmpty < 3) {
                continue;
            }

            foreach ($values as $v) {
                foreach (self::HEADER_KEYWORDS as $k) {
                    if (str_contains($v, $k)) {
                        return $row;
                    }
                }
            }
        }

        return 1;
    }

    private function cellToString($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_numeric($value)) {
            // Keep numeric as-is; Excel prices/stocks are easier to read.
            return (string) $value;
        }

        return trim((string) $value);
    }
}
