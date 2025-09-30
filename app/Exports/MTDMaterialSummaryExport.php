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
use App\Models\MaterialUtilizationAnalytics;



/**
 * Material Utilization Export Class
 * 
 * This class generates comprehensive Excel reports for material utilization
 * including production data, weekly summaries, and trend charts.
 */
class MTDMaterialSummaryExport implements FromArray, WithHeadings, WithEvents, WithCustomStartCell, WithStyles, WithCharts, WithTitle
{
    // Class properties
    protected $line;          // Production line identifier
    protected $month;         // Report month
    protected $monthName;     // Report month name
    protected $year;          // Report year
    protected $data;          // Prepared report data
    public $weeklyData;       // Weekly aggregated data

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
    public function __construct($line, $month, $year, $monthName)
    {
        $this->line = $line;
        $this->month = $month;
        $this->year = $year;
        $this->monthName = $monthName;
        
        // Prepare data for the report
        $this->data = $this->prepareData();
        $this->prepareWeeklyData();
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
        return 'L' . $this->line . ' P' . $this->month;
    }
    public function headings(): array
    {
        return array_merge([
            " ",
            "Production Date",
            "SKU",
            "Bottle per Case",
            "Target Mat'l Efficiency, %",
            "Production Output",
            "FG Usage (Preforms)",
            "Rejects (Preforms)",
            "QA Samples (Preforms)",
            "% Rejects (Preforms)",
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
        ]);
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
            $color = self::LINE_TAB_COLORS[$this->line] ?? 'FFFFFF'; // Default white if undefined
            $sheet->getTabColor()->setRGB($color);
                // Apply all formatting methods
                $this->addReportHeaders($sheet);
                $this->addMaterialHeaders($sheet);
                $this->addPTDLineEfficiency($sheet);
                $this->addYellowFillRow($sheet);
                $this->addTableHeaders($sheet);
                $this->populateDataRows($sheet);
                $this->addMTDSummaryTable($sheet);
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
        $text = $richText->createTextRun("Covering period: {$monthName}, {$this->year}");
        $text->getFont()->setBold(true)->setSize(11)->setUnderline(Font::UNDERLINE_SINGLE);

        // Set header values
        $sheet->setCellValue('A1', "     MAINTENANCE DEPARTMENT");
        $sheet->setCellValue('A2', "       MATERIAL UTILIZATION REPORT - Line {$this->line}");
        $sheet->setCellValue('A3', $richText);

        // Apply header styling
        $sheet->getStyle("A1:A3")->applyFromArray([
            'font' => ['bold' => true]
        ]);
        $sheet->getStyle("A1")->getFont()->setSize(20);
        $sheet->getStyle("A2")->getFont()->setSize(16);
    }

