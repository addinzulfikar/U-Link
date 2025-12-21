<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FinancialOverviewService
{
    /**
     * Process financial data from uploaded spreadsheet(s)
     * Supports merging data from multiple files
     */
    public function processFinancialData(array $filePaths, int $umkmId, ?int $uploadId = null): array
    {
        $allTransactions = [];
        $errors = [];
        $stats = [
            'total_files' => count($filePaths),
            'total_rows_processed' => 0,
            'total_rows_imported' => 0,
            'total_errors' => 0,
        ];

        foreach ($filePaths as $filePath) {
            try {
                $result = $this->processFile($filePath, $umkmId, $uploadId);
                $allTransactions = array_merge($allTransactions, $result['transactions']);
                $errors = array_merge($errors, $result['errors']);
                $stats['total_rows_processed'] += $result['rows_processed'];
                $stats['total_rows_imported'] += $result['rows_imported'];
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => basename($filePath),
                    'error' => 'Gagal memproses file: ' . $e->getMessage(),
                ];
            }
        }

        $stats['total_errors'] = count($errors);

        // Save all transactions to database
        if (!empty($allTransactions)) {
            FinancialTransaction::insert($allTransactions);
        }

        return [
            'stats' => $stats,
            'errors' => $errors,
            'transactions_count' => count($allTransactions),
        ];
    }

    /**
     * Process a single file
     */
    protected function processFile(string $filePath, int $umkmId, ?int $uploadId): array
    {
        $transactions = [];
        $errors = [];
        $rowsProcessed = 0;
        $rowsImported = 0;

        $spreadsheet = IOFactory::load($filePath);
        
        // Look for "Pemasukan & Pengeluaran" sheet or similar
        $sheet = null;
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (preg_match('/pemasukan|pengeluaran|income|expense|transaksi|keuangan/i', $sheetName)) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                break;
            }
        }

        if (!$sheet) {
            // If no matching sheet, try second sheet (as per requirement)
            if ($spreadsheet->getSheetCount() >= 2) {
                $sheet = $spreadsheet->getSheet(1); // Index 1 is the second sheet
            } else {
                throw new \Exception('Tidak ditemukan sheet untuk data keuangan');
            }
        }

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Find header row (look for "tanggal", "jenis", "nominal")
        $headerRow = $this->findHeaderRow($sheet, $highestColumnIndex, $highestRow);
        
        if (!$headerRow) {
            throw new \Exception('Tidak dapat menemukan header yang sesuai');
        }

        // Get column mappings
        $columnMap = $this->getColumnMapping($sheet, $headerRow, $highestColumnIndex);

        // Process data rows
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $rowsProcessed++;
            
            try {
                $transaction = $this->parseTransactionRow($sheet, $row, $columnMap, $umkmId, $uploadId, basename($filePath), $row);
                
                if ($transaction) {
                    $transactions[] = $transaction;
                    $rowsImported++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => basename($filePath),
                    'row' => $row,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'transactions' => $transactions,
            'errors' => $errors,
            'rows_processed' => $rowsProcessed,
            'rows_imported' => $rowsImported,
        ];
    }

    /**
     * Find the header row in the sheet
     */
    protected function findHeaderRow($sheet, int $highestColumnIndex, int $highestRow): ?int
    {
        $keywords = ['tanggal', 'jenis', 'transaksi', 'nominal', 'keterangan', 'date', 'type', 'amount'];
        
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            $matchCount = 0;
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;
                $value = strtolower(trim((string)$sheet->getCell($cellAddress)->getValue()));
                
                foreach ($keywords as $keyword) {
                    if (str_contains($value, $keyword)) {
                        $matchCount++;
                        break;
                    }
                }
            }
            
            if ($matchCount >= 3) {
                return $row;
            }
        }
        
        return null;
    }

    /**
     * Map columns to their purposes
     */
    protected function getColumnMapping($sheet, int $headerRow, int $highestColumnIndex): array
    {
        $map = [
            'date' => null,
            'type' => null,
            'description' => null,
            'amount' => null,
        ];

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellAddress = Coordinate::stringFromColumnIndex($col) . $headerRow;
            $header = strtolower(trim((string)$sheet->getCell($cellAddress)->getValue()));

            if (preg_match('/tanggal|date|tgl/i', $header)) {
                $map['date'] = $col;
            } elseif (preg_match('/jenis|type|transaksi/i', $header)) {
                $map['type'] = $col;
            } elseif (preg_match('/keterangan|description|deskripsi|catatan/i', $header)) {
                $map['description'] = $col;
            } elseif (preg_match('/nominal|amount|jumlah|harga/i', $header)) {
                $map['amount'] = $col;
            }
        }

        return $map;
    }

    /**
     * Parse a single transaction row
     */
    protected function parseTransactionRow($sheet, int $row, array $columnMap, int $umkmId, ?int $uploadId, string $sourceFile, int $rowNumber): ?array
    {
        $errors = [];
        
        // Get date
        $dateValue = $columnMap['date'] ? $sheet->getCell(Coordinate::stringFromColumnIndex($columnMap['date']) . $row)->getValue() : null;
        $transactionDate = $this->parseDate($dateValue);
        
        if (!$transactionDate) {
            $errors[] = 'Format tanggal tidak valid';
        }

        // Get type
        $typeValue = $columnMap['type'] ? trim((string)$sheet->getCell(Coordinate::stringFromColumnIndex($columnMap['type']) . $row)->getValue()) : null;
        $transactionType = $this->parseTransactionType($typeValue);
        
        if (!$transactionType) {
            $errors[] = 'Jenis transaksi tidak valid (harus: Pemasukan atau Pengeluaran)';
        }

        // Get description
        $description = $columnMap['description'] ? trim((string)$sheet->getCell(Coordinate::stringFromColumnIndex($columnMap['description']) . $row)->getValue()) : null;

        // Get amount
        $amountValue = $columnMap['amount'] ? $sheet->getCell(Coordinate::stringFromColumnIndex($columnMap['amount']) . $row)->getValue() : null;
        $amount = $this->parseAmount($amountValue);
        
        if ($amount === null || $amount <= 0) {
            $errors[] = 'Nominal tidak valid';
        }

        // Skip row if all values are empty
        if (!$dateValue && !$typeValue && !$amountValue) {
            return null;
        }

        // Return transaction even with errors (as per requirement)
        return [
            'umkm_id' => $umkmId,
            'spreadsheet_upload_id' => $uploadId,
            'transaction_date' => $transactionDate ?? now()->format('Y-m-d'),
            'transaction_type' => $transactionType ?? 'Pemasukan',
            'description' => $description,
            'amount' => $amount ?? 0,
            'source_file' => $sourceFile,
            'row_number' => $rowNumber,
            'validation_errors' => !empty($errors) ? json_encode($errors) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Parse date value from various formats
     */
    protected function parseDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        // If it's an Excel date number
        if (is_numeric($value) && $value > 0) {
            try {
                $date = ExcelDate::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Not a valid Excel date
            }
        }

        // Try parsing as string
        if (is_string($value)) {
            try {
                $date = new \DateTime($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Invalid date string
            }
        }

        return null;
    }

    /**
     * Parse transaction type
     */
    protected function parseTransactionType(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = strtolower(trim($value));
        
        if (preg_match('/pemasukan|income|masuk|pendapatan|revenue/i', $normalized)) {
            return FinancialTransaction::TYPE_INCOME;
        }
        
        if (preg_match('/pengeluaran|expense|keluar|biaya|cost/i', $normalized)) {
            return FinancialTransaction::TYPE_EXPENSE;
        }

        return null;
    }

    /**
     * Parse amount value
     */
    protected function parseAmount($value): ?float
    {
        if (!$value) {
            return null;
        }

        // If already numeric
        if (is_numeric($value)) {
            return (float)$value;
        }

        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.,\-]/', '', (string)$value);
        $cleaned = str_replace(',', '.', $cleaned);
        
        if (is_numeric($cleaned)) {
            return (float)$cleaned;
        }

        return null;
    }

    /**
     * Generate financial overview for a UMKM
     */
    public function generateOverview(int $umkmId): array
    {
        $umkm = Umkm::findOrFail($umkmId);
        
        // Calculate financial totals
        $totalIncome = FinancialTransaction::where('umkm_id', $umkmId)
            ->where('transaction_type', FinancialTransaction::TYPE_INCOME)
            ->sum('amount');
            
        $totalExpense = FinancialTransaction::where('umkm_id', $umkmId)
            ->where('transaction_type', FinancialTransaction::TYPE_EXPENSE)
            ->sum('amount');
            
        $netBalance = $totalIncome - $totalExpense;

        // Calculate stock value (from products)
        $totalStockValue = Product::where('umkm_id', $umkmId)
            ->where('type', Product::TYPE_PRODUCT)
            ->selectRaw('SUM(price * stock) as total_value')
            ->value('total_value') ?? 0;

        // Calculate asset value (products + net balance)
        $totalAssetValue = $totalStockValue + $netBalance;

        // Get transaction statistics
        $transactionStats = [
            'total_transactions' => FinancialTransaction::where('umkm_id', $umkmId)->count(),
            'income_count' => FinancialTransaction::where('umkm_id', $umkmId)
                ->where('transaction_type', FinancialTransaction::TYPE_INCOME)
                ->count(),
            'expense_count' => FinancialTransaction::where('umkm_id', $umkmId)
                ->where('transaction_type', FinancialTransaction::TYPE_EXPENSE)
                ->count(),
            'transactions_with_errors' => FinancialTransaction::where('umkm_id', $umkmId)
                ->whereNotNull('validation_errors')
                ->count(),
        ];

        // Get monthly trends (last 6 months)
        $monthlyTrends = $this->getMonthlyTrends($umkmId, 6);

        return [
            'overview' => [
                'total_asset_value' => $totalAssetValue,
                'total_stock_value' => $totalStockValue,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $netBalance,
            ],
            'statistics' => $transactionStats,
            'monthly_trends' => $monthlyTrends,
        ];
    }

    /**
     * Get monthly income/expense trends
     */
    protected function getMonthlyTrends(int $umkmId, int $months = 6): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        
        $data = FinancialTransaction::where('umkm_id', $umkmId)
            ->where('transaction_date', '>=', $startDate)
            ->selectRaw("
                DATE_TRUNC('month', transaction_date) as month,
                transaction_type,
                SUM(amount) as total
            ")
            ->groupBy('month', 'transaction_type')
            ->orderBy('month')
            ->get();

        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $month = now()->subMonths($months - $i - 1)->format('Y-m');
            $trends[$month] = [
                'month' => $month,
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ];
        }

        foreach ($data as $row) {
            $month = date('Y-m', strtotime($row->month));
            if (isset($trends[$month])) {
                if ($row->transaction_type === FinancialTransaction::TYPE_INCOME) {
                    $trends[$month]['income'] = (float)$row->total;
                } else {
                    $trends[$month]['expense'] = (float)$row->total;
                }
                $trends[$month]['balance'] = $trends[$month]['income'] - $trends[$month]['expense'];
            }
        }

        return array_values($trends);
    }
}
