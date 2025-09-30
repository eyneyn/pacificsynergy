<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineEfficiencyAnalytics extends Model
{
    use HasFactory;

    // Table name (optional, Laravel will guess from class name but better to be explicit)
    protected $table = 'line_efficiency_analytics';

    // Fillable fields for mass assignment
    protected $fillable = [
        'production_report_id',
        'line',
        'production_date',
        'sku',
        'bottlesPerCase',   // ✅ correct
        'line_efficiency',
        'downtime_type',
        'category',
        'minutes',
    ];


    // Casts for automatic type conversion
    protected $casts = [
        'production_date'   => 'date',
        'line_efficiency'   => 'decimal:2',
        'minutes'           => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // If you have a ProductionReport model
    public function productionReport()
    {
        return $this->belongsTo(ProductionReport::class, 'production_report_id');
    }

    // If you have a Line model
    public function lineRef()
    {
        return $this->belongsTo(Line::class, 'line');
    }
}
