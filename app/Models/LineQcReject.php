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
        // 👇 Ensure soft-deleted defects are still accessible
        return $this->belongsTo(Defect::class, 'defects_id')->withTrashed();
    }
}
