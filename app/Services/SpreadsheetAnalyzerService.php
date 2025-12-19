<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpreadsheetAnalyzerService
{
    /**
     * Maximum number of rows to sample for analysis
     */
    protected const SAMPLE_ROWS_LIMIT = 100;

    /**
     * Maximum number of rows to check for duplicates
     */
    protected const DUPLICATE_CHECK_LIMIT = 500;

    protected Spreadsheet $spreadsheet;

    protected array $analysisResult = [];

    /**
     * Get cell value by column and row (helper for PhpSpreadsheet 5.x compatibility)
     */
    protected function getCellValue(Worksheet $sheet, int $col, int $row): mixed
    {
        $cellAddress = Coordinate::stringFromColumnIndex($col).$row;

        return $sheet->getCell($cellAddress)->getValue();
    }

    /**
     * Analyze the uploaded spreadsheet file
     */
    public function analyze(string $filePath): array
    {
        $fullPath = Storage::path($filePath);

        if (! file_exists($fullPath)) {
            throw new \Exception('File tidak ditemukan: '.$filePath);
        }

        $this->spreadsheet = IOFactory::load($fullPath);

        $this->analysisResult = [
            'ringkasan_file' => $this->getFileSummary(),
            'sheet_details' => $this->analyzeSheets(),
            'relasi_antar_sheet' => $this->detectSheetRelations(),
            'masalah_data' => $this->detectDataProblems(),
            'insight_bisnis' => $this->generateBusinessInsights(),
            'rekomendasi' => $this->generateRecommendations(),
        ];

        return $this->analysisResult;
    }

    /**
     * Get file summary
     */
    protected function getFileSummary(): array
    {
        $sheetCount = $this->spreadsheet->getSheetCount();
        $sheetNames = $this->spreadsheet->getSheetNames();

        return [
            'jumlah_sheet' => $sheetCount,
            'nama_sheet' => $sheetNames,
            'deskripsi' => "File ini berisi {$sheetCount} sheet: ".implode(', ', $sheetNames),
        ];
    }

    /**
     * Analyze each sheet in detail
     */
    protected function analyzeSheets(): array
    {
        $sheetsAnalysis = [];

        foreach ($this->spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // Get headers (first row)
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $this->getCellValue($sheet, $col, 1);
                $headers[] = $cellValue;
            }

            // Analyze columns
            $columns = $this->analyzeColumns($sheet, $headers, $highestRow, $highestColumnIndex);

            // Detect business function
            $businessFunction = $this->detectBusinessFunction($sheetName, $headers);

            $sheetsAnalysis[$sheetName] = [
                'nama_sheet' => $sheetName,
                'jumlah_baris' => $highestRow - 1, // Excluding header
                'jumlah_kolom' => $highestColumnIndex,
                'headers' => $headers,
                'fungsi_bisnis' => $businessFunction,
                'detail_kolom' => $columns,
                'tabel_database_terkait' => $this->suggestDatabaseTable($sheetName, $headers),
            ];
        }

        return $sheetsAnalysis;
    }

    /**
     * Analyze columns with data type detection
     */
    protected function analyzeColumns(Worksheet $sheet, array $headers, int $highestRow, int $highestColumnIndex): array
    {
        $columns = [];
        $sampleRows = min($highestRow, self::SAMPLE_ROWS_LIMIT);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headerName = $headers[$col - 1] ?? "Kolom $col";
            $values = [];
            $emptyCount = 0;

            for ($row = 2; $row <= $sampleRows; $row++) {
                $value = $this->getCellValue($sheet, $col, $row);

                if ($value === null || $value === '') {
                    $emptyCount++;
                } else {
                    $values[] = $value;
                }
            }

            $dataType = $this->detectDataType($values, $headerName);
            $explanation = $this->getColumnExplanation($headerName, $dataType);

            $columns[] = [
                'nama_kolom' => $headerName,
                'tipe_data' => $dataType,
                'penjelasan' => $explanation,
                'jumlah_kosong' => $emptyCount,
                'persentase_kosong' => $sampleRows > 1 ? round(($emptyCount / ($sampleRows - 1)) * 100, 1) : 0,
                'contoh_nilai' => array_slice($values, 0, 3),
            ];
        }

        return $columns;
    }

    /**
     * Detect data type from values
     */
    protected function detectDataType(array $values, string $headerName): string
    {
        if (empty($values)) {
            return 'tidak_diketahui';
        }

        // Check header name for hints
        $headerLower = strtolower($headerName);

        if (preg_match('/tanggal|date|tgl|waktu|time/i', $headerLower)) {
            return 'tanggal';
        }

        if (preg_match('/harga|price|total|jumlah|qty|quantity|stok|stock/i', $headerLower)) {
            $hasNumbers = false;
            foreach ($values as $value) {
                if (is_numeric($value) || is_numeric(str_replace(['Rp', '.', ',', ' '], '', $value))) {
                    $hasNumbers = true;
                    break;
                }
            }
            if ($hasNumbers) {
                if (preg_match('/harga|price|total/i', $headerLower)) {
                    return 'mata_uang';
                }

                return 'angka';
            }
        }

        if (preg_match('/id|kode|code|no|nomor|sku/i', $headerLower)) {
            return 'id_kode';
        }

        // Analyze sample values
        $typeScores = [
            'angka' => 0,
            'tanggal' => 0,
            'mata_uang' => 0,
            'teks' => 0,
        ];

        foreach ($values as $value) {
            if (is_numeric($value)) {
                // Check if it looks like a date (Excel stores dates as numbers)
                if ($value > 40000 && $value < 50000) {
                    $typeScores['tanggal']++;
                } else {
                    $typeScores['angka']++;
                }
            } elseif (is_string($value)) {
                if (preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}/', $value) ||
                    preg_match('/^\d{2}[-\/]\d{2}[-\/]\d{4}/', $value)) {
                    $typeScores['tanggal']++;
                } elseif (preg_match('/^Rp\.?\s*[\d.,]+/', $value)) {
                    $typeScores['mata_uang']++;
                } elseif (is_numeric(str_replace(['Rp', '.', ',', ' '], '', $value))) {
                    $typeScores['angka']++;
                } else {
                    $typeScores['teks']++;
                }
            }
        }

        arsort($typeScores);

        return array_key_first($typeScores);
    }

    /**
     * Get user-friendly column explanation
     */
    protected function getColumnExplanation(string $headerName, string $dataType): string
    {
        $headerLower = strtolower($headerName);

        // Common column patterns and their explanations
        $explanations = [
            '/tanggal|date|tgl/' => 'Kolom ini digunakan untuk mencatat kapan suatu kejadian atau transaksi terjadi.',
            '/nama\s*produk|product\s*name|item/' => 'Kolom ini berisi nama produk atau barang yang dijual.',
            '/harga\s*satuan|unit\s*price/' => 'Kolom ini menunjukkan harga satu unit produk sebelum dikalikan dengan jumlah.',
            '/harga|price/' => 'Kolom ini berisi informasi harga dalam mata uang.',
            '/jumlah|qty|quantity/' => 'Kolom ini menunjukkan berapa banyak item atau barang.',
            '/total/' => 'Kolom ini berisi hasil perhitungan total (biasanya harga × jumlah).',
            '/stok|stock/' => 'Kolom ini mencatat jumlah barang yang tersedia.',
            '/nama\s*pelanggan|customer\s*name/' => 'Kolom ini mencatat nama pembeli atau pelanggan.',
            '/alamat|address/' => 'Kolom ini berisi informasi lokasi atau alamat.',
            '/telepon|phone|hp/' => 'Kolom ini menyimpan nomor kontak.',
            '/kategori|category/' => 'Kolom ini mengelompokkan data berdasarkan jenis atau tipe tertentu.',
            '/keterangan|deskripsi|notes|description/' => 'Kolom ini berisi catatan atau informasi tambahan.',
            '/id|kode|code|sku/' => 'Kolom ini berisi kode unik untuk identifikasi data.',
            '/status/' => 'Kolom ini menunjukkan kondisi atau status dari suatu data.',
        ];

        foreach ($explanations as $pattern => $explanation) {
            if (preg_match($pattern, $headerLower)) {
                return $explanation;
            }
        }

        // Default explanations based on data type
        return match ($dataType) {
            'tanggal' => "Kolom '{$headerName}' berisi data tanggal atau waktu.",
            'angka' => "Kolom '{$headerName}' berisi data numerik/angka.",
            'mata_uang' => "Kolom '{$headerName}' berisi data nilai uang.",
            'id_kode' => "Kolom '{$headerName}' berisi kode identifikasi unik.",
            default => "Kolom '{$headerName}' berisi data teks.",
        };
    }

    /**
     * Detect business function of a sheet
     */
    protected function detectBusinessFunction(string $sheetName, array $headers): array
    {
        $sheetLower = strtolower($sheetName);
        $headersLower = array_map('strtolower', array_filter($headers));
        $headerString = implode(' ', $headersLower);

        $functions = [];

        // Sales/Transaction detection
        if (preg_match('/penjualan|sales|transaksi|transaction|order|pesanan/i', $sheetLower.' '.$headerString)) {
            $functions[] = [
                'fungsi' => 'Pencatatan Penjualan',
                'deskripsi' => 'Sheet ini digunakan untuk mencatat transaksi penjualan harian atau per periode.',
                'icon' => '💰',
            ];
        }

        // Product/Inventory detection
        if (preg_match('/produk|product|barang|item|inventory|inventaris/i', $sheetLower.' '.$headerString)) {
            $functions[] = [
                'fungsi' => 'Data Master Produk',
                'deskripsi' => 'Sheet ini berisi daftar produk atau barang yang dijual.',
                'icon' => '📦',
            ];
        }

        // Stock detection
        if (preg_match('/stok|stock|persediaan|gudang|warehouse/i', $sheetLower.' '.$headerString)) {
            $functions[] = [
                'fungsi' => 'Pencatatan Stok',
                'deskripsi' => 'Sheet ini mencatat jumlah stok barang yang tersedia.',
                'icon' => '📊',
            ];
        }

        // Customer detection
        if (preg_match('/pelanggan|customer|klien|client|pembeli/i', $sheetLower.' '.$headerString)) {
            $functions[] = [
                'fungsi' => 'Data Pelanggan',
                'deskripsi' => 'Sheet ini menyimpan informasi pelanggan atau pembeli.',
                'icon' => '👥',
            ];
        }

        // Report/Summary detection
        if (preg_match('/laporan|report|rekap|summary|ringkasan/i', $sheetLower)) {
            $functions[] = [
                'fungsi' => 'Laporan/Rekap',
                'deskripsi' => 'Sheet ini berfungsi sebagai laporan rekap atau ringkasan data.',
                'icon' => '📈',
            ];
        }

        // Expense/Cost detection
        if (preg_match('/pengeluaran|expense|biaya|cost|operasional/i', $sheetLower.' '.$headerString)) {
            $functions[] = [
                'fungsi' => 'Pencatatan Pengeluaran',
                'deskripsi' => 'Sheet ini mencatat biaya atau pengeluaran usaha.',
                'icon' => '💸',
            ];
        }

        if (empty($functions)) {
            $functions[] = [
                'fungsi' => 'Data Umum',
                'deskripsi' => 'Sheet ini berisi data yang dapat digunakan untuk berbagai keperluan.',
                'icon' => '📋',
            ];
        }

        return $functions;
    }

    /**
     * Suggest related database table
     */
    protected function suggestDatabaseTable(string $sheetName, array $headers): array
    {
        $suggestions = [];
        $sheetLower = strtolower($sheetName);
        $headerString = implode(' ', array_map('strtolower', array_filter($headers)));

        if (preg_match('/penjualan|sales|transaksi|order/i', $sheetLower.' '.$headerString)) {
            $suggestions[] = 'Tabel Penjualan';
        }

        if (preg_match('/produk|product|barang|item/i', $sheetLower.' '.$headerString)) {
            $suggestions[] = 'Tabel Produk';
        }

        if (preg_match('/stok|stock|persediaan/i', $sheetLower.' '.$headerString)) {
            $suggestions[] = 'Tabel Stok';
        }

        if (preg_match('/pelanggan|customer|klien/i', $sheetLower.' '.$headerString)) {
            $suggestions[] = 'Tabel Pelanggan';
        }

        if (preg_match('/kategori|category/i', $sheetLower.' '.$headerString)) {
            $suggestions[] = 'Tabel Kategori';
        }

        return empty($suggestions) ? ['Tabel Umum'] : $suggestions;
    }

    /**
     * Detect relations between sheets
     */
    protected function detectSheetRelations(): array
    {
        $relations = [];
        $sheetNames = $this->spreadsheet->getSheetNames();

        if (count($sheetNames) < 2) {
            return ['info' => 'File hanya memiliki satu sheet, tidak ada relasi antar sheet yang dapat dideteksi.'];
        }

        // Collect all headers from all sheets
        $allHeaders = [];
        foreach ($sheetNames as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $this->getCellValue($sheet, $col, 1);
                if ($cellValue) {
                    $headers[] = strtolower($cellValue);
                }
            }
            $allHeaders[$sheetName] = $headers;
        }

        // Find common columns (potential relations)
        foreach ($sheetNames as $i => $sheet1) {
            for ($j = $i + 1; $j < count($sheetNames); $j++) {
                $sheet2 = $sheetNames[$j];
                $commonHeaders = array_intersect($allHeaders[$sheet1], $allHeaders[$sheet2]);

                if (! empty($commonHeaders)) {
                    $relations[] = [
                        'sheet1' => $sheet1,
                        'sheet2' => $sheet2,
                        'kolom_terkait' => array_values($commonHeaders),
                        'deskripsi' => "Sheet '{$sheet1}' dan '{$sheet2}' memiliki kolom yang sama: ".
                            implode(', ', $commonHeaders).'. Kemungkinan ada relasi data antara kedua sheet ini.',
                    ];
                }
            }
        }

        if (empty($relations)) {
            return ['info' => 'Tidak ditemukan kolom yang sama antar sheet. Sheet-sheet mungkin berisi data yang independen.'];
        }

        return $relations;
    }

    /**
     * Detect data problems
     */
    protected function detectDataProblems(): array
    {
        $problems = [];

        foreach ($this->spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $sheetProblems = [];

            // Get headers
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[$col] = $this->getCellValue($sheet, $col, 1);
            }

            // Check for empty columns
            $emptyColumns = [];
            $negativeValues = [];
            $zeroValues = [];
            $duplicates = [];

            $rowHashes = [];
            $sampleRows = min($highestRow, self::DUPLICATE_CHECK_LIMIT);

            for ($row = 2; $row <= $sampleRows; $row++) {
                $rowHash = '';

                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $value = $this->getCellValue($sheet, $col, $row);
                    $rowHash .= $value.'|';

                    $headerName = $headers[$col] ?? "Kolom $col";
                    $headerLower = strtolower($headerName);

                    // Check for negative values in quantity/stock columns
                    if (preg_match('/stok|stock|jumlah|qty/i', $headerLower)) {
                        if (is_numeric($value) && $value < 0) {
                            $negativeValues[] = [
                                'baris' => $row,
                                'kolom' => $headerName,
                                'nilai' => $value,
                            ];
                        }
                    }

                    // Check for zero prices
                    if (preg_match('/harga|price/i', $headerLower)) {
                        if (is_numeric($value) && $value == 0) {
                            $zeroValues[] = [
                                'baris' => $row,
                                'kolom' => $headerName,
                            ];
                        }
                    }
                }

                // Check for duplicates
                if (isset($rowHashes[$rowHash])) {
                    $duplicates[] = [
                        'baris_asli' => $rowHashes[$rowHash],
                        'baris_duplikat' => $row,
                    ];
                } else {
                    $rowHashes[$rowHash] = $row;
                }
            }

            // Check empty column ratio
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $emptyCount = 0;
                for ($row = 2; $row <= $sampleRows; $row++) {
                    $value = $this->getCellValue($sheet, $col, $row);
                    if ($value === null || $value === '') {
                        $emptyCount++;
                    }
                }
                $emptyRatio = ($sampleRows > 1) ? ($emptyCount / ($sampleRows - 1)) * 100 : 0;

                if ($emptyRatio > 50) {
                    $emptyColumns[] = [
                        'kolom' => $headers[$col] ?? "Kolom $col",
                        'persentase_kosong' => round($emptyRatio, 1),
                    ];
                }
            }

            // Compile problems for this sheet
            if (! empty($emptyColumns)) {
                $sheetProblems[] = [
                    'jenis' => 'Kolom Sering Kosong',
                    'icon' => '⚠️',
                    'detail' => $emptyColumns,
                    'dampak_bisnis' => 'Data yang tidak lengkap dapat menyebabkan kesalahan dalam analisis dan pelaporan.',
                ];
            }

            if (! empty($negativeValues)) {
                $sheetProblems[] = [
                    'jenis' => 'Nilai Negatif Tidak Wajar',
                    'icon' => '🔴',
                    'detail' => array_slice($negativeValues, 0, 5),
                    'dampak_bisnis' => 'Stok atau jumlah negatif menunjukkan kemungkinan kesalahan input atau data yang tidak valid.',
                ];
            }

            if (! empty($zeroValues)) {
                $sheetProblems[] = [
                    'jenis' => 'Harga Nol',
                    'icon' => '💰',
                    'detail' => array_slice($zeroValues, 0, 5),
                    'dampak_bisnis' => 'Harga nol dapat menyebabkan kesalahan perhitungan pendapatan.',
                ];
            }

            if (! empty($duplicates)) {
                $sheetProblems[] = [
                    'jenis' => 'Duplikasi Data',
                    'icon' => '📋',
                    'detail' => array_slice($duplicates, 0, 5),
                    'jumlah_duplikat' => count($duplicates),
                    'dampak_bisnis' => 'Data duplikat dapat menyebabkan perhitungan ganda pada laporan.',
                ];
            }

            if (! empty($sheetProblems)) {
                $problems[$sheetName] = $sheetProblems;
            }
        }

        if (empty($problems)) {
            return ['info' => '✅ Tidak ditemukan masalah data yang signifikan. Data sudah cukup baik untuk disimpan.'];
        }

        return $problems;
    }

    /**
     * Generate business insights
     */
    protected function generateBusinessInsights(): array
    {
        $insights = [];

        foreach ($this->spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // Get headers
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[$col] = $this->getCellValue($sheet, $col, 1);
            }

            $sheetInsights = [];

            // Find date range
            $dates = [];
            $salesTotal = 0;
            $productCounts = [];
            $salesColumn = null;
            $dateColumn = null;
            $productColumn = null;

            foreach ($headers as $col => $header) {
                if ($header === null) {
                    continue;
                }
                $headerLower = strtolower($header);

                if (preg_match('/tanggal|date|tgl/i', $headerLower)) {
                    $dateColumn = $col;
                }
                if (preg_match('/total|harga|price|penjualan/i', $headerLower)) {
                    $salesColumn = $col;
                }
                if (preg_match('/produk|product|nama\s*barang|item/i', $headerLower)) {
                    $productColumn = $col;
                }
            }

            // Collect data
            for ($row = 2; $row <= $highestRow; $row++) {
                if ($dateColumn) {
                    $dateValue = $this->getCellValue($sheet, $dateColumn, $row);
                    if (is_numeric($dateValue) && $dateValue > 0) {
                        try {
                            $dates[] = ExcelDate::excelToDateTimeObject($dateValue);
                        } catch (\Exception $e) {
                            // Invalid date, skip
                        }
                    } elseif (is_string($dateValue)) {
                        try {
                            $dates[] = new \DateTime($dateValue);
                        } catch (\Exception $e) {
                            // Invalid date, skip
                        }
                    }
                }

                if ($salesColumn) {
                    $value = $this->getCellValue($sheet, $salesColumn, $row);
                    if (is_numeric($value)) {
                        $salesTotal += (float) $value;
                    }
                }

                if ($productColumn) {
                    $productName = $this->getCellValue($sheet, $productColumn, $row);
                    if ($productName) {
                        $productCounts[$productName] = ($productCounts[$productName] ?? 0) + 1;
                    }
                }
            }

            // Generate insights
            if (! empty($dates)) {
                sort($dates);
                $startDate = $dates[0]->format('d M Y');
                $endDate = end($dates)->format('d M Y');
                $sheetInsights[] = [
                    'icon' => '📅',
                    'judul' => 'Rentang Waktu Data',
                    'nilai' => "$startDate - $endDate",
                    'deskripsi' => 'Periode data yang tercatat dalam file.',
                ];
            }

            if ($salesTotal > 0) {
                $sheetInsights[] = [
                    'icon' => '💰',
                    'judul' => 'Total Penjualan',
                    'nilai' => 'Rp '.number_format($salesTotal, 0, ',', '.'),
                    'deskripsi' => 'Perkiraan total penjualan berdasarkan data yang tersedia.',
                ];
            }

            if (! empty($productCounts)) {
                arsort($productCounts);
                $topProducts = array_slice($productCounts, 0, 5, true);
                $sheetInsights[] = [
                    'icon' => '🏆',
                    'judul' => 'Produk Paling Sering Muncul',
                    'nilai' => array_key_first($topProducts).' ('.reset($topProducts).' kali)',
                    'deskripsi' => 'Produk dengan frekuensi transaksi tertinggi.',
                    'detail' => $topProducts,
                ];
            }

            $sheetInsights[] = [
                'icon' => '📊',
                'judul' => 'Jumlah Data',
                'nilai' => ($highestRow - 1).' baris data',
                'deskripsi' => 'Total baris data (tidak termasuk header).',
            ];

            if (! empty($sheetInsights)) {
                $insights[$sheetName] = $sheetInsights;
            }
        }

        if (empty($insights)) {
            return ['info' => 'Data tidak cukup untuk menghasilkan insight bisnis. Pastikan file berisi data transaksi atau penjualan.'];
        }

        return $insights;
    }

    /**
     * Generate recommendations for improving the spreadsheet
     */
    protected function generateRecommendations(): array
    {
        $recommendations = [];

        foreach ($this->spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $sheetRecs = [];

            // Check headers
            $headers = [];
            $emptyHeaders = 0;
            $unclearHeaders = 0;

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $header = $this->getCellValue($sheet, $col, 1);
                $headers[] = $header;

                if ($header === null || $header === '') {
                    $emptyHeaders++;
                } elseif (strlen($header) < 3 || preg_match('/^[a-z]$/i', $header)) {
                    $unclearHeaders++;
                }
            }

            if ($emptyHeaders > 0) {
                $sheetRecs[] = [
                    'icon' => '📝',
                    'kategori' => 'Penamaan Kolom',
                    'masalah' => "Terdapat {$emptyHeaders} kolom tanpa nama header.",
                    'saran' => 'Berikan nama yang jelas untuk setiap kolom agar mudah dipahami.',
                    'manfaat' => 'Mempermudah identifikasi data dan mengurangi kebingungan saat input data.',
                ];
            }

            if ($unclearHeaders > 0) {
                $sheetRecs[] = [
                    'icon' => '✏️',
                    'kategori' => 'Kejelasan Header',
                    'masalah' => "Terdapat {$unclearHeaders} kolom dengan nama yang kurang deskriptif.",
                    'saran' => 'Gunakan nama kolom yang lebih deskriptif seperti "Nama Produk" bukan "P" atau "Tanggal Transaksi" bukan "Tgl".',
                    'manfaat' => 'Membantu admin lain memahami data tanpa penjelasan tambahan.',
                ];
            }

            // Check for data consistency
            if ($this->spreadsheet->getSheetCount() > 3) {
                $sheetRecs[] = [
                    'icon' => '📂',
                    'kategori' => 'Struktur File',
                    'masalah' => 'File memiliki banyak sheet.',
                    'saran' => 'Pertimbangkan untuk memisahkan data master (Produk, Pelanggan) dengan data transaksi (Penjualan).',
                    'manfaat' => 'Mempermudah pemeliharaan dan update data.',
                ];
            }

            if (! empty($sheetRecs)) {
                $recommendations[$sheetName] = $sheetRecs;
            }
        }

        // General recommendations
        $generalRecs = [];

        $generalRecs[] = [
            'icon' => '💾',
            'kategori' => 'Format File',
            'saran' => 'Simpan file dalam format .xlsx untuk menjaga kompatibilitas dengan sistem.',
            'manfaat' => 'Format xlsx lebih stabil dan mendukung lebih banyak fitur.',
        ];

        $generalRecs[] = [
            'icon' => '📅',
            'kategori' => 'Format Tanggal',
            'saran' => 'Gunakan format tanggal yang konsisten (contoh: YYYY-MM-DD atau DD/MM/YYYY).',
            'manfaat' => 'Memudahkan pengurutan dan filtering data berdasarkan waktu.',
        ];

        $generalRecs[] = [
            'icon' => '🔢',
            'kategori' => 'Format Angka',
            'saran' => 'Pastikan angka tidak dicampur dengan teks (contoh: "100" bukan "100 pcs").',
            'manfaat' => 'Memungkinkan perhitungan otomatis seperti total dan rata-rata.',
        ];

        $recommendations['_umum'] = $generalRecs;

        return $recommendations;
    }
}
