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
 * Material Utilization Export Class
 * 
 * This class generates comprehensive Excel reports for material utilization
 * including production data, quarterly summaries, and trend charts.
 */
class MaterialOverallSummaryWorksheet implements FromArray, WithHeadings, WithEvents, WithCustomStartCell, WithStyles, WithCharts, WithTitle
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
        
        // Assign the prepared quarterly template
        $this->quarterlyData = $this->preparequarterlyData();

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
            "Month",
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
                
                $this->quarterlyData = $this->preparequarterlyData();
                // Apply all formatting methods
                $this->addReportHeaders($sheet);
                $this->addMaterialHeaders($sheet);
                $this->addPTDLineEfficiency($sheet);
                $this->addYellowFillRow($sheet);
                $this->addTableHeaders($sheet);
                $this->populateDataRows($sheet);
                $this->addMTDSummaryTable($sheet);
                $this->addPTDCalculations($sheet);
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
        $materialHeaders = ['Preform', 'Caps', 'OPP', 'Shrinkfilm', 'Material Effy'];
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
            "Period",
            "Month",
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
        $col = 'F'; // Start after fixed headers

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

    /**
     * Add MTD (Month-to-Date) Summary Report table
     * 
     * @param Worksheet $sheet
     */
    private function addMTDSummaryTable(Worksheet $sheet): void
    {
        $lastDataRow = 31 + count($this->data);
        $PTDStartRow = $lastDataRow + 2;
        $PTDEndRow = $PTDStartRow + 5; // Q1-Q5 + PTD

        // Create main title block
        $this->createMTDTitleBlock($sheet, $PTDStartRow, $PTDEndRow);
        
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
        $quarterLabels = ['Q1','Q2','Q3','Q4','', 'PTD'];

        $sheet->mergeCells("B{$startRow}:C{$endRow}");
        $sheet->setCellValue("B{$startRow}", "MTD RM SUMMARY REPORT");

        // Style block...
        $sheet->getStyle("B{$startRow}:C{$endRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCD5B4'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // === Define quarter to month mapping ===
        $quarterMonths = [
            'Q1' => [1,2,3],
            'Q2' => [4,5,6],
            'Q3' => [7,8,9],
            'Q4' => [10,11,12],
        ];

        foreach ($quarterLabels as $i => $label) {
            $rowNum = $startRow + $i;
            $isBold = $label === 'PTD';

            // Column D → Quarter labels
            $sheet->setCellValue("D{$rowNum}", $label);

        if ($label === 'PTD') {
            // PTD = total of all quarters
            $sheet->setCellValue("E{$rowNum}", "=IFERROR(SUM(E{$startRow}:E" . ($startRow + 3) . "),\"\")");
        } elseif (isset($quarterMonths[$label])) {
            // Build SUM range of rows for this quarter
            $minRow = 31 + ($quarterMonths[$label][0] - 1);
            $maxRow = 31 + (end($quarterMonths[$label]) - 1);

            $formula = "=IFERROR(SUM(E{$minRow}:E{$maxRow}),\"\")";
            $sheet->setCellValue("E{$rowNum}", $formula);
        } else {
            // Skip empty row
            $sheet->setCellValue("E{$rowNum}", "");
        }

            // === Apply styling ===
            foreach (['D', 'E'] as $col) {
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
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
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
                'F' => ['label' => 'Preforms', 'color' => self::PREFORM_COLOR, 'data_key' => 'preform'],
                'K' => ['label' => 'Caps', 'color' => self::CAPS_COLOR, 'data_key' => 'caps'],
                'P' => ['label' => 'OPP Labels', 'color' => self::OPP_LABELS_COLOR, 'data_key' => 'label'],
                'U' => ['label' => 'LDPE Shrinkfilm', 'color' => self::LDPE_COLOR, 'data_key' => 'ldpe'],
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

    // === Handle Preform columns (FG Usage = G, Rejects = H, QA Samples = I) ===
    $preformColumns = ['G', 'H', 'I'];

    foreach ($preformColumns as $col) {
        if ($label === 'PTD') {
            // PTD = sum of all quarter rows (Q1–Q4)
            $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 3) . "),\"\")";
        } elseif (isset($quarterMonths[$label])) {
            // Quarter = sum of 3 months that belong to this quarter
            $minRow = 31 + ($quarterMonths[$label][0] - 1);
            $maxRow = 31 + (end($quarterMonths[$label]) - 1);

            $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
        } else {
            $formula = "";
        }

        $sheet->setCellValue("{$col}{$rowNum}", $formula);
    }

    // === Handle Preform % Rejects (Column J) ===
    if ($label === 'PTD') {
        // PTD = total rejects / (fg usage + rejects + qa samples) across all quarters
        $formula = "=IFERROR(SUM(H{$startRow}:H" . ($startRow + 3) . ")/" .
                "(SUM(G{$startRow}:G" . ($startRow + 3) . ")+SUM(H{$startRow}:H" . ($startRow + 3) . ")+SUM(I{$startRow}:I" . ($startRow + 3) . ")),\"\")";
    } elseif (isset($quarterMonths[$label])) {
        // Quarterly %Rejects = sum of quarter rejects / (sum of quarter FG + rejects + QA)
        $minRow = 31 + ($quarterMonths[$label][0] - 1);
        $maxRow = 31 + (end($quarterMonths[$label]) - 1);

        $formula = "=IFERROR(SUM(H{$minRow}:H{$maxRow})/(SUM(G{$minRow}:G{$maxRow})+SUM(H{$minRow}:H{$maxRow})+SUM(I{$minRow}:I{$maxRow})),\"\")";
    } else {
        // Skip empty row
        $formula = "";
    }

    $sheet->setCellValue("J{$rowNum}", $formula);

    // Format as percentage with 2 decimals
    $sheet->getStyle("J{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

    // === Apply styling to Preform (Columns G–J) ===
    $sheet->getStyle("G{$rowNum}:J{$rowNum}")->applyFromArray([
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

    // === Handle CAPS columns (FG Usage = L, Rejects = M, QA Samples = N) ===
    $capsColumns = ['L', 'M', 'N'];

    foreach ($capsColumns as $col) {
        if ($label === 'PTD') {
            // PTD = sum of all quarter rows (Q1–Q4)
            $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 3) . "),\"\")";
        } elseif (isset($quarterMonths[$label])) {
            // Quarter = sum of 3 months that belong to this quarter
            $minRow = 31 + ($quarterMonths[$label][0] - 1);
            $maxRow = 31 + (end($quarterMonths[$label]) - 1);

            $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
        } else {
            $formula = "";
        }

        $sheet->setCellValue("{$col}{$rowNum}", $formula);
    }

    // === Handle Caps % Rejects (Column O) ===
    if ($label === 'PTD') {
        // PTD = total rejects / (fg usage + rejects + qa samples) across all quarters
        $formula = "=IFERROR(SUM(M{$startRow}:M" . ($startRow + 3) . ")/" .
                "(SUM(L{$startRow}:L" . ($startRow + 3) . ")+SUM(M{$startRow}:M" . ($startRow + 3) . ")+SUM(N{$startRow}:N" . ($startRow + 3) . ")),\"\")";
    } elseif (isset($quarterMonths[$label])) {
        // Quarterly %Rejects = sum of quarter rejects / (sum of quarter FG + rejects + QA)
        $minRow = 31 + ($quarterMonths[$label][0] - 1);
        $maxRow = 31 + (end($quarterMonths[$label]) - 1);

        $formula = "=IFERROR(SUM(M{$minRow}:M{$maxRow})/(SUM(L{$minRow}:L{$maxRow})+SUM(M{$minRow}:M{$maxRow})+SUM(N{$minRow}:N{$maxRow})),\"\")";
    } else {
        $formula = "";
    }

    $sheet->setCellValue("O{$rowNum}", $formula);

    // Format as percentage with 2 decimals
    $sheet->getStyle("O{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

    // === Apply styling to Caps (Columns L–O) ===
    $sheet->getStyle("L{$rowNum}:O{$rowNum}")->applyFromArray([
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => self::CAPS_COLOR], // Caps color
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

    // === Handle OPP LABELS columns (FG Usage = Q, Rejects = R, QA Samples = S) ===
    $oppColumns = ['Q', 'R', 'S'];

    foreach ($oppColumns as $col) {
        if ($label === 'PTD') {
            // PTD = sum of all quarter rows (Q1–Q4)
            $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 3) . "),\"\")";
        } elseif (isset($quarterMonths[$label])) {
            // Quarter = sum of 3 months that belong to this quarter
            $minRow = 31 + ($quarterMonths[$label][0] - 1);
            $maxRow = 31 + (end($quarterMonths[$label]) - 1);

            $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
        } else {
            $formula = "";
        }

        $sheet->setCellValue("{$col}{$rowNum}", $formula);
    }

    // === Handle OPP Labels % Rejects (Column T) ===
    if ($label === 'PTD') {
        $formula = "=IFERROR(SUM(R{$startRow}:R" . ($startRow + 3) . ")/" .
                "(SUM(Q{$startRow}:Q" . ($startRow + 3) . ")+SUM(R{$startRow}:R" . ($startRow + 3) . ")+SUM(S{$startRow}:S" . ($startRow + 3) . ")),\"\")";
    } elseif (isset($quarterMonths[$label])) {
        $minRow = 31 + ($quarterMonths[$label][0] - 1);
        $maxRow = 31 + (end($quarterMonths[$label]) - 1);

        $formula = "=IFERROR(SUM(R{$minRow}:R{$maxRow})/(SUM(Q{$minRow}:Q{$maxRow})+SUM(R{$minRow}:R{$maxRow})+SUM(S{$minRow}:S{$maxRow})),\"\")";
    } else {
        $formula = "";
    }

    $sheet->setCellValue("T{$rowNum}", $formula);
    $sheet->getStyle("T{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

    // === Apply styling to OPP Labels (Columns Q–T) ===
    $sheet->getStyle("Q{$rowNum}:T{$rowNum}")->applyFromArray([
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => self::OPP_LABELS_COLOR], // OPP color
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
    // === Handle LDPE SHRINKFILM columns (FG Usage = V, Rejects = W, QA Samples = X) ===
    $ldpeColumns = ['V', 'W', 'X'];

    foreach ($ldpeColumns as $col) {
        if ($label === 'PTD') {
            // PTD = sum of all quarter rows (Q1–Q4)
            $formula = "=IFERROR(SUM({$col}{$startRow}:{$col}" . ($startRow + 3) . "),\"\")";
        } elseif (isset($quarterMonths[$label])) {
            // Quarter = sum of 3 months that belong to this quarter
            $minRow = 31 + ($quarterMonths[$label][0] - 1);
            $maxRow = 31 + (end($quarterMonths[$label]) - 1);

            $formula = "=IFERROR(SUM({$col}{$minRow}:{$col}{$maxRow}),\"\")";
        } else {
            $formula = "";
        }

        $sheet->setCellValue("{$col}{$rowNum}", $formula);
    }

    // === Handle LDPE % Rejects (Column Y) ===
    if ($label === 'PTD') {
        $formula = "=IFERROR(SUM(W{$startRow}:W" . ($startRow + 3) . ")/" .
                "(SUM(V{$startRow}:V" . ($startRow + 3) . ")+SUM(W{$startRow}:W" . ($startRow + 3) . ")+SUM(X{$startRow}:X" . ($startRow + 3) . ")),\"\")";
    } elseif (isset($quarterMonths[$label])) {
        $minRow = 31 + ($quarterMonths[$label][0] - 1);
        $maxRow = 31 + (end($quarterMonths[$label]) - 1);

        $formula = "=IFERROR(SUM(W{$minRow}:W{$maxRow})/(SUM(V{$minRow}:V{$maxRow})+SUM(W{$minRow}:W{$maxRow})+SUM(X{$minRow}:X{$maxRow})),\"\")";
    } else {
        $formula = "";
    }

    $sheet->setCellValue("Y{$rowNum}", $formula);
    $sheet->getStyle("Y{$rowNum}")->getNumberFormat()->setFormatCode('0.00%');

    // === Apply styling to LDPE Shrinkfilm (Columns V–Y) ===
    $sheet->getStyle("V{$rowNum}:Y{$rowNum}")->applyFromArray([
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => self::LDPE_COLOR], // LDPE color
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


        }
    }

    /**
     * Add data columns for each material type
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     * @param string $startCol
     * @param array $materialInfo
     */
    private function addMaterialDataColumns(Worksheet $sheet, int $startRow, string $startCol, array $materialInfo): void
    {
        $dataTypes = ['fg', 'rej', 'qa', 'percent'];
        $quarterLabels = ['Q1', 'Q2', 'Q3', 'Q4', '', 'PTD'];
        
        // Calculate column positions
        $cols = [];
        for ($i = 0; $i < 4; $i++) {
            $cols[$dataTypes[$i]] = chr(ord($startCol) + 1 + $i);
        }

        foreach ($dataTypes as $type) {
            $col = $cols[$type];
            
            foreach ($quarterLabels as $i => $label) {
                $rowNum = $startRow + $i;
                $isBold = $label === 'PTD';

                    if (trim($label) === '') {
                    // Don't set value, just format
                    $sheet->getStyle("{$col}{$rowNum}")->applyFromArray([
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $materialInfo['color']]],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
                    ]);
                    continue;
                }


        if ($type === 'percent') {
            // Get the PTD cell references for FG, REJ, QA
            $fgCell = $cols['fg'] . $rowNum;
            $rejCell = $cols['rej'] . $rowNum;
            $qaCell = $cols['qa'] . $rowNum;

            // Construct Excel formula for %rejects
            $value = "=IFERROR({$rejCell}/({$fgCell}+{$rejCell}+{$qaCell}), 0)";
        } else {
            $value = $this->calculatePTDValue($materialInfo['data_key'], $type);
        }

                
                $sheet->setCellValue("{$col}{$rowNum}", $value);

                // 🟢 Apply number format for 'percent' dataType
                if ($type === 'percent') {
                    $sheet->getStyle("{$col}{$rowNum}")
                        ->getNumberFormat()
                        ->setFormatCode('0.00%');
                }

                // Apply styling
                $sheet->getStyle("{$col}{$rowNum}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $materialInfo['color']]],
                    'font' => ['bold' => $isBold, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
                ]);
            }
        }
    }

    /**
     * Get quarterly value for specific material and data type
     * 
     * @param int $quarterIndex
     * @param string $materialKey
     * @param string $dataType
     * @return mixed
     */
    private function getQuarterlyValue(int $quarterIndex, string $materialKey, string $dataType)
    {
        return $this->quarterlyData[$quarterIndex][$materialKey][$dataType] ?? 
               ($dataType === 'percent' ? '0.00%' : 0);
    }

    /**
     * Calculate PTD (Period-to-Date) value
     * 
     * @param string $materialKey
     * @param string $dataType
     * @return mixed
     */
    private function calculatePTDValue(string $materialKey, string $dataType)
    {
        if ($dataType === 'percent') {
            $percentFormulas = [];

            foreach (range(0, 3) as $q) {
                $value = $this->quarterlyData[$q][$materialKey]['percent'] ?? 0;

                // Only include Excel formulas or numeric values
                if (is_string($value) && str_starts_with($value, '=')) {
                    $percentFormulas[] = substr($value, 1); // remove '='
                } elseif (is_numeric($value)) {
                    $percentFormulas[] = $value;
                }
            }

            // Return an average of all quarterly percentages
            if (count($percentFormulas) > 0) {
                return '=AVERAGE(' . implode(',', $percentFormulas) . ')';
            }

            return '0.00%';
        }

        // For fg, rej, qa → return Excel SUM formula of 4 quarters
        $cellRefs = [];

        foreach (range(0, 3) as $q) {
            $value = $this->quarterlyData[$q][$materialKey][$dataType] ?? 0;

            if (is_string($value) && str_starts_with($value, '=')) {
                $cellRefs[] = substr($value, 1);
            } elseif (is_numeric($value)) {
                $cellRefs[] = $value;
            }
        }

        return '=SUM(' . implode(',', $cellRefs) . ')';
    }




    /**
     * Add PTD calculations in row 6
     * 
     * @param Worksheet $sheet
     */
    private function addPTDCalculations(Worksheet $sheet): void
    {
        // 🧮 Determine the PTD row in the MTD Summary table
        $lastDataRow = 31 + count($this->data);
        $PTDStartRow = $lastDataRow + 2;
        $ptdRow = $PTDStartRow + 5;

        // 🔢 Reference MTD PTD percent cells directly
        $sheet->setCellValue('C6', "=J{$ptdRow}"); // Preforms
        $sheet->setCellValue('D6', "=O{$ptdRow}"); // Caps
        $sheet->setCellValue('E6', "=T{$ptdRow}"); // OPP
        $sheet->setCellValue('F6', "=Y{$ptdRow}"); // LDPE

        // 🧮 Compute the average across C6–F6
        $sheet->setCellValue('G6', "=AVERAGE(C6:F6)");

        // 🎨 Apply formatting to C6–G6
        foreach (['C','D','E','F','G'] as $col) {
            $sheet->getStyle("{$col}6")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("{$col}6")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D8E4BC'],
                ],
                'font' => [
                    'bold' => true,
                    'size' => $col === 'G' ? 18 : 12,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }
    }


    /**
     * Calculate PTD percentage for a specific material type
     * 
     * @param string $materialKey
     * @return float
     */
private function calculatePTDPercentage(string $materialKey): float
{
    $totalRejects = $totalFG = $totalQA = 0;

    foreach ($this->quarterlyData as $quarter) {
        $rej = $quarter[$materialKey]['rej'] ?? 0;
        $fg  = $quarter[$materialKey]['fg'] ?? 0;
        $qa  = $quarter[$materialKey]['qa'] ?? 0;

        // ✅ Safely add only if numeric (i.e., NOT formula strings)
        $totalRejects += is_numeric($rej) ? (float)$rej : 0;
        $totalFG      += is_numeric($fg)  ? (float)$fg  : 0;
        $totalQA      += is_numeric($qa)  ? (float)$qa  : 0;
    }

    $total = $totalRejects + $totalFG + $totalQA;
    return $total > 0 ? round(($totalRejects / $total) * 100, 2) : 0;
}


    /**
     * Add production trend chart
     * 
     * @param Worksheet $sheet
     */

    /**
     * Prepare quarterly aggregated data with Excel formulas
     */
    private function prepareQuarterlyData(): array
    {
        $quarterlyData = [];

        for ($q = 1; $q <= 4; $q++) {
            $startMonth = ($q - 1) * 3 + 1;  // 1, 4, 7, 10
            $endMonth   = $startMonth + 2;   // 3, 6, 9, 12

            $quarterlyData[$q] = [
                'output'  => [],
                'preform' => ['fg' => [], 'rej' => [], 'qa' => [], 'percent' => []],
                'caps'    => ['fg' => [], 'rej' => [], 'qa' => [], 'percent' => []],
                'label'   => ['fg' => [], 'rej' => [], 'qa' => [], 'percent' => []],
                'ldpe'    => ['fg' => [], 'rej' => [], 'qa' => [], 'percent' => []],
            ];
        }

        return $quarterlyData;
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
    $lines = Line::pluck('line_number')->toArray();

    for ($m = 1; $m <= 12; $m++) {
        $monthName = Carbon::create()->month($m)->format('F');

        $outputFormulaParts   = [];
        $fgUsageParts         = [];
        $preformsrejParts     = [];
        $preformsqaParts      = [];
        $preformsrejperParts  = [];

        $capsrejParts         = [];
        $capsqaParts          = [];
        $capsrejperParts      = [];

        $opprejParts          = [];
        $oppqaParts           = [];
        $opprejperParts       = [];

        $ldperejParts         = [];
        $ldpeqaParts          = [];
        $ldperejperParts      = [];

        foreach ($lines as $line) {
            $outputFormulaParts[]   = "INDEX('L{$line} P{$m}'!F1:F200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $fgUsageParts[]         = "INDEX('L{$line} P{$m}'!H1:H200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";

            $preformsrejParts[]     = "INDEX('L{$line} P{$m}'!I1:I200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $preformsqaParts[]      = "INDEX('L{$line} P{$m}'!J1:J200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $preformsrejperParts[]  = "INDEX('L{$line} P{$m}'!K1:K200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";

            $capsrejParts[]         = "INDEX('L{$line} P{$m}'!N1:N200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $capsqaParts[]          = "INDEX('L{$line} P{$m}'!O1:O200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $capsrejperParts[]      = "INDEX('L{$line} P{$m}'!P1:P200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";

            $opprejParts[]          = "INDEX('L{$line} P{$m}'!S1:S200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $oppqaParts[]           = "INDEX('L{$line} P{$m}'!T1:T200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $opprejperParts[]       = "INDEX('L{$line} P{$m}'!U1:U200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";

            $ldperejParts[]         = "INDEX('L{$line} P{$m}'!X1:X200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $ldpeqaParts[]          = "INDEX('L{$line} P{$m}'!Y1:Y200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
            $ldperejperParts[]      = "INDEX('L{$line} P{$m}'!Z1:Z200, MATCH(\"PTD\", 'L{$line} P{$m}'!E1:E200, 0))";
        }

        // SUM numeric values
        $outputFormula   = "=SUM(" . implode(",", $outputFormulaParts) . ")";
        $fgUsage         = "=SUM(" . implode(",", $fgUsageParts) . ")";
        $preformsrej     = "=SUM(" . implode(",", $preformsrejParts) . ")";
        $preformsqa      = "=SUM(" . implode(",", $preformsqaParts) . ")";
$preformsrejper  = "=IFERROR(AVERAGE(" . implode(",", $preformsrejperParts) . "),0)";

        $capsrej         = "=SUM(" . implode(",", $capsrejParts) . ")";
        $capsqa          = "=SUM(" . implode(",", $capsqaParts) . ")";
$capsrejper      = "=IFERROR(AVERAGE(" . implode(",", $capsrejperParts) . "),0)";

        $opprej          = "=SUM(" . implode(",", $opprejParts) . ")";
        $oppqa           = "=SUM(" . implode(",", $oppqaParts) . ")";
$opprejper       = "=IFERROR(AVERAGE(" . implode(",", $opprejperParts) . "),0)";

        $ldperej         = "=SUM(" . implode(",", $ldperejParts) . ")";
        $ldpeqa          = "=SUM(" . implode(",", $ldpeqaParts) . ")";
$ldperejper      = "=IFERROR(AVERAGE(" . implode(",", $ldperejperParts) . "),0)";

        $rows[] = [
            $m, "P{$m}", $monthName, "80.00%", $outputFormula,
            'Preforms', $fgUsage, $preformsrej, $preformsqa, $preformsrejper, 
            'Caps', $fgUsage, $capsrej , $capsqa, $capsrejper, 
            'OPP Labels', $fgUsage, $opprej, $oppqa, $opprejper, 
            'LDPE Shrinkfilm', $outputFormula, $ldperej, $ldpeqa, $ldperejper, 
        ];
    }

    return $rows;
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
    $preforms = new DataSeriesValues('Number', "'{$sheetTitle}'!\$J{$dataStartRow}:\$J{$dataEndRow}", null, $pointCount);
    $caps     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$O{$dataStartRow}:\$O{$dataEndRow}", null, $pointCount);
    $labels   = new DataSeriesValues('Number', "'{$sheetTitle}'!\$T{$dataStartRow}:\$T{$dataEndRow}", null, $pointCount);
    $ldpe     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$Y{$dataStartRow}:\$Y{$dataEndRow}", null, $pointCount);

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

    // Chart colors (line colors)
    $lineColors = [
        'Target MAT Efficiency, %' => self::TARGET_COLOR,
        'PREFORMS'                 => self::PREFORM_COLOR,
        'CAPS'                     => self::CAPS_COLOR,
        'OPP LABELS'               => self::OPP_LABELS_COLOR,
        'LDPE Shrinkfilm'          => self::LDPE_COLOR,
    ];

    // Marker fill colors (circle colors)
    $markerColors = [
        'Target MAT Efficiency, %' => '4A7EBB',
        'PREFORMS'                 => 'BE4B48',
        'CAPS'                     => '98B954',
        'OPP LABELS'               => '7D60A0',
        'LDPE Shrinkfilm'          => '46AAC5',
    ];

    // Data columns
    $indicators = [
        ['Target MAT Efficiency, %',  'D', 'percent'],
        ['Production (Output, Cs)',   'E', 'number'],
        ['PREFORMS',                  'J', 'percent'],
        ['CAPS',                      'O', 'percent'],
        ['OPP LABELS',                'T', 'percent'],
        ['LDPE Shrinkfilm',           'Y', 'percent'],
    ];

    // ===== Header row =====
    $sheet->mergeCells("{$labelCol}{$startRow}:{$secondCol}{$startRow}");
    $sheet->setCellValue("{$labelCol}{$startRow}", 'Indicator');
    $sheet->getStyle("{$labelCol}{$startRow}:{$secondCol}{$startRow}")
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