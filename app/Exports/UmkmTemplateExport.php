<?php

namespace App\Exports;

use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Umkm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UmkmTemplateExport
{
    protected Umkm $umkm;

    private const PRODUCT_DATA_START_ROW = 7;
    private const PRODUCT_MAX_ROWS = 50;

    private const FIN_DATA_START_ROW = 5;
    private const FIN_MAX_ROWS = 100;

    public function __construct(Umkm $umkm)
    {
        $this->umkm = $umkm;
    }

    /**
     * Generate and return the spreadsheet
     */
    public function generate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        
        // Create first sheet - Template Produk
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Produk');
        $this->createProductSheet($sheet);
        
        // Create second sheet - Pemasukan & Pengeluaran
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Pemasukan & Pengeluaran');
        $this->createIncomeExpenseSheet($sheet2);

        return $spreadsheet;
    }
    
    /**
     * Create the product template sheet
     */
    protected function createProductSheet($sheet): void
    {
        // Add UMKM information at the top
        $sheet->setCellValue('A1', 'TEMPLATE PRODUK/JASA');
        $sheet->setCellValue('A2', 'Nama UMKM:');
        $sheet->setCellValue('B2', $this->umkm->name);
        $sheet->setCellValue('A3', 'Alamat:');
        $sheet->setCellValue('B3', $this->umkm->address ?? '-');
        $sheet->setCellValue('A4', 'Kota:');
        $sheet->setCellValue('B4', $this->umkm->city ?? '-');
        $sheet->setCellValue('A5', 'Telepon:');
        $sheet->setCellValue('B5', $this->umkm->phone ?? '-');

        // Merge cells for title
        $sheet->mergeCells('A1:G1');

        // Style the title row
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
        ]);

        // Style UMKM info rows
        $sheet->getStyle('A2:A5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Add table headers (row 6)
        $headers = [
            'Nama Produk',
            'Tipe (Produk/Jasa)',
            'Kategori',
            'Deskripsi',
            'Harga',
            'Stok',
            'Status (Aktif/Nonaktif)',
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'6', $header);
            $col++;
        }

        // Style the header row
        $sheet->getStyle('A6:G6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply borders to data cells (50 rows)
        $sheet->getStyle('A6:G56')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Center align certain columns
        $sheet->getStyle('B7:B56')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G7:G56')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column number formats
        $sheet->getStyle('E7:E56')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('F7:F56')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(6)->setRowHeight(25);

        // Auto-size columns
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $this->prefillProducts($sheet);
    }
    
    /**
     * Create the income & expense sheet
     */
    protected function createIncomeExpenseSheet($sheet): void
    {
        // Add title
        $sheet->setCellValue('A1', 'PEMASUKAN & PENGELUARAN');
        $sheet->setCellValue('A2', 'Nama UMKM:');
        $sheet->setCellValue('B2', $this->umkm->name);
        
        // Merge cells for title
        $sheet->mergeCells('A1:E1');
        
        // Style the title row
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
        ]);
        
        // Style UMKM info row
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        
        // Add table headers (row 4)
        $headers = [
            'No',
            'Tanggal',
            'Jenis Transaksi',
            'Keterangan',
            'Nominal (Rp)',
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'4', $header);
            $col++;
        }
        
        // Style the header row
        $sheet->getStyle('A4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0284C7'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        
        // Apply borders to data cells (100 rows for transactions)
        $sheet->getStyle('A4:E104')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        
        // Center align certain columns
        $sheet->getStyle('A5:A104')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5:C104')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set column number formats
        $sheet->getStyle('E5:E104')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(25);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(20);

        $this->prefillFinancialTransactions($sheet);
    }

    private function prefillProducts($sheet): void
    {
        $products = $this->umkm->products()
            ->with('category')
            ->orderBy('created_at')
            ->limit(self::PRODUCT_MAX_ROWS)
            ->get();

        $row = self::PRODUCT_DATA_START_ROW;
        foreach ($products as $product) {
            $sheet->setCellValue('A'.$row, $product->name);
            $sheet->setCellValue('B'.$row, $product->type === Product::TYPE_SERVICE ? 'Jasa' : 'Produk');
            $sheet->setCellValue('C'.$row, $product->category?->name ?? '');
            $sheet->setCellValue('D'.$row, $product->description ?? '');
            $sheet->setCellValue('E'.$row, (int) ($product->price ?? 0));
            $sheet->setCellValue('F'.$row, (int) ($product->stock ?? 0));
            $sheet->setCellValue('G'.$row, $product->is_active ? 'Aktif' : 'Nonaktif');
            $row++;
        }
    }

    private function prefillFinancialTransactions($sheet): void
    {
        $transactions = FinancialTransaction::query()
            ->where('umkm_id', $this->umkm->id)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->limit(self::FIN_MAX_ROWS)
            ->get(['transaction_date', 'transaction_type', 'description', 'amount']);

        $row = self::FIN_DATA_START_ROW;
        $no = 1;
        foreach ($transactions as $tx) {
            $sheet->setCellValue('A'.$row, $no);

            $date = $tx->transaction_date;
            if ($date) {
                // Put as Excel date number for better sorting/formatting in Excel
                $sheet->setCellValue('B'.$row, ExcelDate::PHPToExcel($date->toDateTime()));
                $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            } else {
                $sheet->setCellValue('B'.$row, '');
            }

            $sheet->setCellValue('C'.$row, $tx->transaction_type);
            $sheet->setCellValue('D'.$row, $tx->description ?? '');
            $sheet->setCellValue('E'.$row, (float) ($tx->amount ?? 0));

            $row++;
            $no++;
        }
    }

    /**
     * Download the template as a response
     */
    public function toResponse(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->generate();
        $writer = new Xlsx($spreadsheet);

        // Sanitize filename to prevent injection attacks
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->umkm->name);
        $filename = 'Template_Produk_'.$safeName.'_'.date('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
