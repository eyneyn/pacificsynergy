<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
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

        //'two_factor_secret',
        //'two_factor_enabled',

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

    protected $appends = ['name', 'photo_url', 'role_label', 'permission_label'];

    /**
     * Computed full name.
     */
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function getPermissionLabelAttribute(): string
    {
        return $this->getAllPermissions()->pluck('name')->implode(', ');
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * Computed profile photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        $path = $this->photo ?? '';

        // Handle absolute path in public/
        if ($path && file_exists(public_path($path))) {
            return asset($path);
        }

        // Handle storage disk paths
        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // Default image fallback
        return asset('img/default.jpg');
    }
}
