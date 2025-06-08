<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'production_report_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productionReport()
    {
        return $this->belongsTo(ProductionReport::class);
    }
}
