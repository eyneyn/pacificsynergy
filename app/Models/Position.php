<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory; // ✅ Add this line

    protected $table = 'positions';

    protected $fillable = [
        'position_name',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'position_id');
    }
}
