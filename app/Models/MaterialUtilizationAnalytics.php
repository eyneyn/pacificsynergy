<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUtilizationAnalytics extends Model
{
    use HasFactory;

    protected $table = 'material_utilization_analytics';

    protected $fillable = [
        'production_report_id',
        'line',
        'production_date',
        'sku',
        'bottlePerCase',
        'targetMaterialEfficiency',
        'total_output',

        'preformDesc', 'preform_fg', 'preform_rej', 'preform_qa', 'preform_pct',
        'capsDesc', 'caps_fg', 'caps_rej', 'caps_qa', 'caps_pct',
        'labelDesc', 'label_fg', 'label_rej', 'label_qa', 'label_pct',
        'ldpeDesc', 'ldpe_fg', 'ldpe_rej', 'ldpe_qa', 'ldpe_pct',
    ];

    /**
     * Relationship to the ProductionReport model
     */
    public function productionReport()
    {
        return $this->belongsTo(ProductionReport::class, 'production_report_id');
    }
}
