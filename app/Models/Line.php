<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Line extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'line_number';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['line_number', 'status'];
}