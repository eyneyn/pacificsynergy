<?php

namespace App\Exports;

use App\Models\ProductionReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use App\Models\Line;



/**
 * Line Utilization Export Class
 * 
 * This class generates comprehensive Excel reports for Line utilization
 * including production data, quarterly summaries, and trend charts.
 */
class LineOverallSummaryWorksheet implements FromArray, WithHeadings, WithEvents, WithCustomStartCell, WithStyles, WithCharts, WithTitle
{
// Class properties
    protected $line;          // Production line identifier
    protected $month;         // Report month
    protected $monthName;     // Report month name
    protected $year;          // Report year
    protected $data;          // Prepared report data
    public $quarterlyData;       // quarterly aggregated data

    // Constants for styling colors
    const PREFORM_COLOR = '808080';
    const CAPS_COLOR = '16365C';
    const OPP_LABELS_COLOR = '4F6228';
    const LDPE_COLOR = '974706';
    const HEADER_COLOR = '0070C0';
    const YELLOW_FILL = 'FFFF00';
    const LIGHT_FILL_DBE5F1 = 'DBE5F1';
    const LIGHT_FILL_F2F2F2 = 'F2F2F2';
    const TARGET_COLOR   = '198754'; // Green
    const PRODUCTION_COLOR = '23527c'; // Teal
    private const LINE_TAB_COLORS = [
        1 => '92CDDC', // Line 1
        2 => 'B1A0C7', // Line 2
        // Add more lines/colors here
    ];
    /**
     * Constructor - Initialize report parameters and prepare data
     * 
     * @param mixed $line Production line
     * @param int $month Report month
     * @param int $year Report year
     */
    public function __construct($year)
    {
        $this->year = $year;
        
        // Prepare main table data
        $this->data = $this->prepareData();
    }

    /**
     * Return data array for Excel export
     * 
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }
    /**
     * Define table headers starting from row 29
     * 
     * @return array
     */
    public function title(): string
    {
        // Add spacing for readability
        return "{$this->year} Plant Summary";
    }
    public function headings(): array
    {
        return [
            " ", 
            "Period",
            "Total Vol., in cases",
            "Target Mat'l Efficiency, %",
            "Vol. Line 1, cases",
            "Vol. Line 2, cases",
            "% Vol.  Contribution Line 1",
            "% Vol. Contribution Line 2",
            "Target LE, %",
            "FG Usage (Caps)",
            "Rejects (Caps)",
            "QA Samples (Caps)",
            "% Rejects (Caps)",
            "FG Usage (Labels)",
            "Rejects (Labels)",
            "QA Samples (Labels)",
            "% Rejects (Labels)",
            "FG Usage (LDPE)",
            "Rejects (LDPE)",
            "QA Samples (LDPE)",
            "% Rejects (LDPE)",
        ];
    }
    /**
     * Define starting cell for data (row 29)
     * 
     * @return string
     */
    public function startCell(): string
    {
        return 'A29';
    }