    /**
     * Add material type headers in row 5
     * 
     * @param Worksheet $sheet
     */
    private function addMaterialHeaders(Worksheet $sheet): void
    {
        $materialHeaders = ['Preform', 'Caps', 'OPP', 'Shrinkfilm'];
        $col = 'C';
        $row = 5;

        foreach ($materialHeaders as $header) {
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

        // Row 6 target cells
        $row = 6;

        // Reference the PTD row from the summary table
        // (lastDataRow + 2 + 5) = PTD row
        $ptdRow = 31 + count($this->data) + 2 + 5;

        // Column mapping for PTD % Rejects
        $categories = [
            'C' => 'K', // Preforms % Rejects (col K)
            'D' => 'P', // Caps % Rejects (col P)
            'E' => 'U', // OPP Labels % Rejects (col U)
            'F' => 'Z', // LDPE Shrinkfilm % Rejects (col Z)
        ];

        foreach ($categories as $displayCol => $ptdCol) {
            $sheet->setCellValue("{$displayCol}{$row}", "={$ptdCol}{$ptdRow}");
            $sheet->getStyle("{$displayCol}{$row}")
                ->getNumberFormat()->setFormatCode('0.00%');

            $sheet->getStyle("{$displayCol}{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D8E4BC'],
                ],
            ]);
        }

    }
    /**
     * Fill row 28 with yellow background
     * 
     * @param Worksheet $sheet
     */
    private function addYellowFillRow(Worksheet $sheet, int $row = 28, string $endCol = 'Z'): void
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
        
        // Fixed headers for initial columns
        $this->addFixedHeaders($sheet, $startRow);
        
        // Group headers for material types
        $this->addGroupHeaders($sheet, $startRow);
    }

    /**
     * Add fixed headers (A-F columns)
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     */
    private function addFixedHeaders(Worksheet $sheet, int $startRow): void
    {
        $fixedHeaders = [
            " ",
            "Production Date",
            "SKU",
            "Bottles per Case",
            "Target Mat'l Efficiency, %",
            "Production Output, Cs"
        ];

        $col = 'A';
        foreach ($fixedHeaders as $header) {
            $cell = $col . $startRow;
            $sheet->setCellValue($cell, $header);
            $sheet->mergeCells("{$col}{$startRow}:{$col}" . ($startRow + 1));

            // Apply styling based on header content
            $fillStyle = $header === " " ? [] : [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::HEADER_COLOR]
            ];

            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + 1))->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $header === " " ? '000000' : 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'fill' => $fillStyle
            ]);

            $col++;
        }
    }

    /**
     * Add group headers for material types
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     */
    private function addGroupHeaders(Worksheet $sheet, int $startRow): void
    {
        $groupHeaders = [
            "PREFORMS" => self::PREFORM_COLOR,
            "CAPS" => self::CAPS_COLOR,
            "OPP LABELS" => self::OPP_LABELS_COLOR,
            "LDPE SHRINKFILM" => self::LDPE_COLOR
        ];

        $subHeaders = ["Description", "FG Usage", "Rejects", "QA Samples", "% Rejects"];
        $col = 'G'; // Start after fixed headers

        foreach ($groupHeaders as $group => $color) {
            $startCol = $col;
            $endCol = chr(ord($col) + count($subHeaders) - 1);

            // Add main group header
            $sheet->mergeCells("{$startCol}{$startRow}:{$endCol}{$startRow}");
            $sheet->setCellValue("{$startCol}{$startRow}", $group);
            $sheet->getStyle("{$startCol}{$startRow}:{$endCol}{$startRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ]
            ]);

            // Add subheaders
            $subCol = $startCol;
            foreach ($subHeaders as $sub) {
                $sheet->setCellValue("{$subCol}" . ($startRow + 1), $sub);
                $sheet->getStyle("{$subCol}" . ($startRow + 1))->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color]
                    ]
                ]);
                $subCol++;
            }
            $col = ++$endCol;
        }
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
                // Wrap cell value with IFERROR if it's numeric/formula-like
                if (is_numeric($cellValue)) {
                    $sheet->setCellValueExplicit($cell, "=IFERROR($cellValue,\"\")", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA);
                } else {
                    $sheet->setCellValue($cell, $cellValue ?? "");
                }

                // Apply cell styling
                $this->applyCellStyling($sheet, $cell, $colLetter, $cellValue);
                $colLetter++;
            }
        }

        // Format % columns as percent (J, N, R, V)
        $first = 31;
        $last  = 31 + count($this->data) - 1;
        foreach (['K','P','U','Z'] as $col) {
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
                'bold'  => true
            ];
            $styleArray['alignment'] = [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ];
            $styleArray['borders'] = [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'd9d9d9'], // black border
                ]
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
        $fillColumnsDBE5F1 = ['B', 'C', 'F', 'I', 'J', 'M', 'N', 'O', 'S', 'T', 'X', 'Y'];
        $fillColumnsF2F2F2 = ['D', 'E', 'G', 'H', 'L', 'M', 'R', 'V', 'W'];

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
    private function applyPercentageFormatting(array &$styleArray, $cellValue): void
    {
        if (is_string($cellValue) && str_ends_with($cellValue, '%')) {
            $numericValue = floatval(str_replace('%', '', $cellValue));
            
            if (is_numeric($cellValue)) {
                if ($cellValue < 0.0099) {
                    $styleArray['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '92D050'] // Green for <1%
                    ];
                } elseif ($cellValue >= 0.01) {
                    $styleArray['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FF0000'] // Red for >=1%
                    ];
                }
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

    /**
     * Add MTD (Month-to-Date) Summary Report table
     * 
     * @param Worksheet $sheet
     */
    private function addMTDSummaryTable(Worksheet $sheet): void
    {
        $lastDataRow = 31 + count($this->data);
        $ytdStartRow = $lastDataRow + 2;
        $ytdEndRow = $ytdStartRow + 5; // W1-W5 + PTD

        // Create main title block
        $this->createMTDTitleBlock($sheet, $ytdStartRow, $ytdEndRow);
    }

    /**
     * Create MTD title block
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     * @param int $endRow
     */
private function createMTDTitleBlock(Worksheet $sheet, int $startRow, int $endRow): void
{
    $weekLabels = ['W1', 'W2', 'W3', 'W4', 'W5', 'PTD'];

    // === Title Block (only once) ===
    $sheet->mergeCells("B{$startRow}:D{$endRow}");
    $sheet->setCellValue("B{$startRow}", "MTD RM SUMMARY REPORT");

    $sheet->getStyle("B{$startRow}:D{$endRow}")->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FCD5B4'], // Light orange
        ],
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ],
    ]);

    // === Weekly Labels & Output ===
    foreach ($weekLabels as $i => $label) {
        $rowNum = $startRow + $i; // place each week row below title
        $isBold = $label === 'PTD';

        // Add week label (Column E)
        $sheet->setCellValue("E{$rowNum}", $label);

        // Add production output (Column F)
        if ($label === 'PTD') {
            // PTD is total of W1–W5
            $sheet->setCellValue("F{$rowNum}", "=IFERROR(SUM(F{$startRow}:F" . ($startRow + 4) . "),\"\")");
        } else {
            $excelRows = $this->weeklyData[$i]['excel_rows'] ?? [];
            if (count($excelRows)) {
                $minRow = min($excelRows);
                $maxRow = max($excelRows);

                if ($minRow === $maxRow) {
                    // Only one row → no need for range
                    $formula = "=IFERROR(F{$minRow},\"\")";
                } else {
                    // Range SUM
                    $formula = "=IFERROR(SUM(F{$minRow}:F{$maxRow}),\"\")";
                }
                $sheet->setCellValue("F{$rowNum}", $formula);
            } else {
                $sheet->setCellValue("F{$rowNum}", "");
            }
        }

            // === Apply styling ===
            foreach (['E', 'F'] as $col) {
                $sheet->getStyle("{$col}{$rowNum}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FCD5B4'],
                    ],
                    'font' => [
                        'bold' => $isBold,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ],
                ]);
            }

        // Material type headers with their colors
        $materialTypes = [
            'G' => ['label' => 'Preforms', 'color' => self::PREFORM_COLOR, 'data_key' => 'preform'],
            'L' => ['label' => 'Caps', 'color' => self::CAPS_COLOR, 'data_key' => 'caps'],
            'Q' => ['label' => 'OPP Labels', 'color' => self::OPP_LABELS_COLOR, 'data_key' => 'label'],
            'V' => ['label' => 'LDPE Shrinkfilm', 'color' => self::LDPE_COLOR, 'data_key' => 'ldpe'],
        ];

        foreach ($materialTypes as $startCol => $info) {
            // Add merged header
            $endRow = $startRow + 5;
            $sheet->mergeCells("{$startCol}{$startRow}:{$startCol}{$endRow}");
            $sheet->setCellValue("{$startCol}{$startRow}", $info['label']);
            
            // Apply header styling
            $sheet->getStyle("{$startCol}{$startRow}:{$startCol}{$endRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $info['color']]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
            ]);
        }

