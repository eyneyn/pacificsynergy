<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Line;

class LineSeeder extends Seeder
{
    public function run(): void
    {
        Line::create([
            'line_number' => 1,
            'status' => 'Active',
        ]);

        Line::create([
            'line_number' => 2,
            'status' => 'Active',
        ]);
    }
}
