<?php

namespace App\Exports;

use App\Models\Umkm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UmkmTemplateExport
{
    protected Umkm $umkm;

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
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Produk');

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

        return $spreadsheet;
    }

    /**
     * Download the template as a response
     */
    public function toResponse(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->generate();
        $writer = new Xlsx($spreadsheet);

        $filename = 'Template_Produk_'.str_replace(' ', '_', $this->umkm->name).'_'.date('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