// === Handle Preform columns (FG Usage = H, Rejects = I, QA Samples = J) ===
$preformColumns = ['H', 'I', 'J'];

foreach ($preformColumns as $col) {
    if ($label === 'PTD') {
        // PTD = sum of W1–W5
        $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 4) . "),\"\")";
    } else {
        $excelRows = $this->weeklyData[$i]['excel_rows'] ?? [];
        if (count($excelRows)) {
            $minRow = min($excelRows);
            $maxRow = max($excelRows);

            if ($minRow === $maxRow) {
                $formula = "=IFERROR({$col}{$minRow},\"\")";
            } else {
                $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
            }
        } else {
            $formula = "";
        }
    }

    $sheet->setCellValue("{$col}{$rowNum}", $formula);
}

// === Handle Preform % Rejects (Column K) ===
if ($label === 'PTD') {
    // PTD = total rejects / (fg usage + rejects + qa samples) for W1–W5
    $formula = "=IFERROR(SUM(I{$startRow}:I" . ($startRow + 4) . ")/" .
               "(SUM(H{$startRow}:H" . ($startRow + 4) . ")+SUM(I{$startRow}:I" . ($startRow + 4) . ")+SUM(J{$startRow}:J" . ($startRow + 4) . ")),\"\")";
} else {
    // Weekly row → use same row's values
    $formula = "=IFERROR((I{$rowNum}/(H{$rowNum}+I{$rowNum}+J{$rowNum})),\"\")";
}

