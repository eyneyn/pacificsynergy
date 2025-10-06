<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'event', 'ip_address', 'user_agent', 'context', 'description'
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* -------- Scopes -------- */
    public function scopeOfEvent($q, $event)
    {
        if ($event && $event !== 'all') {
            $q->where('event', $event);
        }
    }

    public function scopeDateRange($q, $from, $to)
    {
        if ($from) $q->whereDate('created_at', '>=', $from);
        if ($to)   $q->whereDate('created_at', '<=', $to);
    }
    /* -------- NEW Helper -------- */
    public static function record($event, $description, $userId = null, array $context = [])
    {
        return self::create([
            'user_id'    => $userId ?? Auth::id(),
            'event'      => $event,
            'description'=> $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => $context,
        ]);
    }
}
