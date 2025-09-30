<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\ProductionReport;
use App\Exports\YTDLineSummaryWorksheet;
use App\Exports\LineOverallSummaryWorksheet;
use App\Models\Line;

class LineOverallExport implements WithMultipleSheets
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 0) Plant Total Summary sheet (always first)
        $sheets[] = new LineOverallSummaryWorksheet($this->year);
        
        // 🔹 Fetch all active lines
        $lines = Line::pluck('line_number')->toArray();

        // 1) Add all YTD sheets (side by side, one per line)
        foreach ($lines as $line) {
            $sheets[] = new YTDLineSummaryWorksheet(
                $line,
                null,
                $this->year,
                "L{$line} {$this->year} YTD Summary"
            );
        }

        // 2) Add all MTD sheets (for all lines, after YTD group)
        foreach ($lines as $line) {
            for ($m = 1; $m <= 12; $m++) {
                $sheets[] = new MTDLineSummaryExport(
                    $line,
                    $m,
                    $this->year,
                    "L{$line} P{$m}" // e.g. L1 P1, L1 P2 … L2 P1, L2 P2 …
                );
            }
        }

        return $sheets;
    }

}
