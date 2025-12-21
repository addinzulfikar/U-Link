<?php

namespace Tests\Unit;

use App\Services\FinancialOverviewService;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinancialOverviewServiceTest extends TestCase
{
    protected function createTestSpreadsheet(): string
    {
        $spreadsheet = new Spreadsheet();
        
        // Create a sheet with financial data
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pemasukan & Pengeluaran');
        
        // Add headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Jenis Transaksi');
        $sheet->setCellValue('D1', 'Keterangan');
        $sheet->setCellValue('E1', 'Nominal');
        
        // Add sample data
        $data = [
            [1, '2024-01-15', 'Pemasukan', 'Penjualan Produk A', 500000],
            [2, '2024-01-16', 'Pengeluaran', 'Beli Bahan Baku', 200000],
            [3, '2024-01-17', 'Pemasukan', 'Penjualan Produk B', 750000],
            [4, '2024-01-18', 'Pengeluaran', 'Bayar Listrik', 150000],
            [5, '2024-01-19', 'Pemasukan', 'Penjualan Produk C', 300000],
        ];
        
        $row = 2;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_financial_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        
        return $tempFile;
    }

    public function test_can_find_header_row(): void
    {
        $service = new FinancialOverviewService();
        $testFile = $this->createTestSpreadsheet();
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('findHeaderRow');
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($testFile);
        $sheet = $spreadsheet->getActiveSheet();
        
        $headerRow = $method->invoke($service, $sheet, 5, 10);
        
        $this->assertEquals(1, $headerRow, 'Should find header row at row 1');
        
        unlink($testFile);
    }

    public function test_can_map_columns(): void
    {
        $service = new FinancialOverviewService();
        $testFile = $this->createTestSpreadsheet();
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('getColumnMapping');
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($testFile);
        $sheet = $spreadsheet->getActiveSheet();
        
        $mapping = $method->invoke($service, $sheet, 1, 5);
        
        $this->assertIsArray($mapping);
        $this->assertArrayHasKey('date', $mapping);
        $this->assertArrayHasKey('type', $mapping);
        $this->assertArrayHasKey('description', $mapping);
        $this->assertArrayHasKey('amount', $mapping);
        
        $this->assertEquals(2, $mapping['date'], 'Date should be in column 2 (B)');
        $this->assertEquals(3, $mapping['type'], 'Type should be in column 3 (C)');
        $this->assertEquals(4, $mapping['description'], 'Description should be in column 4 (D)');
        $this->assertEquals(5, $mapping['amount'], 'Amount should be in column 5 (E)');
        
        unlink($testFile);
    }

    public function test_can_parse_transaction_type(): void
    {
        $service = new FinancialOverviewService();
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseTransactionType');
        
        // Test income variations
        $this->assertEquals('Pemasukan', $method->invoke($service, 'Pemasukan'));
        $this->assertEquals('Pemasukan', $method->invoke($service, 'Income'));
        $this->assertEquals('Pemasukan', $method->invoke($service, 'Masuk'));
        $this->assertEquals('Pemasukan', $method->invoke($service, 'Pendapatan'));
        
        // Test expense variations
        $this->assertEquals('Pengeluaran', $method->invoke($service, 'Pengeluaran'));
        $this->assertEquals('Pengeluaran', $method->invoke($service, 'Expense'));
        $this->assertEquals('Pengeluaran', $method->invoke($service, 'Keluar'));
        $this->assertEquals('Pengeluaran', $method->invoke($service, 'Biaya'));
        
        // Test case insensitivity
        $this->assertEquals('Pemasukan', $method->invoke($service, 'PEMASUKAN'));
        $this->assertEquals('Pengeluaran', $method->invoke($service, 'pengeluaran'));
    }

    public function test_can_parse_date_from_string(): void
    {
        $service = new FinancialOverviewService();
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseDate');
        
        // Test various date formats
        $this->assertEquals('2024-01-15', $method->invoke($service, '2024-01-15'));
        $this->assertEquals('2024-01-15', $method->invoke($service, '15/01/2024'));
        $this->assertEquals('2024-01-15', $method->invoke($service, '15-01-2024'));
        
        // Test invalid date
        $this->assertNull($method->invoke($service, 'invalid date'));
        $this->assertNull($method->invoke($service, null));
    }

    public function test_can_parse_amount(): void
    {
        $service = new FinancialOverviewService();
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseAmount');
        
        // Test numeric values
        $this->assertEquals(1000.0, $method->invoke($service, 1000));
        $this->assertEquals(1500.50, $method->invoke($service, 1500.50));
        
        // Test formatted strings
        $this->assertEquals(1000000.0, $method->invoke($service, 'Rp 1.000.000'));
        $this->assertEquals(500000.0, $method->invoke($service, 'Rp. 500.000,-'));
        $this->assertEquals(250000.0, $method->invoke($service, '250,000'));
        
        // Test invalid values
        $this->assertNull($method->invoke($service, 'invalid'));
        $this->assertNull($method->invoke($service, null));
    }
}
