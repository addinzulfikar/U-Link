<?php

namespace Tests\Unit;

use App\Exports\UmkmTemplateExport;
use App\Models\Umkm;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class UmkmTemplateExportTest extends TestCase
{
    public function test_template_has_two_sheets(): void
    {
        // Create a mock UMKM object
        $umkm = new Umkm([
            'name' => 'Test UMKM',
            'address' => 'Test Address',
            'city' => 'Test City',
            'phone' => '08123456789',
        ]);

        $exporter = new UmkmTemplateExport($umkm);
        $spreadsheet = $exporter->generate();

        // Assert that the spreadsheet has exactly 2 sheets
        $this->assertEquals(2, $spreadsheet->getSheetCount(), 'Spreadsheet should have 2 sheets');
    }

    public function test_first_sheet_is_product_template(): void
    {
        $umkm = new Umkm([
            'name' => 'Test UMKM',
            'address' => 'Test Address',
            'city' => 'Test City',
            'phone' => '08123456789',
        ]);

        $exporter = new UmkmTemplateExport($umkm);
        $spreadsheet = $exporter->generate();

        $firstSheet = $spreadsheet->getSheet(0);
        
        // Assert sheet name
        $this->assertEquals('Template Produk', $firstSheet->getTitle());
        
        // Assert title
        $this->assertEquals('TEMPLATE PRODUK/JASA', $firstSheet->getCell('A1')->getValue());
        
        // Assert UMKM info
        $this->assertEquals('Nama UMKM:', $firstSheet->getCell('A2')->getValue());
        $this->assertEquals('Test UMKM', $firstSheet->getCell('B2')->getValue());
        
        // Assert headers exist
        $this->assertEquals('Nama Produk', $firstSheet->getCell('A6')->getValue());
        $this->assertEquals('Tipe (Produk/Jasa)', $firstSheet->getCell('B6')->getValue());
        $this->assertEquals('Kategori', $firstSheet->getCell('C6')->getValue());
        $this->assertEquals('Deskripsi', $firstSheet->getCell('D6')->getValue());
        $this->assertEquals('Harga', $firstSheet->getCell('E6')->getValue());
        $this->assertEquals('Stok', $firstSheet->getCell('F6')->getValue());
        $this->assertEquals('Status (Aktif/Nonaktif)', $firstSheet->getCell('G6')->getValue());
    }

    public function test_second_sheet_is_income_expense_template(): void
    {
        $umkm = new Umkm([
            'name' => 'Test UMKM',
            'address' => 'Test Address',
            'city' => 'Test City',
            'phone' => '08123456789',
        ]);

        $exporter = new UmkmTemplateExport($umkm);
        $spreadsheet = $exporter->generate();

        $secondSheet = $spreadsheet->getSheet(1);
        
        // Assert sheet name
        $this->assertEquals('Pemasukan & Pengeluaran', $secondSheet->getTitle());
        
        // Assert title
        $this->assertEquals('PEMASUKAN & PENGELUARAN', $secondSheet->getCell('A1')->getValue());
        
        // Assert UMKM info
        $this->assertEquals('Nama UMKM:', $secondSheet->getCell('A2')->getValue());
        $this->assertEquals('Test UMKM', $secondSheet->getCell('B2')->getValue());
        
        // Assert headers exist (row 4)
        $this->assertEquals('No', $secondSheet->getCell('A4')->getValue());
        $this->assertEquals('Tanggal', $secondSheet->getCell('B4')->getValue());
        $this->assertEquals('Jenis Transaksi', $secondSheet->getCell('C4')->getValue());
        $this->assertEquals('Keterangan', $secondSheet->getCell('D4')->getValue());
        $this->assertEquals('Nominal (Rp)', $secondSheet->getCell('E4')->getValue());
    }

    public function test_second_sheet_has_correct_column_structure(): void
    {
        $umkm = new Umkm([
            'name' => 'Test UMKM',
        ]);

        $exporter = new UmkmTemplateExport($umkm);
        $spreadsheet = $exporter->generate();

        $secondSheet = $spreadsheet->getSheet(1);
        
        // Verify we have exactly 5 columns as per requirement
        $highestColumn = $secondSheet->getHighestColumn();
        $this->assertEquals('E', $highestColumn, 'Second sheet should have 5 columns (A-E)');
        
        // Verify headers are in row 4
        $headerRow = 4;
        $headers = [
            'A' => 'No',
            'B' => 'Tanggal',
            'C' => 'Jenis Transaksi',
            'D' => 'Keterangan',
            'E' => 'Nominal (Rp)',
        ];
        
        foreach ($headers as $col => $expectedHeader) {
            $actualHeader = $secondSheet->getCell($col . $headerRow)->getValue();
            $this->assertEquals($expectedHeader, $actualHeader, "Column $col should have header '$expectedHeader'");
        }
    }
}
