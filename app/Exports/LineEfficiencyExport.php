<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class LineEfficiencyExport implements FromArray, WithHeadings, WithStyles
{
    private $line;
    private $month;
    private $year;
    private $data;
    private $tableHeaders;
    private $chartData;

    public function __construct($line, $month, $year, $data, $tableHeaders, $chartData = null)
    {
        $this->line = $line;
        $this->month = $month;
        $this->year = $year;
        $this->data = $data;
        $this->tableHeaders = $tableHeaders;
        $this->chartData = $chartData;
    }

    public function headings(): array
    {
        return [
            ["                       MAINTENANCE DEPARTMENT"],                                                    // Row 1
            ["                       MATERIAL UTILIZATION REPORT - " . strtoupper($this->line)],               // Row 2
            ["                                Covering period: " . $this->month . ", " . $this->year],                  // Row 3
            [""],                                                                        // Row 4 (empty)
            $this->tableHeaders ?: ["Column 1", "Column 2", "Column 3"]                // Row 5 (table headers)
        ];
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest column for proper merging
        $highestCol = $sheet->getHighestColumn();
        $dataRowCount = count($this->data);
        $totalRows = 10 + $dataRowCount; // 5 header rows + data rows

        // Merge header cells across all columns
        $sheet->mergeCells("A1:{$highestCol}1");
        $sheet->mergeCells("A2:{$highestCol}2");
        $sheet->mergeCells("A3:{$highestCol}3");

        // Style the main headers
        $sheet->getStyle("A1:A2")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ]
        ]);

        // Style the period header
        $sheet->getStyle("A3")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ]
        ]);

        // Style the table headers (row 5)
        $sheet->getStyle("A5:{$highestCol}5")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Add borders to the data table (headers + data rows)
        $sheet->getStyle("A5:{$highestCol}{$totalRows}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
    }
}