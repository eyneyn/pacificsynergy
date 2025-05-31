<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIssues extends Model
{
    protected $fillable = [
        'production_reports_id',
        'maintenances_id',
        'remarks',
        'minutes',
    ];
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenances_id');
    }
}
