<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionReportHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'production_report_id',
        'old_data',
        'new_data',
        'updated_by',
        'updated_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function report()
    {
        return $this->belongsTo(ProductionReport::class, 'production_report_id');
    }
}