$sheet->setCellValue("K{$rowNum}", $formula);

// Format as percentage with 2 decimals
$sheet->getStyle("K{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');



// === Apply styling to Preform (Columns H–K) ===
$sheet->getStyle("H{$rowNum}:K{$rowNum}")->applyFromArray([
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => self::PREFORM_COLOR], // Preform color
    ],
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'], // White text
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'FFFFFF'],
        ],
    ],
]);

// === Handle Caps columns (FG Usage = M, Rejects = N, QA Samples = O) ===
$capsColumns = ['M', 'N', 'O'];

foreach ($capsColumns as $col) {
    if ($label === 'PTD') {
        // PTD = sum of W1–W5
        $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 4) . "),\"\")";
    } else {
        $excelRows = $this->weeklyData[$i]['excel_rows'] ?? [];
        if (count($excelRows)) {
            $minRow = min($excelRows);
            $maxRow = max($excelRows);

            if ($minRow === $maxRow) {
                $formula = "=IFERROR({$col}{$minRow},\"\")";
            } else {
                $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
            }
        } else {
            $formula = "";
        }
    }

    $sheet->setCellValue("{$col}{$rowNum}", $formula);
}

// === Handle Preform % Rejects (Column P) ===
if ($label === 'PTD') {
    // PTD = total rejects / (fg usage + rejects + qa samples) for W1–W5
    $formula = "=IFERROR(SUM(N{$startRow}:N" . ($startRow + 4) . ")/" .
               "(SUM(M{$startRow}:M" . ($startRow + 4) . ")+SUM(N{$startRow}:N" . ($startRow + 4) . ")+SUM(O{$startRow}:O" . ($startRow + 4) . ")),\"\")";
} else {
    // Weekly row → use same row's values
    $formula = "=IFERROR((N{$rowNum}/(M{$rowNum}+N{$rowNum}+O{$rowNum})),\"\")";
}

$sheet->setCellValue("P{$rowNum}", $formula);

// Format as percentage with 2 decimals
$sheet->getStyle("P{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

// === Apply styling to Caps (Columns M–P) ===
$sheet->getStyle("M{$rowNum}:P{$rowNum}")->applyFromArray([
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => self::CAPS_COLOR], // CAPS_COLOR
    ],
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'], // White text
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'FFFFFF'],
        ],
    ],
]);

// === Handle OPP Labels columns (FG Usage = R, Rejects = S, QA Samples = T) ===
$oppColumns = ['R', 'S', 'T'];

foreach ($oppColumns as $col) {
    if ($label === 'PTD') {
        // PTD = sum of W1–W5
        $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 4) . "),\"\")";
    } else {
        $excelRows = $this->weeklyData[$i]['excel_rows'] ?? [];
        if (count($excelRows)) {
            $minRow = min($excelRows);
            $maxRow = max($excelRows);

            if ($minRow === $maxRow) {
                $formula = "=IFERROR({$col}{$minRow},\"\")";
            } else {
                $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
            }
        } else {
            $formula = "";
        }
    }

    $sheet->setCellValue("{$col}{$rowNum}", $formula);
}

// === Handle OPP Labels % Rejects (Column U) ===
if ($label === 'PTD') {
    $formula = "=IFERROR(SUM(S{$startRow}:S" . ($startRow + 4) . ")/" .
               "(SUM(R{$startRow}:R" . ($startRow + 4) . ")+SUM(S{$startRow}:S" . ($startRow + 4) . ")+SUM(T{$startRow}:T" . ($startRow + 4) . ")),\"\")";
} else {
    $formula = "=IFERROR((S{$rowNum}/(R{$rowNum}+S{$rowNum}+T{$rowNum})),\"\")";
}