    /**
     * Apply basic styles to specific rows
     * 
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 20]],  // Main header
            2  => ['font' => ['bold' => true, 'size' => 16]],  // Sub header
            3  => ['font' => ['bold' => true, 'size' => 11]],  // Period info
            31 => ['font' => ['bold' => true, 'size' => 10]],  // Table headers
        ];
    }
    /**
     * Register events for advanced sheet formatting
     * 
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $startRow = 31; // or your actual data start row
                $endRow   = $startRow + count($this->data) - 1; // adjust to your dataset
            if (count($this->data) > 0) {
                $this->applyRejectsConditionalFormatting($sheet, $startRow, $endRow);
            }
            
            // ✅ Set tab color based on line number
            $color = self::LINE_TAB_COLORS[$this->line] ?? '00B050'; // Default white if undefined
            $sheet->getTabColor()->setRGB($color);
                // Apply all formatting methods
                $this->addReportHeaders($sheet);
                $this->addLineHeaders($sheet);
                $this->addPTDLineEfficiency($sheet);
                $this->addYellowFillRow($sheet);
                $this->addTableHeaders($sheet);
                $this->populateDataRows($sheet);
                $this->charts($sheet);
                $sheetTitle   = $this->title();
                $dataStartRow = 31;
                $dataEndRow   = $dataStartRow + count($this->data) - 1;
                $leftCol      = 'B';
                $rightCol     = $this->colNameFromIndex(count($this->headings())); // last data col
                $chartBottom  = 19; // same value you used in charts()
                $this->addChartFooterTable($sheet, $dataStartRow, $dataEndRow, $leftCol, $rightCol, $chartBottom + 1);
                $this->autoSizeColumns($sheet);
                $this->setZoomLevel($sheet);
            },
        ];
    }
    // Convert 1-based column index to Excel letters (1=A, 26=Z, 27=AA, ...)
    private function colNameFromIndex(int $n): string {
        $s = '';
        while ($n > 0) { $n--; $s = chr(65 + ($n % 26)) . $s; $n = intdiv($n, 26); }
        return $s;
    }
    // Convert Excel letters to 1-based index (A=1, Z=26, AA=27, ...)
    private function colIndexFromName(string $name): int {
        $name = strtoupper($name); $n = 0;
        for ($i=0; $i<strlen($name); $i++) { $n = $n*26 + (ord($name[$i]) - 64); }
        return $n;
    }
    /**
     * Add main report headers (rows 1-3)
     * 
     * @param Worksheet $sheet
     */
    private function addReportHeaders(Worksheet $sheet): void
    {
        $highestCol = 'U';
        $monthName = is_numeric($this->month)
            ? Carbon::create()->month($this->month)->format('F')
            : $this->month;

        // Merge cells for headers
        $sheet->mergeCells("A1:{$highestCol}1");
        $sheet->mergeCells("A2:{$highestCol}2");
        $sheet->mergeCells("A3:{$highestCol}3");

        // Create rich text for period info with underline
        $richText = new RichText();
        $space = $richText->createText('         '); // Leading spaces
        $text = $richText->createTextRun("   Covering period: Year {$this->year}");
        $text->getFont()->setBold(true)->setSize(11)->setUnderline(Font::UNDERLINE_SINGLE);

        // Set header values
        $sheet->setCellValue('A1', "     MAINTENANCE DEPARTMENT");
        $sheet->setCellValue('A2', "       Line UTILIZATION REPORT - Line {$this->line}");
        $sheet->setCellValue('A3', $richText);

        // Apply header styling
        $sheet->getStyle("A1:A3")->applyFromArray([
            'font' => ['bold' => true]
        ]);
        $sheet->getStyle("A1")->getFont()->setSize(20);
        $sheet->getStyle("A2")->getFont()->setSize(16);
    }

    /**
     * Add Line type headers in row 5
     * 
     * @param Worksheet $sheet
     */
    private function addLineHeaders(Worksheet $sheet): void
    {
        $LineHeaders = ['Preform', 'Caps', 'OPP', 'Shrinkfilm', 'Line Effy'];
        $col = 'C';
        $row = 5;

        foreach ($LineHeaders as $header) {
            $sheet->setCellValue("{$col}{$row}", $header);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
            $col++;
        }
    }

    /**
     * Add PTD Line Efficiency header in B6
     * 
     * @param Worksheet $sheet
     */
    private function addPTDLineEfficiency(Worksheet $sheet): void
    {
        $sheet->setCellValue('B6', 'PTD Line Eff:');
        $sheet->getStyle('B6')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::YELLOW_FILL],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Fill row 28 with yellow background
     * 
     * @param Worksheet $sheet
     */
    private function addYellowFillRow(Worksheet $sheet, int $row = 28, string $endCol = 'Y'): void
    {
        $endCol = strtoupper($endCol);
        $range = "B{$row}:{$endCol}{$row}";

        $sheet->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::YELLOW_FILL],
            ],
        ]);
    }

    /**
     * Add main table headers (rows 29-30)
     * 
     * @param Worksheet $sheet
     */
