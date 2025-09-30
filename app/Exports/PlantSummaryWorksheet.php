<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use App\Models\Standard;
use App\Models\ProductionReport;
use App\Models\MaterialUtilizationAnalytics;

class PlantSummaryWorksheet implements FromArray, WithTitle, WithEvents, WithCustomStartCell
{
    protected $year;
    protected $lines;

    public function __construct($year)
    {
        $this->year = $year;

        // Auto-detect available lines for this year
        $this->lines = ProductionReport::whereYear('production_date', $year)
            ->distinct()
            ->pluck('line')
            ->sort()
            ->toArray();
    }

    /** Required by Maatwebsite Excel */
    public function array(): array
    {
        // We manually fill rows in registerEvents()
        return [];
    }

    public function title(): string
    {
        return "{$this->year} Plant Summary per SKU";
    }

    public function startCell(): string
    {
        return 'A9';
    }

    /** ✅ Styling */
public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Add top report headers
            $this->addReportHeaders($sheet);

            // Row pointer to know where to draw the next line block
            $rowPointer = 7;
            foreach ($this->lines as $line) {
                // Header + subheaders
                $this->styleTable($sheet, $rowPointer, $line);

                // Data block
                $startRow = $rowPointer + 3;  // first SKU row
                $data = $this->prepareData($line, $rowPointer);
                $sheet->fromArray($data, null, "A{$startRow}");

                $endRow = $startRow + count($data) - 1;

                // Style + summary
                $this->styleDataRows($sheet, $startRow, $endRow);
                $this->addSummaryRows($sheet, $endRow, $line, $rowPointer);

                // Move pointer below this block
                $rowPointer = $endRow + 5;
            }

            // ✅ Add Plant Total after last line
$this->addPlantTotalTable($sheet, $rowPointer);

            $this->autoSizeColumns($sheet);
            $this->setZoomLevel($sheet);
            $sheet->freezePane('C10');
        },
    ];
}




    /** ✅ Custom top report header */
private function addReportHeaders(Worksheet $sheet): void
{
    $lastColIndex = 2 + (12 * 3) + 3; 
    $highestCol   = Coordinate::stringFromColumnIndex($lastColIndex);

    $sheet->mergeCells("A1:{$highestCol}1");
    $sheet->mergeCells("A2:{$highestCol}2");
    $sheet->mergeCells("A3:{$highestCol}3");

    $sheet->setCellValue('A1', "MAINTENANCE DEPARTMENT");
    $sheet->setCellValue('A2', "MATERIAL UTILIZATION REPORT - PLANT TOTAL");
    $sheet->setCellValue('A3', "Covering period: January - December, {$this->year}");

    // Header font + alignment
    $sheet->getStyle("A1:A2")->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Underlined covering period
    $sheet->getStyle("A3")->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 11,
            'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Sizes
    $sheet->getStyle("A1")->getFont()->setSize(20);
    $sheet->getStyle("A2")->getFont()->setSize(16);

    $sheet->getRowDimension(1)->setRowHeight(28);
    $sheet->getRowDimension(2)->setRowHeight(24);
    $sheet->getRowDimension(3)->setRowHeight(20);
}





    /** ✅ Complex header styling */