$sheet->setCellValue("U{$rowNum}", $formula);
$sheet->getStyle("U{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

// === Apply styling to OPP Labels (Columns R–U) ===
$sheet->getStyle("R{$rowNum}:U{$rowNum}")->applyFromArray([
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => self::OPP_LABELS_COLOR],
    ],
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'FFFFFF'],
        ],
    ],
]);

// === Handle LDPE Shrinkfilm columns (FG Usage = W, Rejects = X, QA Samples = Y) ===
$ldpeColumns = ['W', 'X', 'Y'];

foreach ($ldpeColumns as $col) {
    if ($label === 'PTD') {
        $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 4) . "),\"\")";
    } else {
        $excelRows = $this->weeklyData[$i]['excel_rows'] ?? [];
        if (count($excelRows)) {
            $minRow = min($excelRows);
            $maxRow = max($excelRows);

            if ($minRow === $maxRow) {
                $formula = "=IFERROR({$col}{$minRow},\"\")";
            } else {
                $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
            }
        } else {
            $formula = "";
        }
    }

    $sheet->setCellValue("{$col}{$rowNum}", $formula);
}

// === Handle LDPE % Rejects (Column Z) ===
if ($label === 'PTD') {
    $formula = "=IFERROR(SUM(X{$startRow}:X" . ($startRow + 4) . ")/" .
               "(SUM(W{$startRow}:W" . ($startRow + 4) . ")+SUM(X{$startRow}:X" . ($startRow + 4) . ")+SUM(Y{$startRow}:Y" . ($startRow + 4) . ")),\"\")";
} else {
    $formula = "=IFERROR((X{$rowNum}/(W{$rowNum}+X{$rowNum}+Y{$rowNum})),\"\")";
}

$sheet->setCellValue("Z{$rowNum}", $formula);
$sheet->getStyle("Z{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

// === Apply styling to LDPE Shrinkfilm (Columns W–Z) ===
$sheet->getStyle("W{$rowNum}:Z{$rowNum}")->applyFromArray([
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => self::LDPE_COLOR],
    ],
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'FFFFFF'],
        ],
    ],
]);


        
    }
}
/**
 * Prepare weekly aggregated data from production reports
 * 
 * Groups production reports by week and tracks:
 *  - total weekly production output
 *  - the Excel row numbers where each week's data is located
 */
private function prepareWeeklyData(): void
{
    $rows = MaterialUtilizationAnalytics::query()
        ->whereMonth('production_date', $this->month)
        ->whereYear('production_date', $this->year)
        ->when($this->line, fn($q) => $q->where('line', $this->line))
        ->orderBy('production_date')
        ->get();

    $rawWeeklyData = [];

    foreach ($rows as $index => $row) {
        $excelRow   = ($index + 1) + 30;
        $dayOfMonth = \Carbon\Carbon::parse($row->production_date)->day;
        $weekNumber = intdiv(($dayOfMonth - 1), 7) + 1;
        $weekKey    = "Week {$weekNumber}";

        if (!isset($rawWeeklyData[$weekKey])) {
            $rawWeeklyData[$weekKey] = [
                'output' => 0,
                'excel_rows' => [],
            ];
        }

        $rawWeeklyData[$weekKey]['output'] += (int) $row->total_output;
        $rawWeeklyData[$weekKey]['excel_rows'][] = $excelRow;
    }

    $weeklyData = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = "Week $i";
        $weeklyData[$i - 1] = [
            'week' => $key,
            'output' => $rawWeeklyData[$key]['output'] ?? 0,
            'excel_rows' => $rawWeeklyData[$key]['excel_rows'] ?? [],
        ];
    }

    $this->weeklyData = $weeklyData;
}




/**
 * Add production trend chart
 *
 * @param Worksheet $sheet
 * @return array<Chart>
 */
