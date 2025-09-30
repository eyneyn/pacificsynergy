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
use App\Models\Maintenance;

class YTDLineSummaryWorksheet implements WithHeadings, WithEvents, WithCustomStartCell, WithStyles, WithCharts, WithTitle
{
    // Class properties
    protected $line;          // Production line identifier
    protected $month;         // Report month
    protected $monthName;     // Report month name
    protected $year;          // Report year
    protected $data;          // Prepared report data
    public $weeklyData;       // Weekly aggregated data
    // Constants for styling colors
    const OPL_COLOR = '8064A2';
    const EPL_COLOR = '4BACC6';
    const OPP_LABELS_COLOR = '4F6228';
    const LDPE_COLOR = '974706';
    const HEADER_COLOR = '0070C0';
    const YELLOW_FILL = 'FFFF00';
    const LIGHT_FILL_DBE5F1 = 'DBE5F1';
    const LIGHT_FILL_F2F2F2 = 'F2F2F2';
    const TARGET_COLOR   = '0070C0';
    const LE_COLOR = '4F81BD'; 
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
     * Define table headers starting from row 33
     * 
     * @return array
     */
    public function title(): string
    {
        return 'L' . $this->line . ' YTD SUMMARY ' . $this->year;
    }
    public function headings(): array
    {
        return array_merge([
            " ",
            "Production Date",
            "SKU",
            "Size",
            "Target LE,%",
            "LE,%",
            "OPL,%",
            "EPL,%",
            "Top DT"
        ]);
    }
    /**
     * Define starting cell for data (row 33)
     * 
     * @return string
     */
    public function startCell(): string
    {
        return 'A33';
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
            34 => ['font' => ['bold' => true, 'size' => 10]],  // Table headers
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
            $startRow = 34;
            $endRow   = $startRow + count($this->data) - 1; 
                foreach (['C', 'D'] as $col) {
                    $sheet->getColumnDimension($col)->setVisible(false);
                }

            // ✅ Set tab color based on line number
            $color = self::LINE_TAB_COLORS[$this->line] ?? 'FFFFFF'; // Default white if undefined
            $sheet->getTabColor()->setRGB($color);
                // Apply all formatting methods
                $this->addPTDLineEfficiency($sheet);
                $this->addReportHeaders($sheet);
                $this->addYellowFillRow($sheet);
                $this->addTableHeaders($sheet);
                $this->populateDataRows($sheet);
                $this->addMTDSummaryTable($sheet);
                $this->charts($sheet);
                $sheetTitle   = $this->title();
                $dataStartRow = 34;
                $dataEndRow   = $dataStartRow + count($this->data) - 1;
                $leftCol      = 'B';
                $rightCol     = $this->colNameFromIndex(count($this->headings())); // last data col
                $chartBottom  = 25; // same value you used in charts()
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
        $text = $richText->createTextRun("Covering period: {$this->year}");
        $text->getFont()->setBold(true)->setSize(11)->setUnderline(Font::UNDERLINE_SINGLE);

        // Set header values
        $sheet->setCellValue('A1', "     MAINTENANCE DEPARTMENT");
        $sheet->setCellValue('A2', "      LINE {$this->line} DOWNTIME ANALYSIS ");
        $sheet->setCellValue('A3', $richText);

        // Apply header styling
        $sheet->getStyle("A1:A3")->applyFromArray([
            'font' => ['bold' => true]
        ]);
        $sheet->getStyle("A1")->getFont()->setSize(20);
        $sheet->getStyle("A2")->getFont()->setSize(16);
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
     * Fill row 28 with background + totals
     *
     * @param Worksheet $sheet
     */
    private function addYellowFillRow(Worksheet $sheet, int $row = 32): void
    {
        $highestCol = $sheet->getHighestColumn();
        $dataStart  = 34; // first production row
        $dataEnd    = $dataStart + count($this->data) - 1;

        // ===== 1. Base fill yellow =====
        $sheet->getStyle("B{$row}:{$highestCol}{$row}")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::YELLOW_FILL],
            ],
            'font' => ['bold' => true],
        ]);

        // ===== 2. Static gray ranges =====
        $grayRanges = ["K{$row}:M{$row}", "O{$row}:Q{$row}"];
        foreach ($grayRanges as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'], // ✅ must be string
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']       // ✅ white text
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // ===== 3. Add SUM formulas for fixed cols K–M only =====
        foreach (['K','L','M'] as $col) {
            $sheet->setCellValue("{$col}{$row}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
        }

        // ===== 3b. Merge O–Q and add label =====
        $sheet->mergeCells("O{$row}:Q{$row}");
        $sheet->setCellValue("O{$row}", "PERCENT IMPACT");

        // ===== 4. Maintenance columns (dynamic) =====
        $oplTypes = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

        $oplStartIndex = 19; // starting index (col S usually in your setup)
        $oplEndIndex   = $oplStartIndex + count($oplTypes) - 1;
        $eplStartIndex = $oplEndIndex + 3; // +1 spacer
        $eplEndIndex   = $eplStartIndex + count($eplTypes) - 1;

        // OPL totals
        for ($i = $oplStartIndex; $i <= $oplEndIndex; $i++) {
            $col = $this->colNameFromIndex($i);
            $sheet->setCellValue("{$col}{$row}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'], // ✅ must be string
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']       // ✅ white text
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // EPL totals
        for ($i = $eplStartIndex; $i <= $eplEndIndex; $i++) {
            $col = $this->colNameFromIndex($i);
            $sheet->setCellValue("{$col}{$row}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 595959],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF']
                ],
            ]);
        }
    }


    /**
     * Add main table headers (rows 33-34)
     * 
     * @param Worksheet $sheet
     */
    private function addTableHeaders(Worksheet $sheet): void
    {
        $startRow = 33;
        
        // Fixed headers for initial columns
        $this->addFixedHeaders($sheet, $startRow);
        

    }
    /**
     * Add fixed headers (A-F columns)
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     */
    private function addFixedHeaders(Worksheet $sheet, int $startRow): void
    {
        // Fixed headers (A to I)
        $fixedHeaders = [
            " ", "Production Date", "SKU", "Size",
            "Target LE, %", "LE,%", "OPL,%", "EPL,%", "Top DT,%"
        ];

        $col = 'A';
        foreach ($fixedHeaders as $header) {
            $cell = $col . $startRow;
            $sheet->setCellValue($cell, $header);

            $fillStyle = $header === " " ? [] : [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::HEADER_COLOR]
            ];

            $sheet->getStyle("{$col}{$startRow}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $header === " " ? '000000' : 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => false
                ],
                'fill' => $fillStyle
            ]);

            // ✅ set column width based on text length (so it stays on one line)
            $width = strlen($header) > 12 ? strlen($header) + 4 : 20;
            $sheet->getColumnDimension($col)->setWidth($width);

            $col++;
        }

        // Fetch Maintenance Types (alphabetical)
        $oplTypes = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();


        // Flatten all additional headers into one array
        $additionalHeaders = array_merge(
            ['Total DT, Minutes', 'OPL, Minutes', 'EPL, Minutes'],
            [''], // spacer
            ['Total DT in %', 'OPL, %', 'EPL, %'],
            [''], // spacer
            $oplTypes,
            [''], // spacer
            [''], 
            $eplTypes
        );

        // Start placing at column J (index 10)
        $colIndex = 11;
        foreach ($additionalHeaders as $headerText) {
            $colLetter = $this->colNameFromIndex($colIndex);

            $sheet->setCellValue("{$colLetter}{$startRow}", $headerText);

            if ($headerText !== '') {
                // Style only non-spacer headers
                $sheet->getStyle("{$colLetter}{$startRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFF00']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::HEADER_COLOR]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ]
                ]);
            } else {
                // Spacer column: blank, narrow width
                $sheet->getStyle("{$colLetter}{$startRow}")->applyFromArray([
                    'font' => ['bold' => false, 'color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_NONE],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

            }

            $colIndex++;
        }
    }
        /**
     * Populate data rows with production information
     * 
     * @param Worksheet $sheet
     */
    private function populateDataRows(Worksheet $sheet): void
    {
        $dataStartRow = 34; // Start after headers

        foreach ($this->data as $rowIndex => $row) {
            $currentRow = $dataStartRow + $rowIndex;
            $colLetter = 'A';

            foreach ($row as $key => $cellValue) {
                // 🚫 Skip metadata only
                if ($key === 'excelRow') {
                    continue;
                }

                // ✅ Expand OPL data
                if ($key === 'oplData') {
                    foreach ($cellValue as $val) {
                        $cell = "{$colLetter}{$currentRow}";
                        $sheet->setCellValue($cell, $val);
                        $this->applyCellStyling($sheet, $cell, $colLetter, $val);
                        $colLetter++;
                    }

                    // 🚀 Add spacer column after OPL before EPL
                    $colLetter++;
                    $colLetter++;
                    continue;
                }

                // ✅ Expand EPL data
                if ($key === 'eplData') {
                    foreach ($cellValue as $val) {
                        $cell = "{$colLetter}{$currentRow}";
                        $sheet->setCellValue($cell, $val);
                        $this->applyCellStyling($sheet, $cell, $colLetter, $val);
                        $colLetter++;
                    }
                    continue;
                }

                // Normal cell
                $cell = "{$colLetter}{$currentRow}";
                $sheet->setCellValue($cell, $cellValue);
                $this->applyCellStyling($sheet, $cell, $colLetter, $cellValue);
                $colLetter++;
            }
        }

        // Format % columns as percent ('E','F','G','H')
        $first = 34;
        $last  = 34 + count($this->data) - 1;
        foreach (['E','F','G','H','O','P','Q'] as $col) {
        $sheet->getStyle("{$col}{$first}:{$col}{$last}")
            ->getNumberFormat()->setFormatCode('0.00%');
    }

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
        // Static columns
        $fillColumnsDBE5F1 = ['B', 'C', 'D','F', 'I'];
        $fillColumnsF2F2F2 = ['D', 'E', 'G', 'H', 'K','L', 'M', 'O', 'P', 'Q'];

        // Dynamically calculate OPL and EPL columns (starts at J, colIndex = 11)
        $oplTypes = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

        // Position tracking
        $startIndex = 11; // Column 'K' (index 11) after the first 9 fixed columns and one spacer

        // Fixed headers before OPL
        $fixedHeaderCount = 3;  // ['Total DT, Minutes', 'OPL, Minutes', 'EPL, Minutes']
        $spacer1 = 1;
        $percentageHeaders = 3; // ['Total DT in %', 'OPL, %', 'EPL, %']
        $spacer2 = 1;

        $oplStartIndex = $startIndex + $fixedHeaderCount + $spacer1 + $percentageHeaders + $spacer2;
        $oplEndIndex = $oplStartIndex + count($oplTypes) - 1;

        $spacer3 = 2;
        $eplStartIndex = $oplEndIndex + 1 + $spacer3;
        $eplEndIndex = $eplStartIndex + count($eplTypes) - 1;

        // Convert OPL/EPL index ranges to column letters
        for ($i = $oplStartIndex; $i <= $oplEndIndex; $i++) {
            $fillColumnsDBE5F1[] = $this->colNameFromIndex($i);
        }
        for ($i = $eplStartIndex; $i <= $eplEndIndex; $i++) {
            $fillColumnsDBE5F1[] = $this->colNameFromIndex($i);
        }

        // Apply fill color
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
        $grayRow = $lastRow;

        // === 1. Static ranges B–I, K–M, O–Q ===
        $ranges = [
            "B{$grayRow}:I{$grayRow}",
            "K{$grayRow}:M{$grayRow}",
            "O{$grayRow}:Q{$grayRow}",
        ];

        foreach ($ranges as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'], // dark gray
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'], // white text
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ]
                ]
            ]);
        }

        // === 2. Maintenance columns (OPL + EPL) ===
        $oplTypes = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

        $oplStartIndex = 19; // column S (depending on your setup)
        $oplEndIndex   = $oplStartIndex + count($oplTypes) - 1;
        $eplStartIndex = $oplEndIndex + 3; // +1 spacer
        $eplEndIndex   = $eplStartIndex + count($eplTypes) - 1;

        // OPL fill
        for ($i = $oplStartIndex; $i <= $oplEndIndex; $i++) {
            $col = $this->colNameFromIndex($i);
            $sheet->getStyle("{$col}{$grayRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ]
                ]
            ]);
        }

        // EPL fill
        for ($i = $eplStartIndex; $i <= $eplEndIndex; $i++) {
            $col = $this->colNameFromIndex($i);
            $sheet->getStyle("{$col}{$grayRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '595959'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ]
                ]
            ]);
        }
    }
        /**
     * Add MTD (Month-to-Date) Summary Report table
     * 
     * @param Worksheet $sheet
     */
    private function addMTDSummaryTable(Worksheet $sheet): void
    {
        $lastDataRow = 34 + count($this->data);
        $ytdStartRow = $lastDataRow + 1;
        $ytdEndRow = $ytdStartRow + 5; // W1-W5 + PTD

        
        // Add quarter labels and production output
        $this->addPTD($sheet, $ytdStartRow);
    }
        /**
     * Create MTD title block
     * 
     * @param Worksheet $sheet
     * @param int $startRow
     * @param int $endRow
     */
    private function addPTD(Worksheet $sheet, int $startRow): void
    {
        $dataStart = 34;                    // first data row
        $dataEnd   = $startRow - 2;         // last month row before PTD
        $ptdRow    = $startRow;             // single PTD row

        // === Label ===
        $sheet->setCellValue("B{$ptdRow}", "PTD");

        // === SUM (Minutes, Maintenance) ===
        foreach (['K','L','M'] as $col) { // Total, OPL, EPL minutes
            $sheet->setCellValue("{$col}{$ptdRow}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
        }

        // Dynamic OPL/EPL maintenance
        $oplTypes = Maintenance::where('type','OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type','EPL')->orderBy('name')->pluck('name')->toArray();

        $oplStartIndex = 19; // col S
        $oplEndIndex   = $oplStartIndex + count($oplTypes) - 1;
        $eplStartIndex = $oplEndIndex + 3;
        $eplEndIndex   = $eplStartIndex + count($eplTypes) - 1;

        foreach (range($oplStartIndex, $oplEndIndex) as $i) {
            $col = $this->colNameFromIndex($i);
            $sheet->setCellValue("{$col}{$ptdRow}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
        }
        foreach (range($eplStartIndex, $eplEndIndex) as $i) {
            $col = $this->colNameFromIndex($i);
            $sheet->setCellValue("{$col}{$ptdRow}", "=IFERROR(SUM({$col}{$dataStart}:{$col}{$dataEnd}),\"\")");
        }

// === AVG (Percent Columns) ===
foreach (['F','G','H','O','P','Q'] as $col) {
    $sheet->setCellValue(
        "{$col}{$ptdRow}", 
        "=IFERROR(AVERAGEIF({$col}{$dataStart}:{$col}{$dataEnd},\">0\"),\"\")"
    );
    $sheet->getStyle("{$col}{$ptdRow}")->getNumberFormat()->setFormatCode('0.00%');

    // ✅ Special case: also mirror F (Line Efficiency) into E6
    if ($col === 'F') {
        $sheet->setCellValue(
            "E6", 
            "=IFERROR({$col}{$ptdRow},\"\")"
        );
        $sheet->getStyle("E6")->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle("E6")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '23527C'], // dark blue
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C4D79B'], // light green background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ]
            ]
        ]);
    }
}


        // === Styling (only B–I, K–M, O–Q, OPL, EPL) ===
        $highlightRanges = [
            "B{$ptdRow}:I{$ptdRow}",
            "K{$ptdRow}:M{$ptdRow}",
            "O{$ptdRow}:Q{$ptdRow}"
        ];

        // Add dynamic OPL/EPL ranges
        $oplStartCol = $this->colNameFromIndex($oplStartIndex);
        $oplEndCol   = $this->colNameFromIndex($oplEndIndex);
        $eplStartCol = $this->colNameFromIndex($eplStartIndex);
        $eplEndCol   = $this->colNameFromIndex($eplEndIndex);

        if ($oplTypes) {
            $highlightRanges[] = "{$oplStartCol}{$ptdRow}:{$oplEndCol}{$ptdRow}";
        }
        if ($eplTypes) {
            $highlightRanges[] = "{$eplStartCol}{$ptdRow}:{$eplEndCol}{$ptdRow}";
        }

        foreach ($highlightRanges as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FCD5B4'],
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ]
                ]
            ]);
        }
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

        $oplTypes = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplTypes = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create()->month($m)->format('F');

            $line_efficiency = "=IFERROR(INDEX('L{$this->line} P{$m}'!F1:F200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $opl = "=IFERROR(INDEX('L{$this->line} P{$m}'!G1:G200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $epl = "=IFERROR(INDEX('L{$this->line} P{$m}'!H1:H200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";

            $totalDT_mins = "=IFERROR(INDEX('L{$this->line} P{$m}'!K1:K200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $epl_mins = "=IFERROR(INDEX('L{$this->line} P{$m}'!L1:L200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $opl_mins = "=IFERROR(INDEX('L{$this->line} P{$m}'!M1:M200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";

            $totalDT_percent = "=IFERROR(INDEX('L{$this->line} P{$m}'!O1:O200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $epl_percent = "=IFERROR(INDEX('L{$this->line} P{$m}'!P1:P200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            $opl_percent = "=IFERROR(INDEX('L{$this->line} P{$m}'!Q1:Q200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";

            // === OPL Maintenance Formulas ===
            $oplFormulas = [];
            $oplStart = 19; // col S
            foreach ($oplTypes as $i => $name) {
                $colLetter = $this->colNameFromIndex($oplStart + $i);
                $oplFormulas[] = "=IFERROR(INDEX('L{$this->line} P{$m}'!{$colLetter}1:{$colLetter}200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            }

            // === EPL Maintenance Formulas ===
            $eplFormulas = [];
            $eplStart = $oplStart + count($oplTypes) + 2; // + spacer
            foreach ($eplTypes as $j => $name) {
                $colLetter = $this->colNameFromIndex($eplStart + $j);
                $eplFormulas[] = "=IFERROR(INDEX('L{$this->line} P{$m}'!{$colLetter}1:{$colLetter}200, MATCH(\"PTD\", 'L{$this->line} P{$m}'!B1:B200, 0)),\"\")";
            }

            $rows[] = array_merge(
                [
                    $m, "P{$m}", "", "", "80%", 
                    $line_efficiency, $opl, $epl, "", "",
                    $totalDT_mins, $epl_mins, $opl_mins, "",
                    $totalDT_percent, $epl_percent, $opl_percent
                ],
                [""],
                $oplFormulas,
                [""], [""],// spacer
                $eplFormulas
            );
        }

        return $rows;
    }

        /**
     * Add production trend chart
     * 
     * @param Worksheet $sheet
     */
    public function charts(): array
    {
        $sheetTitle   = $this->title();
        $dataStartRow = 34;
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
    $yAxis->setAxisNumberProperties('0%');
    $yAxis->setAxisOptionsProperties('nextTo', null, null, null, null, null, 0.0, 1.0, 0.1);
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

    // --- Daily OPL/EPL (col G/H) ---
    $oplSeriesDaily = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        "'{$sheetTitle}'!\$G{$dataStartRow}:\$G{$dataEndRow}",
        null,
        $pointCount
    );
    // ✅ Set OPL to Purple (#800080)
    $oplSeriesDaily->setFillColor(new ChartColor('8064A2', null, ChartColor::EXCEL_COLOR_TYPE_RGB));

    $eplSeriesDaily = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        "'{$sheetTitle}'!\$H{$dataStartRow}:\$H{$dataEndRow}",
        null,
        $pointCount
    );
    // ✅ Set EPL to Blue (#4BACC6)
    $eplSeriesDaily->setFillColor(new ChartColor('4BACC6', null, ChartColor::EXCEL_COLOR_TYPE_RGB));


    $barLabelsDaily = [
        new DataSeriesValues('String', null, null, 1, ['OPL %']),
        new DataSeriesValues('String', null, null, 1, ['EPL %']),
    ];

    $barSeriesDaily = new DataSeries(
        DataSeries::TYPE_BARCHART,
        DataSeries::GROUPING_CLUSTERED,
        [0,1],
        $barLabelsDaily,
        $xAxisLabels,
        [$oplSeriesDaily, $eplSeriesDaily]
    );
    $barSeriesDaily->setPlotDirection(DataSeries::DIRECTION_COL);

    // === Line Series: Target + Line Efficiency ===
    $targetArray = array_fill(0, $pointCount, 0.80);
    $targetSeries = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        null,
        null,
        $pointCount,
        $targetArray
    );
    // === Target LE (gray hollow circle, size 6) ===
    $targetColor = new ChartColor('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
    $targetSeries->setFillColor($targetColor);
    $targetSeries->setPointMarker('circle');
    $targetSeries->setPointSize(8);
    
    $lineEfficiencySeries = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        "'{$sheetTitle}'!\$F{$dataStartRow}:\$F{$dataEndRow}", // Column F only
        null,
        $pointCount
        );
    // === Line Efficiency (solid blue circle, size 7) ===
    $lineColor = new ChartColor('4F81BD', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
    $lineEfficiencySeries->setFillColor($lineColor);
    $lineEfficiencySeries->setPointMarker('circle');
    $lineEfficiencySeries->setPointSize(8);


    $seriesLabels = [
        new DataSeriesValues('String', null, null, 1, ['Target Line Efficiency']),
        new DataSeriesValues('String', null, null, 1, ['Line Efficiency']),
    ];

    $lineSeries = new DataSeries(
        DataSeries::TYPE_LINECHART,
        DataSeries::GROUPING_STANDARD,
        [0, 1],                 // indexes for Target + Efficiency
        $seriesLabels,
        $xAxisLabels,
        [$targetSeries, $lineEfficiencySeries]
    );

    // show markers on the line chart
    $lineSeries->setPlotStyle(DataSeries::STYLE_MARKER);

    // --- Combine with line chart ---
    $combinedPlot = new PlotArea(null, [$barSeriesDaily, $lineSeries]);
    // Chart title
    $richTitle = new RichText();
    $titleRun = $richTitle->createTextRun("SUMMARY LE % - Line {$this->line}");
    $titleRun->getFont()->setSize(14);

    $chart = new Chart(
        'efficiency_chart',
        new Title($richTitle),
        new Legend(Legend::POSITION_RIGHT, null, false),
        $combinedPlot,
        true,
        0,
        null,
        null
    );

    // Position chart
    $chart->setTopLeftPosition("B8");
    $chart->setBottomRightPosition("J25");
    // Primary axis: percent for line (you already set 0–1)
    $chart->setChartAxisY($yAxis);
    $chart->setChartAxisX($xAxis);

    // (Optional) if you want to style the secondary axis too:
    $yAxis2 = new \PhpOffice\PhpSpreadsheet\Chart\Axis();
    $yAxis2->setAxisNumberProperties('0');        // plain numbers for minutes
    $yAxis2->setAxisOptionsProperties('nextTo');  // basic position
    // Only do this if your PhpSpreadsheet version supports it:
    if (method_exists($chart, 'setChartAxisY2')) {
        $chart->setChartAxisY2($yAxis2);
    }
    
    // === OPL AND EPL  ===
    $ybarAxis = new Axis();
    $ybarAxis->setAxisNumberProperties('0%');
    $ybarAxis->setAxisOptionsProperties(
        'nextTo',   // axis position
        null,       // crosses
        null,       // crossBetween
        null,       // crossesAt
        null,       // orientation
        null,       // isVisible
        0.0,        // min = 0%
        1.0,        // max = 100%
        0.1         // major unit = 10%
    );

    // Color the Y axis line itself
    $ybarAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // === OPL series (8064A2) ===
    $oplSeries = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        "'{$sheetTitle}'!\$L\$32",   // OPL total
        null,
        1,
        [],
        null,
        '8064A2'                     // custom fill color
    );

    // === EPL series (4BACC6) ===
    $eplSeries = new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        "'{$sheetTitle}'!\$M\$32",   // EPL total
        null,
        1,
        [],
        null,
        '4BACC6'                     // custom fill color
    );

    // === Series labels (used in legend) ===
    $seriesLabels = [
        new DataSeriesValues('String', null, null, 1, ['OPL, Minutes']),
        new DataSeriesValues('String', null, null, 1, ['EPL, Minutes']),
    ];

    // === Category (just "PTD") ===
    $categories = [
        new DataSeriesValues('String', null, null, 1, ['']),
    ];

    // === Build chart ===
    $barSeries = new DataSeries(
        DataSeries::TYPE_BARCHART,
        DataSeries::GROUPING_CLUSTERED,
        [0, 1],                       // plot order
        $seriesLabels,
        $categories,
        [$oplSeries, $eplSeries]      // 2 series
    );
    $barSeries->setPlotDirection(DataSeries::DIRECTION_COL);

    // === Show values above bars ===
    $layout = new Layout();
    $layout->setShowVal(true);
    $plotArea = new PlotArea($layout, [$barSeries]);

    // === Title (font size 10, not bold) ===
    $rt = new RichText();
    $run = $rt->createTextRun('OPL & EPL, Downtime in Minutes PTD');
    $run->getFont()->setSize(10);
    $run->getFont()->setBold(false);
    $title = new Title($rt);

    // === Bar chart axes ===
    $barYAxis = new Axis();
    $barYAxis->setAxisNumberProperties('0');   // plain number scale
    $barYAxis->setAxisOptionsProperties('nextTo');
    $barYAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    $barXAxis = new Axis();
    $barXAxis->setAxisOptionsProperties('nextTo');
    $barXAxis->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

    // === Final chart ===
    $barChart = new Chart(
        'oplEplChart',
        $title,
        new Legend(Legend::POSITION_BOTTOM, null, false),
        $plotArea,
        true,
        0,
        null,
        null
    );

    $barChart->setTopLeftPosition("K8");
    $barChart->setBottomRightPosition("R25");

    // attach styled axes
    $barChart->setChartAxisY($barYAxis);
    $barChart->setChartAxisX($barXAxis);

        // === OPL Categories Chart (Row 32 Totals) ===
        $oplTypes = Maintenance::where('type', 'OPL')
            ->orderBy('name')
            ->pluck('name')
            ->map(function ($name) {
                // ✅ Insert line breaks every 15 characters for text wrap
                return wordwrap($name, 10, "\n", true);
            })
            ->toArray();

        // === Dynamic OPL columns (row 32) ===
        $oplStartIndex = 19; // Column S
        $oplEndIndex   = $oplStartIndex + count($oplTypes) - 1;

        // Build column letters for values
        $oplCols = [];
        foreach (range($oplStartIndex, $oplEndIndex) as $i) {
            $oplCols[] = $this->colNameFromIndex($i);
        }

        // === Values for each OPL category (row 32) ===
        $oplValues = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            implode(",", array_map(fn($col) => "'{$sheetTitle}'!\${$col}\$32", $oplCols)),
            null,
            count($oplCols)
        );

        // ✅ Set OPL to Purple (#800080)
        $oplValues->setFillColor(new ChartColor('8064A2', null, ChartColor::EXCEL_COLOR_TYPE_RGB));

        // === Category names (labels under bars) ===
        $oplCategories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            null,
            null,
            count($oplTypes),
            $oplTypes
        );

        $oplLegend = [
            new DataSeriesValues('String', null, null, 1, ['OPL Downtimes, Minutes']),
        ];

        // === Build OPL bar series ===
        $oplBarSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0],
            $oplLegend,
            [$oplCategories],
            [$oplValues]
        );
        $oplBarSeries->setPlotDirection(DataSeries::DIRECTION_COL);

        // === Show values above bars ===
        $oplLayout   = new Layout();
        $oplLayout->setShowVal(true);
        $oplPlotArea = new PlotArea($oplLayout, [$oplBarSeries]);

        // === Title (size 10, not bold) ===
        $oplTitleRt = new RichText();
        $oplRun     = $oplTitleRt->createTextRun('OPL, Downtimes in Minutes');
        $oplRun->getFont()->setSize(10)->setBold(false);

        $oplBarChart = new Chart(
            'oplCategoryChart',
            new Title($oplTitleRt),
            new Legend(Legend::POSITION_BOTTOM, null, false),
            $oplPlotArea,
            true,
            0,
            null,
            null
        );

        // === Dynamically set width to match OPL table ===
        $oplLeftCol  = $this->colNameFromIndex($oplStartIndex);
        $oplRightCol = $this->colNameFromIndex($oplEndIndex + 2);

        $oplBarChart->setTopLeftPosition("{$oplLeftCol}8");
        $oplBarChart->setBottomRightPosition("{$oplRightCol}25");

        // === Axis style ===
        $oplYAxis = new Axis();
        $oplYAxis->setAxisNumberProperties('0');
        $oplYAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

        $oplXAxis = new Axis();
        $oplXAxis->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

        // Attach axes
        $oplBarChart->setChartAxisY($oplYAxis);
        $oplBarChart->setChartAxisX($oplXAxis);


        // === EPL Categories Chart (Row 32 Totals) ===
        $eplTypes = Maintenance::where('type', 'EPL')
            ->orderBy('name')
            ->pluck('name')
            ->map(function ($name) {
                // ✅ Insert line breaks every 15 characters for text wrap
                return wordwrap($name, 10, "\n", true);
            })
            ->toArray();

        // === Dynamic EPL columns (row 32) ===
        $eplStartIndex = $oplEndIndex + 3; // after OPL + 2 spacers
        $eplEndIndex   = $eplStartIndex + count($eplTypes) - 1;

        // Build column letters for values
        $eplCols = [];
        foreach (range($eplStartIndex, $eplEndIndex) as $i) {
            $eplCols[] = $this->colNameFromIndex($i);
        }

        // === Values for each EPL category (row 32) ===
        $eplValues = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            implode(",", array_map(fn($col) => "'{$sheetTitle}'!\${$col}\$32", $eplCols)),
            null,
            count($eplCols)
        );
        // ✅ Set EPL to Blue (#4BACC6)
        $eplValues->setFillColor(new ChartColor('4BACC6', null, ChartColor::EXCEL_COLOR_TYPE_RGB));

        // === Category names (labels under bars) ===
        $eplCategories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            null,
            null,
            count($eplTypes),
            $eplTypes
        );

        $eplLegend = [
            new DataSeriesValues('String', null, null, 1, ['EPL Downtimes, Minutes']),
        ];

        // === Build EPL bar series ===
        $eplBarSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0],
            $eplLegend,
            [$eplCategories],
            [$eplValues]
        );
        $eplBarSeries->setPlotDirection(DataSeries::DIRECTION_COL);

        // === Show values above bars ===
        $eplLayout   = new Layout();
        $eplLayout->setShowVal(true);
        $eplPlotArea = new PlotArea($eplLayout, [$eplBarSeries]);

        // === Title (size 10, not bold) ===
        $eplTitleRt = new RichText();
        $eplRun     = $eplTitleRt->createTextRun('EPL, Downtimes in Minutes');
        $eplRun->getFont()->setSize(10)->setBold(false);

        $eplBarChart = new Chart(
            'eplCategoryChart',
            new Title($eplTitleRt),
            new Legend(Legend::POSITION_BOTTOM, null, false),
            $eplPlotArea,
            true,
            0,
            null,
            null
        );

        // === Dynamically set width to match EPL table ===
        $eplLeftCol  = $this->colNameFromIndex($eplStartIndex);
        $eplRightCol = $this->colNameFromIndex($eplEndIndex + 2);

        $eplBarChart->setTopLeftPosition("{$eplLeftCol}8");
        $eplBarChart->setBottomRightPosition("{$eplRightCol}25");

        // === Axis style ===
        $eplYAxis = new Axis();
        $eplYAxis->setAxisNumberProperties('0');
        $eplYAxis->setLineColorProperties('F2F2F2', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

        $eplXAxis = new Axis();
        $eplXAxis->setLineColorProperties('D9D9D9', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

        // Attach axes
        $eplBarChart->setChartAxisY($eplYAxis);
        $eplBarChart->setChartAxisX($eplXAxis);


    return [$chart, $barChart, $oplBarChart, $eplBarChart];
    }

        /**
     * Add production trend chart
     * 
     * @param Worksheet $sheet
     */
    private function addChartFooterTable(
        Worksheet $sheet,
        int $dataStartRow,
        int $dataEndRow,
        string $leftCol,     // 'B'
        string $rightCol,    // e.g., 'Z'
        int $startRow        // e.g., bottom of chart + 1
    ): void {
        $labelCol   = $leftCol; // B
        $secondCol  = $this->colNameFromIndex($this->colIndexFromName($labelCol)); // C
        $firstDataColIndex = $this->colIndexFromName($secondCol) + 1; // Start placing data from column D
        $sheetName = $sheet->getTitle();

        // Data columns to pull for each row (and display horizontally)
        $indicators = [
            ['Target LE, %',  'E', 'percent',  self::TARGET_COLOR],
            ['LE, %','F', 'percent',   self::LE_COLOR],
            ['OPL, %','G', 'percent',  self::OPL_COLOR],
            ['EPL, %', 'H', 'percent',  self::EPL_COLOR],
        ];

        // ===== Header row =====
        $sheet->mergeCells("{$labelCol}{$startRow}:{$secondCol}{$startRow}");
        $sheet->setCellValue("{$labelCol}{$startRow}", 'Indicator');
        $sheet->getStyle("{$labelCol}{$startRow}:{$secondCol}{$startRow}")
            ->applyFromArray([
                'font' => [
                    'bold' => true
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

            // ✅ Make only Column B (the indicator labels) bold
            $sheet->getStyle("{$labelCol}{$r}")->getFont()->setBold(true);

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
        $sheet->getParent()->getActiveSheet()->getSheetView()->setZoomScale(70);
    }
    
}