private function addTableHeaders(Worksheet $sheet): void
{
    $startRow = 29;
    $lines = Line::pluck('line_number')->toArray();

    // === First Row (Yellow Header) ===
    // VOLUME REFERENCE (B:F)
    $sheet->mergeCells("B{$startRow}:G{$startRow}");
    $sheet->setCellValue("B{$startRow}", "VOLUME REFERENCE");

    // Start Line headers from H
    $colIndex = 8; // Column H
    foreach ($lines as $line) {
        $startCol = $this->colNameFromIndex($colIndex);
        $endCol   = $this->colNameFromIndex($colIndex + 3); // 4 columns per line
        $sheet->mergeCells("{$startCol}{$startRow}:{$endCol}{$startRow}");
        $sheet->setCellValue("{$startCol}{$startRow}", "Line {$line}");
        $colIndex += 4;
    }

    // Plant total group
    $startCol = $this->colNameFromIndex($colIndex);
    $endCol   = $this->colNameFromIndex($colIndex + 3);
    $sheet->mergeCells("{$startCol}{$startRow}:{$endCol}{$startRow}");
    $sheet->setCellValue("{$startCol}{$startRow}", "PLANT TOTAL");

// === Second Row (Blue Subheaders) ===
$subHeadersVolume = [
    "Production Date",
    "Total Vol., in cases",
];

// Add volume columns dynamically per available line
$lines = Line::pluck('line_number')->toArray();
foreach ($lines as $line) {
    $subHeadersVolume[] = "Vol. Line {$line}, cases";
}

// Add % contribution dynamically per available line
foreach ($lines as $line) {
    $subHeadersVolume[] = "% Vol. Contribution Line {$line}";
}

// Finally add the fixed Target LE column
$subHeadersVolume[] = "Target LE, %";


    // Start subheaders from column B
    $colIndex = 2; // Column B
    foreach ($subHeadersVolume as $header) {
        $col = $this->colNameFromIndex($colIndex);
        $sheet->setCellValue("{$col}" . ($startRow + 1), $header);
        $colIndex++;
    }

    // Line subheaders
    $lineSubHeaders = [ "LE, %", "OPL, %", "EPL, %"];
    foreach ($lines as $line) {
        foreach ($lineSubHeaders as $header) {
            $col = $this->colNameFromIndex($colIndex);
            $sheet->setCellValue("{$col}" . ($startRow + 1), $header);
            $colIndex++;
        }
    }

    // Plant total subheaders
    $plantHeaders = ["LE, %", "OPL, %", "EPL, %", "Top DT"];
    foreach ($plantHeaders as $header) {
        $col = $this->colNameFromIndex($colIndex);
        $sheet->setCellValue("{$col}" . ($startRow + 1), $header);
        $colIndex++;
    }

    // === Style Both Rows ===
    $highestCol = $this->colNameFromIndex($colIndex - 1);
    $sheet->getStyle("B{$startRow}:{$highestCol}" . ($startRow + 1))->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    // Yellow fill for row 1
    $sheet->getStyle("B{$startRow}:{$highestCol}{$startRow}")
        ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');

    // Blue fill for row 2
    $sheet->getStyle("B" . ($startRow + 1) . ":{$highestCol}" . ($startRow + 1))
        ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0070C0');
}

    /**
     * Populate data rows with production information
     * 
     * @param Worksheet $sheet
     */
    private function populateDataRows(Worksheet $sheet): void
    {
        $dataStartRow = 31; // Start after headers

        foreach ($this->data as $rowIndex => $row) {
            $currentRow = $dataStartRow + $rowIndex;
            $colLetter = 'A';

            foreach ($row as $cellValue) {
                $cell = "{$colLetter}{$currentRow}";
                $sheet->setCellValue($cell, $cellValue);

                // Apply cell styling
                $this->applyCellStyling($sheet, $cell, $colLetter, $cellValue);
                $colLetter++;
            }
        }

        // Format % columns as percent (J, N, R, V)
        $first = 31;
        $last  = 31 + count($this->data) - 1;
        foreach (['J','O','T','Y'] as $col) {
            $sheet->getStyle("{$col}{$first}:{$col}{$last}")
                ->getNumberFormat()->setFormatCode('0.00%');
        }

        // Add final row with gray background
        $this->addFinalRow($sheet, $dataStartRow + count($this->data));
    }

    /**
     * Apply styling to individual data cells
     * 
     * @param Worksheet $sheet
     * @param string $cell
     * @param string $colLetter
     * @param mixed $cellValue
     */
    private function applyCellStyling(Worksheet $sheet, string $cell, string $colLetter, $cellValue): void
    {
        // Base styling for all cells
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'] // White borders
                ]
            ],
            'font' => ['bold' => false]
        ];

        // Special styling for first column (row numbers)
        if ($colLetter === 'A') {
            $styleArray['font'] = [
                'color' => ['rgb' => 'FF0000'], // Red text
                'bold' => true
            ];
        }

        // Apply background colors based on column
        $this->applyColumnBackgroundColors($styleArray, $colLetter);
        
        // Apply conditional formatting for percentage values
        $this->applyPercentageFormatting($styleArray, $cellValue);

        $sheet->getStyle($cell)->applyFromArray($styleArray);
    }

    /**
     * Apply background colors based on column letter
     * 
     * @param array &$styleArray
     * @param string $colLetter
     */
    private function applyColumnBackgroundColors(array &$styleArray, string $colLetter): void
    {
        $fillColumnsDBE5F1 = ['B', 'C'];
        $fillColumnsF2F2F2 = ['D','E','F', 'G','H','I', 'J','K','L', 'M', 'N', 'O','P','Q','R', 'S', 'T','U','V','W','X','Y'];

        if (in_array($colLetter, $fillColumnsDBE5F1)) {
            $styleArray['fill'] = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::LIGHT_FILL_DBE5F1]
            ];
        } elseif (in_array($colLetter, $fillColumnsF2F2F2)) {
            $styleArray['fill'] = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::LIGHT_FILL_F2F2F2]
            ];
        }
    }

    /**
     * Apply conditional formatting for percentage values
     * 
     * @param array &$styleArray
     * @param mixed $cellValue
     */
    private function applyPercentageFormatting(array &$styleArray, $cellValue, string $colLetter = null): void
    {
        // Only apply percentage-based formatting to columns J, O, T, Y
        if (!in_array($colLetter, ['J','O','T','Y'])) {
            return;
        }

        if (is_numeric($cellValue)) {
            if ($cellValue < 0.0099) {
                $styleArray['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '92D050'], // ✅ Green
                ];
            } elseif ($cellValue >= 0.01) {
                $styleArray['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000'], // 🔴 Red
                ];
            }
        }
    }



    /**
     * Add final row with gray background
     * 
     * @param Worksheet $sheet
     * @param int $lastRow
     */
    private function addFinalRow(Worksheet $sheet, int $lastRow): void
    {
        $highestCol = $sheet->getHighestColumn();
        
        foreach (range('B', $highestCol) as $colLetter) {
            $cell = "{$colLetter}{$lastRow}";
            $sheet->setCellValue($cell, "");
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'] // Gray background
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF']
                    ]
                ]
            ]);
        }

        // Add separator row
        $newRow = $lastRow + 1;
        $sheet->mergeCells("B{$newRow}:{$highestCol}{$newRow}");
        $sheet->getStyle("B{$newRow}:{$highestCol}{$newRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::LIGHT_FILL_F2F2F2],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
    }

    private function generatePeriodMonthRows(): array
    {
        $rows = [];
        for ($i = 1; $i <= 12; $i++) {
            $rows[] = [
                '',                     // A → row index/empty
                'P' . $i,              // B → Period
                Carbon::create()->month($i)->format('F'), // C → Month
            ];
        }
        return $rows;
    }
    /**
     * Prepare daily production data for the main table
     * 
     * This method processes individual production reports and formats
     * them for display in the Excel table.
     * 
     * @return array
     */
