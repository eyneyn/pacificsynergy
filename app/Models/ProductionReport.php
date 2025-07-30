<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'production_date', 
        'shift',
        'line', 
        'ac1', 'ac2', 'ac3', 'ac4',
        'manpower_present', 'manpower_absent',
        'sku', 
        'fbo_fco', 
        'lbo_lco', 
        'total_outputCase',

        'filler_speed', 
        'opp_labeler_speed',
        'opp_labels',
        'shrinkfilm',
        'caps_filling', 
        'bottle_filling', 

        'blow_molding_output', 
        'speed_blow_molding',
        'preform_blow_molding', 
        'bottles_blow_molding',

        'qa_remarks', 
        'with_label', 
        'without_label',
        'total_sample',

        'total_downtime',
        'bottle_code',

        'line_efficiency',
        
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function issues()
    {
        return $this->hasMany('App\Models\ProductionIssues', 'production_reports_id');
    }
    public function line()
    {
        return $this->belongsTo(Line::class, 'line', 'line_number');
    }
    public function standard()
    {
        return $this->belongsTo('App\Models\Standard', 'sku', 'description');
    }
    public function defects()
    {
        return $this->belongsToMany('App\Models\Defect', 'production_report_defect', 'production_report_id', 'defect_id');
    }
public function lineQcRejects()
{
    return $this->hasMany(LineQcReject::class, 'production_reports_id')->with('defect');
}

public function maintenances()
{
    return $this->belongsToMany(Maintenance::class, 'maintenance_production_report', 'production_report_id', 'maintenance_id');
}

    public function statuses()
{
    return $this->hasMany(Status::class);
}
public function histories()
{
    return $this->hasMany(ProductionReportHistory::class);
}
}
