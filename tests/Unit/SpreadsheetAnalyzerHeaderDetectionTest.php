<?php

namespace Tests\Unit;

use App\Services\SpreadsheetAnalyzerService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class SpreadsheetAnalyzerHeaderDetectionTest extends TestCase
{
    private string $tempFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        $uniqueName = uniqid('test_template_', true).'.xlsx';
        $this->tempFilePath = $uniqueName; // Path relative to storage/app/private
    }

    protected function tearDown(): void
    {
        $fullPath = storage_path('app/private/'.$this->tempFilePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        parent::tearDown();
    }

    private function saveSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $writer = new Xlsx($spreadsheet);
        $fullPath = storage_path('app/private/'.$this->tempFilePath);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }
        
        $writer->save($fullPath);
    }

    public function test_analyzer_detects_headers_on_row_6_with_metadata(): void
    {
        // Create a spreadsheet that mimics the template structure
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Produk');

        // Add metadata rows 1-5 (like in the template)
        $sheet->setCellValue('A1', 'TEMPLATE PRODUK/JASA');
        $sheet->setCellValue('A2', 'Nama UMKM:');
        $sheet->setCellValue('B2', 'Test UMKM');
        $sheet->setCellValue('A3', 'Alamat:');
        $sheet->setCellValue('B3', 'Jl. Test 123');
        $sheet->setCellValue('A4', 'Kota:');
        $sheet->setCellValue('B4', 'Jakarta');
        $sheet->setCellValue('A5', 'Telepon:');
        $sheet->setCellValue('B5', '081234567890');

        // Add headers on row 6 (like in the template)
        $sheet->setCellValue('A6', 'Nama Produk');
        $sheet->setCellValue('B6', 'Tipe (Produk/Jasa)');
        $sheet->setCellValue('C6', 'Kategori');
        $sheet->setCellValue('D6', 'Deskripsi');
        $sheet->setCellValue('E6', 'Harga');
        $sheet->setCellValue('F6', 'Stok');
        $sheet->setCellValue('G6', 'Status (Aktif/Nonaktif)');

        // Add sample data rows
        $sheet->setCellValue('A7', 'Produk Test 1');
        $sheet->setCellValue('B7', 'Produk');
        $sheet->setCellValue('C7', 'Makanan');
        $sheet->setCellValue('D7', 'Deskripsi produk test');
        $sheet->setCellValue('E7', 15000);
        $sheet->setCellValue('F7', 100);
        $sheet->setCellValue('G7', 'Aktif');

        $sheet->setCellValue('A8', 'Produk Test 2');
        $sheet->setCellValue('B8', 'Jasa');
        $sheet->setCellValue('C8', 'Layanan');
        $sheet->setCellValue('D8', 'Deskripsi jasa test');
        $sheet->setCellValue('E8', 50000);
        $sheet->setCellValue('F8', 0);
        $sheet->setCellValue('G8', 'Aktif');

        // Save and analyze
        $this->saveSpreadsheet($spreadsheet);
        
        $analyzer = new SpreadsheetAnalyzerService();
        $result = $analyzer->analyze($this->tempFilePath);

        // Verify that headers are correctly detected
        $this->assertArrayHasKey('sheet_details', $result);
        $this->assertArrayHasKey('Template Produk', $result['sheet_details']);

        $sheetDetails = $result['sheet_details']['Template Produk'];
        
        // Check that headers are correct (from row 6, not row 1)
        $this->assertContains('Nama Produk', $sheetDetails['headers']);
        $this->assertContains('Harga', $sheetDetails['headers']);
        $this->assertContains('Stok', $sheetDetails['headers']);
        
        // Should NOT contain metadata from row 1
        $this->assertNotContains('TEMPLATE PRODUK/JASA', $sheetDetails['headers']);
        
        // Check that data row count is correct (2 data rows, not including metadata or header)
        $this->assertEquals(2, $sheetDetails['jumlah_baris']);
    }

    public function test_analyzer_handles_traditional_format_with_headers_on_row_1(): void
    {
        // Create a traditional spreadsheet with headers on row 1
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers on row 1
        $sheet->setCellValue('A1', 'Nama Produk');
        $sheet->setCellValue('B1', 'Harga');
        $sheet->setCellValue('C1', 'Stok');

        // Add data rows
        $sheet->setCellValue('A2', 'Produk A');
        $sheet->setCellValue('B2', 10000);
        $sheet->setCellValue('C2', 50);

        $sheet->setCellValue('A3', 'Produk B');
        $sheet->setCellValue('B3', 20000);
        $sheet->setCellValue('C3', 30);

        // Save and analyze
        $this->saveSpreadsheet($spreadsheet);
        
        $analyzer = new SpreadsheetAnalyzerService();
        $result = $analyzer->analyze($this->tempFilePath);

        // Verify that headers are correctly detected from row 1
        $sheetDetails = $result['sheet_details']['Worksheet'];
        
        $this->assertContains('Nama Produk', $sheetDetails['headers']);
        $this->assertContains('Harga', $sheetDetails['headers']);
        $this->assertContains('Stok', $sheetDetails['headers']);
        
        // Check that data row count is correct (2 data rows)
        $this->assertEquals(2, $sheetDetails['jumlah_baris']);
    }
}