private function prepareData(): array
{
    $rows = [];

    // Get all active lines
    $lines = Line::pluck('line_number')->toArray();

    for ($m = 1; $m <= 12; $m++) {
        $monthName = Carbon::create()->month($m)->format('F');

        // --- Build formulas per column by summing across all lines ---
        $outputFormulaParts = [];
        $fgUsageParts = [];
        $preformsrejParts = [];
        $preformsqaParts = [];
        $preformsrejperParts = [];

        $capsrejParts = [];
        $capsqaParts = [];
        $capsrejperParts = [];

        $opprejParts = [];
        $oppqaParts = [];
        $opprejperParts = [];

        $ldperejParts = [];
        $ldpeqaParts = [];
        $ldperejperParts = [];

        foreach ($lines as $line) {
            $outputFormulaParts[]   = "INDEX('L{$line} P{$m}'!F1:F200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $fgUsageParts[]         = "INDEX('L{$line} P{$m}'!H1:H200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";

            $preformsrejParts[]     = "INDEX('L{$line} P{$m}'!I1:I200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $preformsqaParts[]      = "INDEX('L{$line} P{$m}'!J1:J200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $preformsrejperParts[]  = "VALUE(INDEX('L{$line} P{$m}'!K1:K200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0)))";

            $capsrejParts[]         = "INDEX('L{$line} P{$m}'!N1:N200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $capsqaParts[]          = "INDEX('L{$line} P{$m}'!O1:O200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $capsrejperParts[]      = "VALUE(INDEX('L{$line} P{$m}'!P1:P200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0)))";

            $opprejParts[]          = "INDEX('L{$line} P{$m}'!S1:S200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $oppqaParts[]           = "INDEX('L{$line} P{$m}'!T1:T200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $opprejperParts[]       = "VALUE(INDEX('L{$line} P{$m}'!U1:U200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0)))";

            $ldperejParts[]         = "INDEX('L{$line} P{$m}'!X1:X200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $ldpeqaParts[]          = "INDEX('L{$line} P{$m}'!Y1:Y200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $ldperejperParts[]      = "VALUE(INDEX('L{$line} P{$m}'!Z1:Z200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0)))";
        }

        // Wrap with SUM() for each category
        $outputFormula   = "=SUM(" . implode(",", $outputFormulaParts) . ")";
        $fgUsage         = "=SUM(" . implode(",", $fgUsageParts) . ")";
        $preformsrej     = "=SUM(" . implode(",", $preformsrejParts) . ")";
        $preformsqa      = "=SUM(" . implode(",", $preformsqaParts) . ")";
        // Percentages → average instead of sum (if that's the logic you need)
        $preformsrejper  = "=AVERAGE(" . implode(",", $preformsrejperParts) . ")";

        $capsrej         = "=SUM(" . implode(",", $capsrejParts) . ")";
        $capsqa          = "=SUM(" . implode(",", $capsqaParts) . ")";
        $capsrejper      = "=AVERAGE(" . implode(",", $capsrejperParts) . ")";

        $opprej          = "=SUM(" . implode(",", $opprejParts) . ")";
        $oppqa           = "=SUM(" . implode(",", $oppqaParts) . ")";
        $opprejper       = "=AVERAGE(" . implode(",", $opprejperParts) . ")";

        $ldperej         = "=SUM(" . implode(",", $ldperejParts) . ")";
        $ldpeqa          = "=SUM(" . implode(",", $ldpeqaParts) . ")";
        $ldperejper      = "=AVERAGE(" . implode(",", $ldperejperParts) . ")";

        $rows[] = [
            "","P{$m}", $monthName, "80.00%", $outputFormula,
            'Preforms', $fgUsage, $preformsrej, $preformsqa, $preformsrejper, 
            'Caps', $fgUsage, $capsrej , $capsqa, $capsrejper, 
            'OPP Labels', $fgUsage, $opprej, $oppqa, $opprejper, 
            'LDPE Shrinkfilm', $outputFormula, $ldperej, $ldpeqa, $ldperejper, 
        ];
    }

    return $rows;
}

    /**
     * Format a single production report into a table row
     * 
     * @param ProductionReport $report
     * @param int $rowNumber
     * @return array
     */
        public function charts(): array
    {
        $sheetTitle   = $this->title();
        $dataStartRow = 31;
        $dataEndRow   = $dataStartRow + count($this->data) - 1;
        $pointCount   = max(0, $dataEndRow - $dataStartRow + 1);

        if ($pointCount <= 0) {
            return [];
        }

        $xAxisLabels = [
            new DataSeriesValues(
                'String',
                "'{$sheetTitle}'!\$A{$dataStartRow}:\$A{$dataEndRow}",
                null,
                $pointCount
            ),
        ];

    // === Axes ===
    $yAxis = new Axis();
    $yAxis->setAxisNumberProperties('0.00%');
    $yAxis->setAxisOptionsProperties('nextTo', null, null, null, null, null, 0.00, 0.0120, 0.0020);

    // Color the Y axis line itself
    $yAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // X axis (category axis)
    $xAxis = new Axis();
    $xAxis->setAxisOptionsProperties('nextTo');

    // Color the X axis line itself
    $xAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // === Major gridlines ===
    $majorGrid = new GridLines();
    $majorGrid->setLineStyleProperties(
        0.75,
        Properties::LINE_STYLE_COMPOUND_SIMPLE,
        Properties::LINE_STYLE_DASH_SOLID,
        Properties::LINE_STYLE_CAP_FLAT,
        Properties::LINE_STYLE_JOIN_BEVEL
    );
    $majorGrid->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // Apply to both axes (so grid shows in F2F2F2)
    $yAxis->setMajorGridlines($majorGrid);
    $xAxis->setMajorGridlines($majorGrid);

    // === Minor gridlines ===
    $minorGrid = new GridLines();
    $minorGrid->setLineStyleProperties(
        0.75,
        Properties::LINE_STYLE_COMPOUND_SIMPLE,
        Properties::LINE_STYLE_DASH_SOLID,
        Properties::LINE_STYLE_CAP_FLAT,
        Properties::LINE_STYLE_JOIN_BEVEL
    );
    $minorGrid->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
    $yAxis->setMinorGridlines($minorGrid);
    $xAxis->setMinorGridlines($minorGrid);

        // Target line
        $targetArray = array_fill(0, $pointCount, 0.01);
        $targetSeries = new DataSeriesValues(
            'Number',
            null,
            null,
            $pointCount,
            $targetArray
        );

        // Data series
        $preforms = new DataSeriesValues('Number', "'{$sheetTitle}'!\$J{$dataStartRow}:\$J{$dataEndRow}", null, $pointCount);
        $caps     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$O{$dataStartRow}:\$O{$dataEndRow}", null, $pointCount);
        $labels   = new DataSeriesValues('Number', "'{$sheetTitle}'!\$T{$dataStartRow}:\$T{$dataEndRow}", null, $pointCount);
        $ldpe     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$Y{$dataStartRow}:\$Y{$dataEndRow}", null, $pointCount);

        // Series labels
        $seriesLabels = [
            new DataSeriesValues('String', null, null, 1, ['Target Line Efficiency, %']),
            new DataSeriesValues('String', null, null, 1, ['PREFORMS']),
            new DataSeriesValues('String', null, null, 1, ['CAPS']),
            new DataSeriesValues('String', null, null, 1, ['OPP LABELS']),
            new DataSeriesValues('String', null, null, 1, ['LDPE SHRINKFILM']),
        ];

        $seriesValues = [$targetSeries, $preforms, $caps, $labels, $ldpe];

        $colorMap = [
            0 => '10B981', // Green - Target
            1 => '1E3A8A', // PREFORMS
            2 => '334155', // CAPS
            3 => '0F172A', // OPP LABELS
            4 => 'B45309', // LDPE
        ];

        foreach ($seriesValues as $i => $sv) {
            if (method_exists($sv, 'setLineColorProperties')) {
                $sv->setLineColorProperties($colorMap[$i]);
            }
        }

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($seriesValues) - 1),
            $seriesLabels,
            $xAxisLabels,
            $seriesValues,
            null,
            null,
            false
        );

        // Build custom title text
    $richTitle = new RichText();
    $titleRun = $richTitle->createTextRun("P{$this->month} Mat'l Eff., % Rejects - Line {$this->line}");
    $titleRun->getFont()
        ->setBold(false)   // not bold
        ->setSize(14);     // font size 14

        $plotArea = new PlotArea(null, [$series]);
        $chart = new Chart(
            'efficiency_chart',
            new Title($richTitle),
            null,
            $plotArea,
            true,
            0
        );

    // Anchor chart to the same width as the production table (B .. last heading col)
    $leftCol   = 'B';                                   // table starts at B (A is day index)
    $lastCol   = 'P';  
    $topRow    = 8;                                     // keep your current top
    $bottomRow = 19;                                    // keep your current height

    $chart->setTopLeftPosition("{$leftCol}{$topRow}");
    $chart->setBottomRightPosition("{$lastCol}{$bottomRow}");
        $chart->setChartAxisY($yAxis);
    $chart->setChartAxisX($xAxis);

        return [$chart];
    }
    private function applyRejectsConditionalFormatting(Worksheet $sheet, int $startRow, int $endRow, array $cols = ['J','O','T','Y']): void
    {
        foreach ($cols as $col) {
            $range = "{$col}{$startRow}:{$col}{$endRow}";
            $conditionalStyles = $sheet->getStyle($range)->getConditionalStyles();

            // RED fill when value >= 1.00% (i.e., >= 0.01 as a decimal)
            $gteOne = new Conditional();
            $gteOne->setConditionType(Conditional::CONDITION_CELLIS);
            $gteOne->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
            $gteOne->addCondition('0.01'); // 1.00%
            $gteOne->getStyle()->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FF0000');

            // GREEN fill when value < 1.00% (covers 0.00% to 0.99%)
            $ltOne = new Conditional();
            $ltOne->setConditionType(Conditional::CONDITION_CELLIS);
            $ltOne->setOperatorType(Conditional::OPERATOR_LESSTHAN);
            $ltOne->addCondition('0.01'); // < 1.00%
            $ltOne->getStyle()->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('92D050');

            $conditionalStyles[] = $gteOne;
            $conditionalStyles[] = $ltOne;
            $sheet->getStyle($range)->setConditionalStyles($conditionalStyles);

            // Make sure these cells are formatted as percents with 2 decimals
            $sheet->getStyle($range)->getNumberFormat()->setFormatCode('0.00%');
        }
    }
    private function addChartFooterTable(
        Worksheet $sheet,
        int $dataStartRow,
        int $dataEndRow,
        string $leftCol,     // 'B'
        string $rightCol,    // e.g., 'Z'
        int $startRow        // e.g., bottom of chart + 1
    ): void {
        $labelCol   = $leftCol; // B
        $secondCol  = $this->colNameFromIndex($this->colIndexFromName($labelCol) + 1); // C
        $firstDataColIndex = $this->colIndexFromName($secondCol) + 1; // Start placing data from column D
        $sheetName = $sheet->getTitle();

        // Data columns to pull for each row (and display horizontally)
        $indicators = [
            ['Target MAT Efficiency, %',  'D', 'percent',  self::TARGET_COLOR],
            ['Production (Output, Cs)',   'E', 'number',   self::PRODUCTION_COLOR],
            ['PREFORMS',                  'J', 'percent',  self::PREFORM_COLOR],
            ['CAPS',                      'O', 'percent',  self::CAPS_COLOR],
            ['OPP LABELS',                'T', 'percent',  self::OPP_LABELS_COLOR],
            ['LDPE Shrinkfilm',          'Y', 'percent',  self::LDPE_COLOR],
        ];

        // ===== Header row =====
        $sheet->mergeCells("{$labelCol}{$startRow}:{$secondCol}{$startRow}");
        $sheet->setCellValue("{$labelCol}{$startRow}", 'Indicator');
        $sheet->getStyle("{$labelCol}{$startRow}:{$secondCol}{$startRow}")
        ->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '23527C'], // dark blue (optional)
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'], // gray background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);


        $dataColIndex = $firstDataColIndex;
        for ($row = $dataStartRow; $row <= $dataEndRow; $row++, $dataColIndex++) {
            $colLetter = $this->colNameFromIndex($dataColIndex);
    $sheet->setCellValue("{$colLetter}{$startRow}", "='${sheetName}'!A{$row}");

    $sheet->getStyle("{$colLetter}{$startRow}")
        ->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '23527c'], // Yellow
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'], // Light gray background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        }

        // ===== Fill Data =====
        $r = $startRow + 1;
        foreach ($indicators as [$label, $colLetterInSheet, $fmt, $rgb]) {
            // Label
            $sheet->mergeCells("{$labelCol}{$r}:{$secondCol}{$r}");
            $sheet->setCellValue("{$labelCol}{$r}", $label);

            // Fill horizontal values
            $dataColIndex = $firstDataColIndex;
            for ($row = $dataStartRow; $row <= $dataEndRow; $row++, $dataColIndex++) {
                $targetCol = $this->colNameFromIndex($dataColIndex);
                $formula = "='{$sheetName}'!{$colLetterInSheet}{$row}";
                $sheet->setCellValue("{$targetCol}{$r}", $formula);

                // Format
                if ($fmt === 'percent') {
                    $sheet->getStyle("{$targetCol}{$r}")->getNumberFormat()->setFormatCode('0.00%');
                } else {
                    $sheet->getStyle("{$targetCol}{$r}")->getNumberFormat()->setFormatCode('#,##0');
                }
            }

            // Style row
            $endDataCol = $this->colNameFromIndex($dataColIndex - 1);
            $sheet->getStyle("{$labelCol}{$r}:{$endDataCol}{$r}")
                ->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                    'font' => ['size' => 10],
                ]);

            // Labels left-aligned + color
            $sheet->getStyle("{$labelCol}{$r}:{$secondCol}{$r}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            if ($rgb) {
                $sheet->getStyle("{$labelCol}{$r}:{$secondCol}{$r}")
                    ->getFont()->getColor()->setRGB($rgb);
            }

            $r++;
        }

        // Optional: shrink to fit values
        $lastCol = $this->colNameFromIndex($dataColIndex - 1);
        $sheet->getStyle("{$this->colNameFromIndex($firstDataColIndex)}{$startRow}:{$lastCol}".($r - 1))
            ->getAlignment()->setShrinkToFit(true);
    }
    /**
     * Auto-size all columns for better readability
     * 
     * @param Worksheet $sheet
     */
    private function autoSizeColumns(Worksheet $sheet): void
    {
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    /**
     * Set zoom level for better overview
     * 
     * @param Worksheet $sheet
     */
    private function setZoomLevel(Worksheet $sheet): void
    {
        $sheet->getParent()->getActiveSheet()->getSheetView()->setZoomScale(80);
    }
}