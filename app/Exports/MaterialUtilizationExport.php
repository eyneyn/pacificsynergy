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



/**
 * Material Utilization Export Class
 * 
 * This class generates comprehensive Excel reports for material utilization
 * including production data, weekly summaries, and trend charts.
 */
class MaterialUtilizationExport implements FromArray, WithHeadings, WithEvents, WithCustomStartCell, WithStyles, WithCharts, WithTitle
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
        $this->prepareWeeklyData();
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
    return 'L' . $this->line . ' '. $this->monthName .' '. $this->year;
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
                $this->autoSizeColumns($sheet);
                $this->setZoomLevel($sheet);
$this->applyRejectsConditionalFormatting($sheet, $startRow, $endRow);
            },
        ];
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
                $sheet->setCellValue($cell, $cellValue);

                // Apply cell styling
                $this->applyCellStyling($sheet, $cell, $colLetter, $cellValue);
                $colLetter++;
            }
            $sheet->setCellValue("A{$currentRow}", Carbon::parse($row[1])->format('j'));  // Column Z stores day number
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
            
            if ($numericValue < 0.99) {
                $styleArray['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '92D050'] // Green for good performance
                ];
            } elseif ($numericValue > 1.00) {
                $styleArray['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000'] // Red for poor performance
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
        $ytdStartRow = $lastDataRow + 2;
        $ytdEndRow = $ytdStartRow + 5; // W1-W5 + PTD

        // Create main title block
        $this->createMTDTitleBlock($sheet, $ytdStartRow, $ytdEndRow);
        
        // Add week labels and production output
        $this->addWeekLabelsAndOutput($sheet, $ytdStartRow);
        
        // Add material type columns
        $this->addMaterialTypeColumns($sheet, $ytdStartRow);
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
        // Merge cells for title
        $sheet->mergeCells("B{$startRow}:D{$endRow}");
        $sheet->setCellValue("B{$startRow}", "MTD RM SUMMARY REPORT");
        
        // Apply styling to title block
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
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);
    }

    /**
     * Add week labels and production output data
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     */
    private function addWeekLabelsAndOutput(Worksheet $sheet, int $startRow): void
    {
        $weekLabels = ['W1', 'W2', 'W3', 'W4', 'W5', 'PTD'];
        $totalOutput = 0;

        foreach ($weekLabels as $i => $label) {
            $rowNum = $startRow + $i;
            $isBold = $label === 'PTD';

            // Add week label in column E
            $sheet->setCellValue("E{$rowNum}", $label);
            
            // Add production output in column F
            if ($label !== 'PTD') {
                $weeklyOutput = $this->weeklyData[$i]['output'] ?? 0;
                $sheet->setCellValue("F{$rowNum}", $weeklyOutput);
                $totalOutput += $weeklyOutput;
            } else {
                $sheet->setCellValue("F{$rowNum}", $totalOutput);
            }

            // Apply styling to both columns
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
        }
    }

    /**
     * Add material type columns to MTD summary
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     */
    private function addMaterialTypeColumns(Worksheet $sheet, int $startRow): void
    {
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

            // Add data columns
            $this->addMaterialDataColumns($sheet, $startRow, $startCol, $info);
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
        $weekLabels = ['W1', 'W2', 'W3', 'W4', 'W5', 'PTD'];
        
        // Calculate column positions
        $cols = [];
        for ($i = 0; $i < 4; $i++) {
            $cols[$dataTypes[$i]] = chr(ord($startCol) + 1 + $i);
        }

        foreach ($dataTypes as $type) {
            $col = $cols[$type];
            
            foreach ($weekLabels as $i => $label) {
                $rowNum = $startRow + $i;
                $isBold = $label === 'PTD';

                // Calculate and set value
                if ($label !== 'PTD') {
                    $value = $this->getWeeklyValue($i, $materialInfo['data_key'], $type);
                } else {
                    $value = $this->calculatePTDValue($materialInfo['data_key'], $type);
                }
                
                $sheet->setCellValue("{$col}{$rowNum}", $value);

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
     * Get weekly value for specific material and data type
     * 
     * @param int $weekIndex
     * @param string $materialKey
     * @param string $dataType
     * @return mixed
     */
    private function getWeeklyValue(int $weekIndex, string $materialKey, string $dataType)
    {
        return $this->weeklyData[$weekIndex][$materialKey][$dataType] ?? 
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
            $totalRejects = $totalFG = $totalQA = 0;
            
            foreach ($this->weeklyData as $week) {
                $totalRejects += $week[$materialKey]['rej'] ?? 0;
                $totalFG += $week[$materialKey]['fg'] ?? 0;
                $totalQA += $week[$materialKey]['qa'] ?? 0;
            }
            
            $total = $totalRejects + $totalFG + $totalQA;
            return $total > 0 ? number_format(($totalRejects / $total) * 100, 2) . '%' : '0.00%';
        } else {
            $total = 0;
            foreach ($this->weeklyData as $week) {
                $total += $week[$materialKey][$dataType] ?? 0;
            }
            return $total;
        }
    }

    /**
     * Add PTD calculations in row 6
     * 
     * @param Worksheet $sheet
     */
    private function addPTDCalculations(Worksheet $sheet): void
    {
        // Calculate PTD percentages for each material type
        $ptdPreform = $this->calculatePTDPercentage('preform');
        $ptdCaps = $this->calculatePTDPercentage('caps');
        $ptdOPP = $this->calculatePTDPercentage('label');
        $ptdShrink = $this->calculatePTDPercentage('ldpe');
        $ptdAverage = round(($ptdPreform + $ptdCaps + $ptdOPP + $ptdShrink) / 4, 2);

        // Set values in respective cells--
        $sheet->setCellValue('C6', $ptdPreform / 100);
        $sheet->setCellValue('D6', $ptdCaps    / 100);
        $sheet->setCellValue('E6', $ptdOPP     / 100);
        $sheet->setCellValue('F6', $ptdShrink  / 100);
        $sheet->setCellValue('G6', $ptdAverage / 100);

        foreach (['C','D','E','F','G'] as $col) {
            $sheet->getStyle("{$col}6")->getNumberFormat()->setFormatCode('0.00%');
        }

        // Apply styling to individual material cells
        foreach (range('C', 'F') as $col) {
            $sheet->getStyle("{$col}6")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D8E4BC'], // Light green
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getStyle("{$col}6")->getNumberFormat()->setFormatCode('0.00%');
        }

        // Special styling for total average
        $sheet->getStyle("G6")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D8E4BC'],
            ],
            'font' => [
                'bold' => true,
                'size' => 18, // Larger font for emphasis
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle("G6")->getNumberFormat()->setFormatCode('0.00%');
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
        
        foreach ($this->weeklyData as $week) {
            $totalRejects += $week[$materialKey]['rej'] ?? 0;
            $totalFG += $week[$materialKey]['fg'] ?? 0;
            $totalQA += $week[$materialKey]['qa'] ?? 0;
        }
        
        $total = $totalRejects + $totalFG + $totalQA;
        return $total > 0 ? round(($totalRejects / $total) * 100, 2) : 0;
    }

    /**
     * Add production trend chart
     * 
     * @param Worksheet $sheet
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

// Y axis
$yAxis = new Axis(); $yAxis->setAxisNumberProperties('0.00%'); $yAxis->setAxisOptionsProperties('nextTo', null, null, null, null, null, 0.00, 0.0120, 0.0020);

// Major gridlines
$majorGrid = new GridLines();
$majorGrid->setLineStyleProperties(
    0.75,                                        // width (pt)
    Properties::LINE_STYLE_COMPOUND_SIMPLE,      // compound
    Properties::LINE_STYLE_DASH_SOLID,           // dash
    Properties::LINE_STYLE_CAP_FLAT,             // cap
    Properties::LINE_STYLE_JOIN_BEVEL            // join
);
$majorGrid->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$yAxis->setMajorGridlines($majorGrid);

// Minor gridlines
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
    $preforms = new DataSeriesValues('Number', "'{$sheetTitle}'!\$K{$dataStartRow}:\$K{$dataEndRow}", null, $pointCount);
    $caps     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$P{$dataStartRow}:\$P{$dataEndRow}", null, $pointCount);
    $labels   = new DataSeriesValues('Number', "'{$sheetTitle}'!\$U{$dataStartRow}:\$U{$dataEndRow}", null, $pointCount);
    $ldpe     = new DataSeriesValues('Number', "'{$sheetTitle}'!\$Z{$dataStartRow}:\$Z{$dataEndRow}", null, $pointCount);

    // Series labels
    $seriesLabels = [
        new DataSeriesValues('String', null, null, 1, ['Target Material Efficiency, %']),
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

    $chart->setTopLeftPosition('B8');
    $chart->setBottomRightPosition('J26');
    $chart->setChartAxisY($yAxis);

    return [$chart];
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
        $sheet->getParent()->getActiveSheet()->getSheetView()->setZoomScale(70);
    }

    /**
     * Prepare weekly aggregated data from production reports
     * 
     * This method groups production data by weeks and calculates
     * totals and percentages for each material type.
     */
    private function prepareWeeklyData(): void
    {
        // Fetch production reports for the specified period
        $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
            ->whereMonth('production_date', $this->month)
            ->whereYear('production_date', $this->year)
            ->when($this->line, fn($q) => $q->where('line', $this->line))
            ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
            ->orderBy('production_date')
            ->get();

        // Group reports by week of month
        $groupedWeeks = $reports->groupBy(
            fn($report) => 'Week ' . Carbon::parse($report->production_date)->weekOfMonth
        );

        $rawWeeklyData = [];

        // Process each week's data
        foreach ($groupedWeeks as $weekKey => $group) {
            // Calculate weekly totals
            $outputSum = $group->sum('total_outputCase');
            $bottleSum = $group->sum(fn($r) => ($r->standard?->bottles_per_case ?? 0) * ($r->total_outputCase ?? 0));
            $qaSampleBottle = $group->sum('total_sample');
            $qaSampleCaps = $group->sum('total_sample');
            $qaSampleLabel = $group->sum('with_label');

            // Initialize reject counters by category
            $rejects = [
                'Bottle' => 0,
                'Caps' => 0,
                'Label' => 0,
                'LDPE Shrinkfilm' => 0
            ];

            // Aggregate rejects by category
            foreach ($group as $report) {
                $qc = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
                foreach ($rejects as $cat => $_) {
                    $rejects[$cat] += ($qc[$cat] ?? collect())->sum('quantity');
                }
            }

            // Helper function to calculate percentage
            $calcPercent = fn($rej, $usage, $qa) =>
                ($rej + $usage + $qa) > 0 
                    ? number_format(($rej / ($rej + $usage + $qa)) * 100, 2) . '%' 
                    : '0.00%';

            // Store processed weekly data
            $rawWeeklyData[$weekKey] = [
                'output' => $outputSum,
                'preform' => [
                    'fg' => $bottleSum,
                    'rej' => $rejects['Bottle'],
                    'qa' => $qaSampleBottle,
                    'percent' => $calcPercent($rejects['Bottle'], $bottleSum, $qaSampleBottle),
                ],
                'caps' => [
                    'fg' => $bottleSum,
                    'rej' => $rejects['Caps'],
                    'qa' => $qaSampleCaps,
                    'percent' => $calcPercent($rejects['Caps'], $bottleSum, $qaSampleCaps),
                ],
                'label' => [
                    'fg' => $bottleSum,
                    'rej' => $rejects['Label'],
                    'qa' => $qaSampleLabel,
                    'percent' => $calcPercent($rejects['Label'], $bottleSum, $qaSampleLabel),
                ],
                'ldpe' => [
                    'fg' => $outputSum,
                    'rej' => $rejects['LDPE Shrinkfilm'],
                    'qa' => 0,
                    'percent' => $calcPercent($rejects['LDPE Shrinkfilm'], $outputSum, 0),
                ],
            ];
        }

        // Normalize to fixed Week 1-5 structure
        $weeklyData = [];
        for ($i = 1; $i <= 5; $i++) {
            $key = "Week $i";
            $weeklyData[] = [
                'week' => $key,
                'output' => $rawWeeklyData[$key]['output'] ?? 0,
                'preform' => $rawWeeklyData[$key]['preform'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
                'caps' => $rawWeeklyData[$key]['caps'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
                'label' => $rawWeeklyData[$key]['label'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
                'ldpe' => $rawWeeklyData[$key]['ldpe'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
            ];
        }

        $this->weeklyData = $weeklyData;
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
        
        // Fetch daily production reports
        $dailyReports = ProductionReport::with(['standard', 'lineQcRejects.defect'])
            ->whereMonth('production_date', $this->month)
            ->whereYear('production_date', $this->year)
            ->when($this->line, fn($q) => $q->where('line', $this->line))
            ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
            ->orderBy('production_date')
            ->get();

        foreach ($dailyReports as $index => $report) {
            $rows[] = $this->formatReportRow($report, $index + 1);

            // Add the day-only field for the chart
        $row['day'] = Carbon::parse($report->production_date)->format('j'); // e.g., 1, 2, 3...

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
    private function formatReportRow(ProductionReport $report, int $rowNumber): array
    {
        // Basic report information
        $productionDate = Carbon::parse($report->production_date)->format('F j, Y');
        $sku = $report->standard->description ?? 'No Run';
        $bottlesPerCase = $report->standard->bottles_per_case ?? '';
        $efficiency = '1.00%'; // Fixed efficiency target
        $output = $report->total_outputCase ?? 0;

        // Calculate FG usage
        $fgUsage = $output && $bottlesPerCase ? $output * $bottlesPerCase : 0;

        // Group rejects by material category
        $groupedRejects = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
        $preformRejects = ($groupedRejects['Bottle'] ?? collect())->sum('quantity');
        $capsRejects = ($groupedRejects['Caps'] ?? collect())->sum('quantity');
        $labelRejects = ($groupedRejects['Label'] ?? collect())->sum('quantity');
        $ldpeRejects = ($groupedRejects['LDPE Shrinkfilm'] ?? collect())->sum('quantity');

        // Helper function for percentage calculation
$calcPercent = fn($rejects, $fg, $qa) =>
    ($rejects + $fg + $qa) > 0 ? round($rejects / ($rejects + $fg + $qa), 4) : 0; // numeric fraction

        // Get material descriptions from standard
        $preformDesc = $report->standard->preform_weight ?? '';
        $capsDesc = $report->standard->caps ?? '';
        $labelDesc = $report->standard->opp_label ?? '';
        $ldpeDesc = $report->standard->ldpe_size ?? '';

        // Build complete row array
        return array_merge(
            [$rowNumber], // Row number
            [
                $productionDate,
                $sku,
                $bottlesPerCase,
                $efficiency,
                $output,
                // PREFORMS section
                $preformDesc,
                $fgUsage,
                $preformRejects,
                $report->total_sample ?? 0,
                $calcPercent($preformRejects, $fgUsage, $report->total_sample ?? 0),
                // CAPS section
                $capsDesc,
                $fgUsage,
                $capsRejects,
                $report->total_sample ?? 0,
                $calcPercent($capsRejects, $fgUsage, $report->total_sample ?? 0),
                // OPP LABELS section
                $labelDesc,
                $fgUsage,
                $labelRejects,
                $report->with_label ?? 0,
                $calcPercent($labelRejects, $fgUsage, $report->with_label ?? 0),
                // LDPE SHRINKFILM section
                $ldpeDesc,
                $fgUsage,
                $ldpeRejects,
                0, // No QA samples for LDPE
                $calcPercent($ldpeRejects, $fgUsage, 0),
            ]
        );
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

}