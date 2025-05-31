<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'mat_no',
        'size',
        'description',
        'bottles_per_case',
        'preform_weight',
        'ldpe_size',
        'cases_per_roll',
        'caps',
        'opp_label',
        'barcode_sticker',
        'alt_preform_for_350ml',
        'preform_weight2',
    ];
}
