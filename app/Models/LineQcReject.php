<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineQcReject extends Model
{
    protected $fillable = [
        'production_reports_id',
        'defects_id',
        'quantity',
    ];

    public function defect()
    {
        return $this->belongsTo(Defect::class, 'defects_id');
    }
}
