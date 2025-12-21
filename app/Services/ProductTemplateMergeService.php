<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\SpreadsheetUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductTemplateMergeService
{
    /**
     * Merge products from the "Template Produk" sheet into the UMKM product database.
     *
     * Expected columns (based on UmkmTemplateExport):
     * - Nama Produk
     * - Tipe (Produk/Jasa)
     * - Kategori
     * - Deskripsi
     * - Harga
     * - Stok
     * - Status (Aktif/Nonaktif)
     */
    public function mergeFromUpload(SpreadsheetUpload $upload, int $umkmId): array
    {
        $fullPath = Storage::path($upload->file_path);

        $spreadsheet = IOFactory::load($fullPath);

        $sheet = $this->findProductTemplateSheet($spreadsheet);
        if (! $sheet) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [
                    'Tidak ditemukan sheet "Template Produk".',
                ],
            ];
        }

        $highestRow = (int) $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $headerRow = $this->findHeaderRow($sheet, $highestRow, $highestColumnIndex);
        if (! $headerRow) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [
                    'Header template produk tidak ditemukan (kolom "Nama Produk" dll).',
                ],
            ];
        }

        $columnMap = $this->mapColumns($sheet, $headerRow, $highestColumnIndex);
        if (! isset($columnMap['name'])) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [
                    'Kolom "Nama Produk" tidak ditemukan pada header.',
                ],
            ];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // IMPORTANT for PostgreSQL: if a statement fails inside an open transaction,
        // the whole transaction becomes "aborted" and further queries will throw 25P02 until rollback.
        // To avoid repeated 25P02 (e.g. if something higher up opened a transaction),
        // we do row-level operations without starting our own transactions and force-clean rollback on failure.
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $name = $this->getCellString($sheet, $columnMap['name'], $row);
            if ($name === '') {
                if ($this->isRowEmpty($sheet, $row, $columnMap)) {
                    $skipped++;
                    continue;
                }
                $skipped++;
                continue;
            }

            try {
                $typeRaw = isset($columnMap['type']) ? $this->getCellString($sheet, $columnMap['type'], $row) : '';
                $categoryRaw = isset($columnMap['category']) ? $this->getCellString($sheet, $columnMap['category'], $row) : '';
                $description = isset($columnMap['description']) ? $this->getCellString($sheet, $columnMap['description'], $row) : null;
                $priceRaw = isset($columnMap['price']) ? $this->getCellValue($sheet, $columnMap['price'], $row) : null;
                $stockRaw = isset($columnMap['stock']) ? $this->getCellValue($sheet, $columnMap['stock'], $row) : null;
                $statusRaw = isset($columnMap['status']) ? $this->getCellString($sheet, $columnMap['status'], $row) : '';

                $type = $this->parseProductType($typeRaw);
                $price = $this->parseIntegerMoney($priceRaw);
                $stock = $this->parseIntegerNullable($stockRaw);
                $isActive = $this->parseIsActive($statusRaw);

                $categoryId = null;
                if ($categoryRaw !== '') {
                    $categorySlug = Str::slug($categoryRaw);
                    if ($categorySlug !== '') {
                        $category = Category::firstOrCreate(
                            ['slug' => $categorySlug],
                            ['name' => $categoryRaw]
                        );
                        $categoryId = $category->id;
                    }
                }

                $slug = Str::slug($name);
                if ($slug === '') {
                    throw new \RuntimeException('nama produk tidak valid');
                }

                $existing = Product::where('umkm_id', $umkmId)->where('slug', $slug)->first();

                $payload = [
                    'umkm_id' => $umkmId,
                    'type' => $type,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $type === Product::TYPE_SERVICE ? null : $stock,
                    'is_active' => $isActive,
                    'category_id' => $categoryId,
                ];

                Product::updateOrCreate(
                    ['umkm_id' => $umkmId, 'slug' => $slug],
                    $payload
                );

                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }
            } catch (\Throwable $e) {
                // Defensive cleanup: if something else started a transaction, ensure we rollback to clear 25P02 state.
                while (DB::transactionLevel() > 0) {
                    try {
                        DB::rollBack();
                    } catch (\Throwable $ignored) {
                        break;
                    }
                }
                $errors[] = "Baris {$row}: gagal merge produk ({$name}) - {$e->getMessage()}";
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function findProductTemplateSheet($spreadsheet)
    {
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (preg_match('/^template\s*produk/i', $sheetName)) {
                return $spreadsheet->getSheetByName($sheetName);
            }
        }

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (preg_match('/produk|product/i', $sheetName)) {
                return $spreadsheet->getSheetByName($sheetName);
            }
        }

        return null;
    }

    private function findHeaderRow($sheet, int $highestRow, int $highestColumnIndex): ?int
    {
        $maxScan = min(30, $highestRow);
        for ($row = 1; $row <= $maxScan; $row++) {
            $hit = 0;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $v = strtolower(trim((string) $sheet->getCell(Coordinate::stringFromColumnIndex($col).$row)->getValue()));
                if ($v === '') {
                    continue;
                }
                if (str_contains($v, 'nama produk')) {
                    $hit++;
                }
                if (str_contains($v, 'kategori')) {
                    $hit++;
                }
                if (str_contains($v, 'harga')) {
                    $hit++;
                }
                if (str_contains($v, 'stok')) {
                    $hit++;
                }
            }

            if ($hit >= 2) {
                return $row;
            }
        }

        return null;
    }

    private function mapColumns($sheet, int $headerRow, int $highestColumnIndex): array
    {
        $map = [];

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header = strtolower(trim((string) $sheet->getCell(Coordinate::stringFromColumnIndex($col).$headerRow)->getValue()));
            if ($header === '') {
                continue;
            }

            if (preg_match('/nama\s*produk|product\s*name/i', $header)) {
                $map['name'] = $col;
            } elseif (preg_match('/tipe|jenis\s*\(produk\/?jasa\)|produk\/?jasa|type/i', $header)) {
                $map['type'] = $col;
            } elseif (preg_match('/kategori|category/i', $header)) {
                $map['category'] = $col;
            } elseif (preg_match('/deskripsi|keterangan|description/i', $header)) {
                $map['description'] = $col;
            } elseif (preg_match('/harga|price|nominal/i', $header)) {
                $map['price'] = $col;
            } elseif (preg_match('/stok|stock|qty|quantity/i', $header)) {
                $map['stock'] = $col;
            } elseif (preg_match('/status|aktif|nonaktif|active/i', $header)) {
                $map['status'] = $col;
            }
        }

        return $map;
    }

    private function getCellValue($sheet, int $colIndex, int $row)
    {
        return $sheet->getCell(Coordinate::stringFromColumnIndex($colIndex).$row)->getValue();
    }

    private function getCellString($sheet, int $colIndex, int $row): string
    {
        $value = $this->getCellValue($sheet, $colIndex, $row);
        $value = is_string($value) ? $value : (string) $value;
        return trim($value);
    }

    private function isRowEmpty($sheet, int $row, array $columnMap): bool
    {
        $cols = array_values($columnMap);
        $cols = array_filter($cols, fn ($v) => is_int($v));
        foreach ($cols as $col) {
            $v = trim((string) $this->getCellValue($sheet, $col, $row));
            if ($v !== '') {
                return false;
            }
        }
        return true;
    }

    private function parseProductType(string $value): string
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return Product::TYPE_PRODUCT;
        }
        if (preg_match('/jasa|service/i', $normalized)) {
            return Product::TYPE_SERVICE;
        }
        return Product::TYPE_PRODUCT;
    }

    private function parseIsActive(string $value): bool
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return true;
        }
        if (preg_match('/non|tidak|false|0|mati|off/i', $normalized)) {
            return false;
        }
        if (preg_match('/aktif|active|true|1|on/i', $normalized)) {
            return true;
        }
        return true;
    }

    private function parseIntegerMoney($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (int) round((float) $value);
        }
        $s = (string) $value;
        // keep digits only
        $digits = preg_replace('/[^0-9]/', '', $s);
        if ($digits === '' || ! is_numeric($digits)) {
            return 0;
        }
        return (int) $digits;
    }

    private function parseIntegerNullable($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) round((float) $value);
        }
        $s = (string) $value;
        $digits = preg_replace('/[^0-9\-]/', '', $s);
        if ($digits === '' || ! is_numeric($digits)) {
            return null;
        }
        return (int) $digits;
    }
}
