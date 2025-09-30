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

    // ✅ Only use created_at, no updated_at
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productionReport()
    {
        return $this->belongsTo(ProductionReport::class);
    }

    /**
     * 🔔 Notification: message
     */
    public function getNotificationMessageAttribute(): string
    {
        $sku = $this->productionReport?->standard?->description ?? 'N/A';
        $line = $this->productionReport?->line?->line_number ?? $this->productionReport?->line ?? 'N/A';
        $userName = trim(($this->user?->first_name ?? '') . ' ' . ($this->user?->last_name ?? ''));

        if ($this->status === 'Submitted') {
            return "{$sku} | Line {$line} was submitted by {$userName}.";
        } elseif ($this->status === 'Validated') {
            return "{$sku} | Line {$line} was validated by {$userName}.";
        }

        return "{$sku} | Line {$line} has status {$this->status} by {$userName}.";
    }

    /**
     * 🔔 Notification: time
     */
    public function getNotificationTimeAttribute()
    {
        return $this->created_at;
    }

    /**
     * 🔔 Notification: report_id
     */
    public function getNotificationReportIdAttribute()
    {
        return $this->production_report_id;
    }
}
