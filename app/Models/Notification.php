<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'production_report_id', 'message', 'is_read'
    ];

    public function report()
    {
        return $this->belongsTo(ProductionReport::class, 'production_report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

