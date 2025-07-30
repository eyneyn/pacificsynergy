<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Line;

class AnalyticsController extends Controller
{
public function index()
{
    $activeLines = Line::where('status', 'Active')->orderBy('line_number')->get();

    return view('analytics.index', compact('activeLines'));
}
}