private function styleTable(Worksheet $sheet, int $rowPointer, int|string $line): void
{
    // === LINE HEADER (yellow row) ===
$sheet->mergeCells("A{$rowPointer}:B{$rowPointer}");

// Dynamic label: handles both numeric line numbers and "PLANT TOTAL"
$label = is_numeric($line) ? "LINE {$line}" : strtoupper($line);
$sheet->setCellValue("A{$rowPointer}", $label);

    $lastColIndex = 2 + (12 * 3) + 3; // 2 fixed cols + 12 months × 3 + 3 totals
    $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

    $sheet->mergeCells("C{$rowPointer}:{$lastCol}{$rowPointer}");
    $sheet->getStyle("A{$rowPointer}:{$lastCol}{$rowPointer}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 20],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // === HEADER ROWS (monthRow + subRow) ===
    $monthRow = $rowPointer + 1;
    $subRow   = $rowPointer + 2;

    // SIZE + SKU
    $sheet->mergeCells("A{$monthRow}:A{$subRow}");
    $sheet->setCellValue("A{$monthRow}", "SIZE");
    $sheet->mergeCells("B{$monthRow}:B{$subRow}");
    $sheet->setCellValue("B{$monthRow}", "SKU");

    $sheet->getStyle("A{$monthRow}:B{$subRow}")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0070C0'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
    ]);

    // === MONTH HEADERS ===
    $months = [
        'JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
        'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'
    ];

    $colIndex = 3; // start at column C
    foreach ($months as $month) {
        $col1 = Coordinate::stringFromColumnIndex($colIndex);
        $col2 = Coordinate::stringFromColumnIndex($colIndex + 1);
        $col3 = Coordinate::stringFromColumnIndex($colIndex + 2);

        // Merge month name row
        $sheet->mergeCells("{$col1}{$monthRow}:{$col3}{$monthRow}");
        $sheet->setCellValue("{$col1}{$monthRow}", $month);

        // Subheaders
        $sheet->setCellValue("{$col1}{$subRow}", "Production, Cs");
        $sheet->setCellValue("{$col2}{$subRow}", "Preforms, Pcs");
        $sheet->setCellValue("{$col3}{$subRow}", "Caps, Pcs");

        // Month header style
        $sheet->getStyle("{$col1}{$monthRow}:{$col3}{$monthRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FDE9D9'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Subheader styles
        $sheet->getStyle("{$col1}{$subRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D8E4BC'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("{$col2}{$subRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'B7DEE8'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("{$col3}{$subRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCC0DA'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $colIndex += 3;
    }

    // === TOTAL COLUMNS ===
    $totalCol1 = Coordinate::stringFromColumnIndex($colIndex);
    $totalCol2 = Coordinate::stringFromColumnIndex($colIndex + 1);
    $totalCol3 = Coordinate::stringFromColumnIndex($colIndex + 2);

    $sheet->mergeCells("{$totalCol1}{$monthRow}:{$totalCol3}{$monthRow}");
    $sheet->setCellValue("{$totalCol1}{$monthRow}", "TOTAL");

    $sheet->setCellValue("{$totalCol1}{$subRow}", "Production, Cs");
    $sheet->setCellValue("{$totalCol2}{$subRow}", "Preforms, Pcs");
    $sheet->setCellValue("{$totalCol3}{$subRow}", "Caps, Pcs");

    $sheet->getStyle("{$totalCol1}{$monthRow}:{$totalCol3}{$monthRow}")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FDE9D9'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    $sheet->getStyle("{$totalCol1}{$subRow}")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D8E4BC'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getStyle("{$totalCol2}{$subRow}")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'B7DEE8'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getStyle("{$totalCol3}{$subRow}")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'CCC0DA'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
}


/**
 * ✅ Apply styling to data rows (SIZE + SKU and beyond)
 */
private function styleDataRows(Worksheet $sheet, int $startRow, int $endRow): void
{
    // ✅ Column A (SIZE) → center
    $sheet->getStyle("A{$startRow}:A{$endRow}")
        ->applyFromArray([
            'font' => ['bold' => false],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBE5F1'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

    // ✅ Column B (SKU) → left
    $sheet->getStyle("B{$startRow}:B{$endRow}")
        ->applyFromArray([
            'font' => ['bold' => false],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBE5F1'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

    // ✅ Columns C → Last (monthly + TOTAL data) → center
    $highestCol = $sheet->getHighestColumn();
    $sheet->getStyle("C{$startRow}:{$highestCol}{$endRow}")
        ->applyFromArray([
            'font' => ['bold' => false],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);
}



private function prepareData(int $line, int $rowPointer): array
{
    $rows = [];
    $startRow = $rowPointer + 3;

    // Sort standards by size
    $standards = Standard::all()->sortBy(function ($item) {
        if (preg_match('/(\d+(\.\d+)?)\s*(ml|l)/i', $item->size, $matches)) {
            $value = (float) $matches[1];
            $unit  = strtolower($matches[3]);
            return $unit === 'l' ? $value * 900 : $value;
        }
        return PHP_INT_MAX;
    });

    $lastSize = null;

    foreach ($standards as $standard) {
        if ($lastSize !== null && $standard->size !== $lastSize) {
            $rows[] = array_fill(0, 2 + (12 * 3) + 3, null);
        }

        $row = [$standard->size, $standard->description];

        // === Monthly data ===
        for ($m = 1; $m <= 12; $m++) {
        $monthly = MaterialUtilizationAnalytics::selectRaw("
                COALESCE(SUM(total_output),0) as total_cases,
                COALESCE(SUM(preform_rej),0) as total_preforms,
                COALESCE(SUM(caps_rej),0) as total_caps
            ")
            ->where('sku', $standard->description)
            ->where('line', $line)   // ✅ filter by specific line only
            ->whereYear('production_date', $this->year)
            ->whereMonth('production_date', $m)
            ->first();

            $row[] = $monthly->total_cases ?: null;
            $row[] = $monthly->total_preforms ?: null;
            $row[] = $monthly->total_caps ?: null;
        }

        // === Totals per SKU row ===
        $rowIndex = $startRow + count($rows);

        $startColIndex = 3;
        $prodCols = $preformCols = $capCols = [];

        for ($i = 0; $i < 12; $i++) {
            $prodCols[]    = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3))     . $rowIndex;
            $preformCols[] = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3) + 1) . $rowIndex;
            $capCols[]     = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3) + 2) . $rowIndex;
        }

        $row[] = "=IF(SUM(" . implode(",", $prodCols) . ")=0,\"\",SUM(" . implode(",", $prodCols) . "))";
        $row[] = "=IF(SUM(" . implode(",", $preformCols) . ")=0,\"\",SUM(" . implode(",", $preformCols) . "))";
        $row[] = "=IF(SUM(" . implode(",", $capCols) . ")=0,\"\",SUM(" . implode(",", $capCols) . "))";

        $rows[] = $row;
        $lastSize = $standard->size;
    }

    return $rows;
}


private function addSummaryRows(Worksheet $sheet, int $endRow, int|string $line, int $rowPointer): void
{
    $labels = [
        "TOTAL >>>> 11/12 Grams",
        "TOTAL >>>> 21 Grams",
        is_numeric($line) ? "Line {$line} Total" : strtoupper($line) . " Total", // ✅ dynamic
    ];
    
    // Define size ranges for each row
    $conditions = [
        '11/12' => ['min' => 350, 'max' => 500],
        '21'    => ['min' => 1000, 'max' => 1000],
        'all'   => null,
    ];

    $highestCol = $sheet->getHighestColumn();
    $lastIndex  = Coordinate::columnIndexFromString($highestCol);

    foreach ($labels as $i => $label) {
        $rowNum = $endRow + $i + 1;
        $conditionKey = $i === 0 ? '11/12' : ($i === 1 ? '21' : 'all');

        $sheet->setCellValue("B{$rowNum}", $label);

        // ✅ Loop only inside this block (not global row 10)
        for ($col = 3; $col <= $lastIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $ranges = [];

            for ($r = $rowPointer + 3; $r <= $endRow; $r++) {
                $size = $sheet->getCell("A{$r}")->getValue();
                if ($size && preg_match('/(\d+(\.\d+)?)\s*(ml|l)/i', $size, $matches)) {
                    $value = (float) $matches[1];
                    $unit  = strtolower($matches[3]);
                    $ml    = $unit === 'l' ? $value * 1000 : $value;

                    $cond = $conditions[$conditionKey] ?? null;
                    if ($cond === null || ($ml >= $cond['min'] && $ml <= $cond['max'])) {
                        $ranges[] = "{$colLetter}{$r}";
                    }
                }
            }

            if (!empty($ranges)) {
                $formula = "=IFERROR(SUM(" . implode(",", $ranges) . "),0)";
                $sheet->setCellValue("{$colLetter}{$rowNum}", $formula);
            }
        }

        // ✅ Apply dark style to ALL summary rows
        $range = "A{$rowNum}:{$highestCol}{$rowNum}";
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // white text
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '595959'], // dark gray
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'], // white border
                ],
            ],
        ]);
    }
}