public function charts(): array
{
    $sheetTitle   = $this->title();
    $dataStartRow = 31; // first data row in the sheet
    $dataEndRow   = $dataStartRow + count($this->data) - 1;
    $pointCount   = max(0, $dataEndRow - $dataStartRow + 1);

    if ($pointCount <= 0) {
        return []; // no data -> no chart
    }

    // === X-axis labels ===
    $xAxisLabels = [
        new DataSeriesValues(
            'String',
            "'{$sheetTitle}'!\$A{$dataStartRow}:\$A{$dataEndRow}",
            null,
            $pointCount
        ),
    ];

    // === Y-axis (percentage values) ===
    $yAxis = new Axis();
    $yAxis->setAxisNumberProperties('0.00%');
    $yAxis->setAxisOptionsProperties('nextTo', null, null, null, null, null, 0.00, 0.0120, 0.0020);
    $yAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // === X-axis ===
    $xAxis = new Axis();
    $xAxis->setAxisOptionsProperties('nextTo');
    $xAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // === Gridlines ===
    $majorGrid = new GridLines();
    $majorGrid->setLineStyleProperties(
        0.75,
        Properties::LINE_STYLE_COMPOUND_SIMPLE,
        Properties::LINE_STYLE_DASH_SOLID,
        Properties::LINE_STYLE_CAP_FLAT,
        Properties::LINE_STYLE_JOIN_BEVEL
    );
    $majorGrid->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    $minorGrid = new GridLines();
    $minorGrid->setLineStyleProperties(
        0.75,
        Properties::LINE_STYLE_COMPOUND_SIMPLE,
        Properties::LINE_STYLE_DASH_SOLID,
        Properties::LINE_STYLE_CAP_FLAT,
        Properties::LINE_STYLE_JOIN_BEVEL
    );
    $minorGrid->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    $yAxis->setMajorGridlines($majorGrid);
    $xAxis->setMajorGridlines($majorGrid);
    $yAxis->setMinorGridlines($minorGrid);
    $xAxis->setMinorGridlines($minorGrid);

    // === Data series (materials + target) ===
    $preforms = new DataSeriesValues('Number', "'{$sheetTitle}'!\$K{$dataStartRow}:\$K{$dataEndRow}", null, $pointCount);
    $caps     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$P{$dataStartRow}:\$P{$dataEndRow}", null, $pointCount);
    $labels   = new DataSeriesValues('Number', "'{$sheetTitle}'!\$U{$dataStartRow}:\$U{$dataEndRow}", null, $pointCount);
    $ldpe     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$Z{$dataStartRow}:\$Z{$dataEndRow}", null, $pointCount);

    // === Colors per material ===
    $colorMap = [
        'preforms' => '7F7F7F', 
        'caps'     => '254061', 
        'labels'   => '77933C', 
        'ldpe'     => '984807', 
    ];

    // Apply circle markers + unique colors
    $preforms->setPointMarker('circle');
    $preforms->setPointSize(5);
    $preforms->setFillColor(new ChartColor($colorMap['preforms'], null, ChartColor::EXCEL_COLOR_TYPE_RGB));

    $caps->setPointMarker('circle');
    $caps->setPointSize(5);
    $caps->setFillColor(new ChartColor($colorMap['caps'], null, ChartColor::EXCEL_COLOR_TYPE_RGB));

    $labels->setPointMarker('circle');
    $labels->setPointSize(5);
    $labels->setFillColor(new ChartColor($colorMap['labels'], null, ChartColor::EXCEL_COLOR_TYPE_RGB));

    $ldpe->setPointMarker('circle');
    $ldpe->setPointSize(5);
    $ldpe->setFillColor(new ChartColor($colorMap['ldpe'], null, ChartColor::EXCEL_COLOR_TYPE_RGB));

    // === Target line (plain, no markers) ===
    $targetArray  = array_fill(0, $pointCount, 0.01);
    $targetSeries = new DataSeriesValues(
        'Number',
        null,
        null,
        $pointCount,
        $targetArray
    );
    $targetSeries->setLineColorProperties('00B050'); // green line
    $targetSeries->setPointMarker('circle');         // add markers
    $targetSeries->setPointSize(5);                  // marker size

    // === Series labels ===
    $seriesLabels = [
        new DataSeriesValues('String', null, null, 1, ['Target Material Efficiency, %']),
        new DataSeriesValues('String', null, null, 1, ['PREFORMS']),
        new DataSeriesValues('String', null, null, 1, ['CAPS']),
        new DataSeriesValues('String', null, null, 1, ['OPP LABELS']),
        new DataSeriesValues('String', null, null, 1, ['LDPE SHRINKFILM']),
    ];

    // === Combine series ===
    $seriesValues = [$targetSeries, $preforms, $caps, $labels, $ldpe];

    $series = new DataSeries(
        DataSeries::TYPE_LINECHART,
        DataSeries::GROUPING_STANDARD,
        range(0, count($seriesValues) - 1),
        $seriesLabels,
        $xAxisLabels,
        $seriesValues
    );
    $series->setPlotStyle(DataSeries::STYLE_MARKER);

    // === Chart title ===
    $richTitle = new RichText();
    $titleRun  = $richTitle->createTextRun("P{$this->month} Mat'l Eff., % Rejects - Line {$this->line}");
    $titleRun->getFont()->setBold(false)->setSize(14);

    // === Build chart ===
    $plotArea = new PlotArea(null, [$series]);
    $chart    = new Chart(
        'material_chart',
        new Title($richTitle),
        new Legend(Legend::POSITION_RIGHT, null, false),
        $plotArea,
        true,
        0
    );

    $chart->setTopLeftPosition("B8");
    $chart->setBottomRightPosition("P19");
    $chart->setChartAxisY($yAxis);
    $chart->setChartAxisX($xAxis);

    return [$chart];
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

        // Fetch from analytics table instead of recalculating from ProductionReport
        $analyticsRows = MaterialUtilizationAnalytics::query()
            ->whereMonth('production_date', $this->month)
            ->whereYear('production_date', $this->year)
            ->when($this->line, fn($q) => $q->where('line', $this->line))
            ->orderBy('production_date')
            ->get();

        foreach ($analyticsRows as $index => $row) {
            $rows[] = $this->formatAnalyticsRow($row, $index + 1);
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
    private function formatAnalyticsRow($row, int $rowNumber): array
    {
        $excelRow = $rowNumber + 30; // because data starts at row 31
        $productionDate  = \Carbon\Carbon::parse($row->production_date)->format('F j, Y');
        $sku             = $row->sku ?? 'No Run';
        $bottlesPerCase  = $row->bottlePerCase ?? 0;
        $efficiency      = number_format(($row->targetMaterialEfficiency ?? 0.01) * 100, 2) . '%';
        $outputCases     = $row->total_output ?? 0;

        return [
            $rowNumber,
            $productionDate,
            $sku,
            $bottlesPerCase,
            $efficiency,
            $outputCases,

            // PREFORMS
            $row->preformDesc,
            "=IFERROR(D{$excelRow}*F{$excelRow},\"\")", // FG Usage formula
            $row->preform_rej,
            $row->preform_qa,
            "=IFERROR(I{$excelRow}/(H{$excelRow}+I{$excelRow}+J{$excelRow}),\"\")",

            // CAPS
            $row->capsDesc,
            "=IFERROR(D{$excelRow}*F{$excelRow},\"\")", // FG Usage formula
            $row->caps_rej,
            $row->caps_qa,
            "=IFERROR(N{$excelRow}/(M{$excelRow}+N{$excelRow}+O{$excelRow}),\"\")",

            // OPP LABELS
            $row->labelDesc,
            "=IFERROR(D{$excelRow}*F{$excelRow},\"\")", // FG Usage formula
            $row->label_rej,
            $row->label_qa,
            "=IFERROR(S{$excelRow}/(R{$excelRow}+S{$excelRow}+T{$excelRow}),\"\")",

            // LDPE
            $row->ldpeDesc,
            "=IFERROR(F{$excelRow},\"\")",
            $row->ldpe_rej,
            $row->ldpe_qa,
            "=IFERROR(X{$excelRow}/(W{$excelRow}+X{$excelRow}+Y{$excelRow}),\"\")",
        ];
    }


    private function applyRejectsConditionalFormatting(Worksheet $sheet, int $startRow, int $endRow, array $cols = ['K','P','U','Z']): void
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

    // Chart colors (line colors)
    $lineColors = [
        'Target MAT Efficiency, %' => self::TARGET_COLOR,
        'Production (Output, Cs)' =>  '376faa',
        'PREFORMS'                 => self::PREFORM_COLOR,
        'CAPS'                     => self::CAPS_COLOR,
        'OPP LABELS'               => self::OPP_LABELS_COLOR,
        'LDPE Shrinkfilm'          => self::LDPE_COLOR,
    ];

    // Marker fill colors (circle colors)
    $markerColors = [
        'Target MAT Efficiency, %' => '4A7EBB',
        'Production (Output, Cs)',   '376faa',
        'PREFORMS'                 => 'BE4B48',
        'CAPS'                     => '98B954',
        'OPP LABELS'               => '7D60A0',
        'LDPE Shrinkfilm'          => '46AAC5',
    ];

    // Data columns
    $indicators = [
        ['Target MAT Efficiency, %',  'E', 'percent'],
        ['Production (Output, Cs)',   'F', 'number'],
        ['PREFORMS',                  'K', 'percent'],
        ['CAPS',                      'P', 'percent'],
        ['OPP LABELS',                'U', 'percent'],
        ['LDPE Shrinkfilm',           'Z', 'percent'],
    ];

    // ===== Header row =====
    $sheet->mergeCells("{$labelCol}{$startRow}:{$secondCol}{$startRow}");
    $sheet->setCellValue("{$labelCol}{$startRow}", 'Indicator');
    $sheet->getStyle("{$labelCol}{$startRow}:{$secondCol}{$startRow}")
        ->applyFromArray([
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => '23527C']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
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

    // Dates row
    $dataColIndex = $firstDataColIndex;
    for ($row = $dataStartRow; $row <= $dataEndRow; $row++, $dataColIndex++) {
        $colLetter = $this->colNameFromIndex($dataColIndex);
        $sheet->setCellValue("{$colLetter}{$startRow}", "='{$sheetName}'!A{$row}");
        $sheet->getStyle("{$colLetter}{$startRow}")
            ->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '23527C']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
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
    foreach ($indicators as [$label, $colLetterInSheet, $fmt]) {
        $lineColor   = $lineColors[$label]   ?? '000000';
        $markerColor = $markerColors[$label] ?? $lineColor;

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

        // Line before marker (line color)
        $runLine1 = $richText->createTextRun("━");
        $runLine1->getFont()->getColor()->setRGB($lineColor);
        $runLine1->getFont()->setBold(true);

        // Marker (circle, marker color)
        $runMarker = $richText->createTextRun("●");
        $runMarker->getFont()->getColor()->setRGB($markerColor);
        $runMarker->getFont()->setBold(true);

        // Line after marker (line color)
        $runLine2 = $richText->createTextRun("━ ");
        $runLine2->getFont()->getColor()->setRGB($lineColor);
        $runLine2->getFont()->setBold(true);

        // Label text
        $runLabel = $richText->createTextRun($label);
        $runLabel->getFont()->getColor()->setRGB('000000');

        // Apply to merged cell
        $sheet->mergeCells("{$labelCol}{$r}:{$secondCol}{$r}");
        $sheet->setCellValue("{$labelCol}{$r}", $richText);

        // === Horizontal data values ===
        $dataColIndex = $firstDataColIndex;
        for ($row = $dataStartRow; $row <= $dataEndRow; $row++, $dataColIndex++) {
            $targetCol = $this->colNameFromIndex($dataColIndex);
            $formula   = "='{$sheetName}'!{$colLetterInSheet}{$row}";
            $sheet->setCellValue("{$targetCol}{$r}", $formula);

            if ($fmt === 'percent') {
                $sheet->getStyle("{$targetCol}{$r}")
                    ->getNumberFormat()->setFormatCode('0.00%');
            } else {
                $sheet->getStyle("{$targetCol}{$r}")
                    ->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        // Style the row
        $endDataCol = $this->colNameFromIndex($dataColIndex - 1);
        $sheet->getStyle("{$labelCol}{$r}:{$endDataCol}{$r}")
            ->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'font' => ['size' => 10],
            ]);

        $sheet->getStyle("{$labelCol}{$r}:{$secondCol}{$r}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $r++;
    }
}
/**
 * Auto-size only the main data table columns.
 * Footer table columns stay fixed width to avoid compression.
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
        $sheet->getParent()->getActiveSheet()->getSheetView()->setZoomScale(70);
    }



}