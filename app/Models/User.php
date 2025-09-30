<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'employee_number',
        'last_name',
        'first_name',
        'phone_number',
        'department',
        'photo',
        'email',
        'password',
        'status',
        'position_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 👉 Computed full name (so you can call $user->name)
     */
    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function getRoleLabelAttribute()
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function getPermissionLabelAttribute()
    {
        return $this->getAllPermissions()->pluck('name')->implode(', ');
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        $path = $this->photo ?? '';

        if ($path && file_exists(public_path($path))) {
            return asset($path);
        }

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('img/default.jpg');
    }
    
}