/**
 * Add Plant Total table after the last line block
 */
private function addPlantTotalTable(Worksheet $sheet, int $rowPointer): void
{
    // --- Plant Total Header + Subheaders (reuse styleTable logic) ---
    $this->styleTable($sheet, $rowPointer, "PLANT TOTAL");

    // Data block starts 3 rows below header
    $startRow = $rowPointer + 3;

    // Get aggregated data across ALL lines
    $data = $this->preparePlantTotalData($rowPointer);
    $sheet->fromArray($data, null, "A{$startRow}");

    $endRow = $startRow + count($data) - 1;

    // Apply styling to rows
    $this->styleDataRows($sheet, $startRow, $endRow);

    // Add summary rows (11/12g, 21g, Plant Total)
    $this->addSummaryRows($sheet, $endRow, "PLANT", $rowPointer);
}

/**
 * Prepare aggregated Plant Total data across all lines
 */
private function preparePlantTotalData(int $rowPointer): array
{
    $rows = [];
    $startRow = $rowPointer + 3;

    $standards = Standard::all()->sortBy(function ($item) {
        if (preg_match('/(\d+(\.\d+)?)\s*(ml|l)/i', $item->size, $matches)) {
            $value = (float) $matches[1];
            $unit  = strtolower($matches[3]);
            return $unit === 'l' ? $value * 900 : $value;
        }
        return PHP_INT_MAX;
    });

    $lastSize = null;

    foreach ($standards as $standard) {
        if ($lastSize !== null && $standard->size !== $lastSize) {
            $rows[] = array_fill(0, 2 + (12 * 3) + 3, null);
        }

        $row = [$standard->size, $standard->description];

        // === Monthly data across ALL lines ===
        for ($m = 1; $m <= 12; $m++) {
            $monthly = MaterialUtilizationAnalytics::selectRaw("
                    COALESCE(SUM(total_output),0) as total_cases,
                    COALESCE(SUM(preform_rej),0) as total_preforms,
                    COALESCE(SUM(caps_rej),0) as total_caps
                ")
                ->where('sku', $standard->description)
                ->whereYear('production_date', $this->year)
                ->whereMonth('production_date', $m)
                ->first();

            $row[] = $monthly->total_cases ?: null;
            $row[] = $monthly->total_preforms ?: null;
            $row[] = $monthly->total_caps ?: null;
        }

        // Totals per SKU row
        $rowIndex = $startRow + count($rows);

        $startColIndex = 3;
        $prodCols = $preformCols = $capCols = [];

        for ($i = 0; $i < 12; $i++) {
            $prodCols[]    = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3))     . $rowIndex;
            $preformCols[] = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3) + 1) . $rowIndex;
            $capCols[]     = Coordinate::stringFromColumnIndex($startColIndex + ($i * 3) + 2) . $rowIndex;
        }

        $row[] = "=IF(SUM(" . implode(",", $prodCols) . ")=0,\"\",SUM(" . implode(",", $prodCols) . "))";
        $row[] = "=IF(SUM(" . implode(",", $preformCols) . ")=0,\"\",SUM(" . implode(",", $preformCols) . "))";
        $row[] = "=IF(SUM(" . implode(",", $capCols) . ")=0,\"\",SUM(" . implode(",", $capCols) . "))";

        $rows[] = $row;
        $lastSize = $standard->size;
    }

    return $rows;
}



    /**
     * Auto-size all columns for better readability
     * 
     * @param Worksheet $sheet
     */
private function autoSizeColumns(Worksheet $sheet): void
{
    // Set reasonable width for SIZE and SKU
    $sheet->getColumnDimension('A')->setWidth(12);  // SIZE
    $sheet->getColumnDimension('B')->setWidth(width: 35);  // SKU description

    // Get last column dynamically (should be AL for Dec Caps)
    $highestCol = $sheet->getHighestColumn();
    $lastIndex  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

    // Loop C → Last
    for ($i = 3; $i <= $lastIndex; $i++) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        $sheet->getColumnDimension($col)->setWidth(20); // wide enough to stay in one line
    }

    // ✅ Ensure subheaders (row 9) do not wrap
    $sheet->getStyle("C9:{$highestCol}9")
        ->getAlignment()
        ->setWrapText(false)
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
}


    /** ✅ Set zoom */
    private function setZoomLevel(Worksheet $sheet): void
    {
        $sheet->getParent()->getActiveSheet()->getSheetView()->setZoomScale(80);
    }
}
