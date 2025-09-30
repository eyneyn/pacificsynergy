<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\ProductionReport;
use App\Exports\YTDLineSummaryWorksheet;

class YTDLineSummaryExport implements WithMultipleSheets
{
    protected $line;
    protected $year;

    public function __construct($line, $year)
    {
        $this->line = $line;
        $this->year = $year;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1) Add YTD Worksheet
        $sheets[] = new YTDLineSummaryWorksheet($this->line, null, $this->year, 'YTD SUMMARY');

        // 2) Add MTD sheet
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create()->month($m)->format('F');
            $sheets[]  = new MTDLineSummaryExport($this->line, $m, $this->year, $monthName);
        }

        return $sheets;
    }
}
