<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Models\Line;
use App\Models\Standard;
use App\Models\Maintenance;
use Carbon\Carbon;
use App\Models\Defect;
use App\Models\ProductionReport;
use App\Models\ProductionIssues;
use App\Models\LineQcReject;

class ProductionReportController extends Controller
{
public function index(Request $request)
{
    $query = ProductionReport::with(['line', 'standard']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('production_date', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%")
            ->orWhere('line', 'like', "%{$search}%")
            ->orWhere('total_outputCase', 'like', "%{$search}%");
        });
    }

    $reports = $query->orderBy('production_date', 'desc')->paginate(10);

    return view('report.index', compact('reports'));
}
public function add()
{
    $lines = Line::orderBy('line_number')->get(); // line_number is unique
    $skus = Standard::orderBy('description')->get(); // SKU from 'description'
    $maintenances = Maintenance::orderBy('name')->orderBy('type')->get();
    $defects = Defect::all();

    // Prepare options for dropdown
    $lineOptions = $lines->mapWithKeys(fn($line) => [$line->line_number => 'Line ' . $line->line_number]);
    $materialsOptions = $maintenances->pluck('name', 'name');

    return view('report.add', [
        'lines' => $lines,
        'skus' => $skus,
        'maintenances' => $maintenances,
        'defects' => $defects,
        'lineOptions' => $lineOptions,
        'materialsOptions' => $materialsOptions, // ✅ added
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'production_date' => 'required|date',
        'shift' => 'required|string',
        'line' => 'required|integer|exists:lines,line_number',
        'ac1' => 'nullable|integer',
        'ac2' => 'nullable|integer',
        'ac3' => 'nullable|integer',
        'ac4' => 'nullable|integer',
        'manpower_present' => 'nullable|integer',
        'manpower_absent' => 'nullable|integer',
        'sku' => 'nullable|string|exists:standard,description',
        'fbo_fco' => 'nullable|string',
        'lbo_lco' => 'nullable|string',
        'total_outputCase' => 'nullable|integer',
        'filler_speed' => 'nullable|integer',
        'opp_labeler_speed' => 'nullable|integer',
        'opp_labels' => 'nullable|integer',
        'shrinkfilm' => 'nullable|integer',
        'caps_filling' => 'nullable|integer',
        'bottle_filling' => 'nullable|integer',
        'blow_molding_output' => 'nullable|integer',
        'speed_blow_molding' => 'nullable|integer',
        'preform_blow_molding' => 'nullable|integer',
        'bottles_blow_molding' => 'nullable|integer',
        'qa_remarks' => 'nullable|string',
        'with_label' => 'nullable|integer',
        'without_label' => 'nullable|integer',
    ]);

    // Generate bottle code
    $productionDate = Carbon::parse($validated['production_date']);
    $expiryDate = $productionDate->copy()->addYear()->format('d F Y');
    $time = ('00:01');
    $lineCode = str_pad($validated['line'], 2, '0', STR_PAD_LEFT); // e.g., 01

    $bottleCode = "EXP {$expiryDate}\n{$time} {$lineCode}";

    // Create Production Report
    $report = ProductionReport::create([
        ...$validated,
        'bottle_code' => $bottleCode,
    ]);

    // Sum downtime
    $totalMinutes = 0;

    if ($request->has(['materials', 'description', 'minutes'])) {
        foreach ($request->materials as $index => $materialName) {
            $maintenance = Maintenance::where('name', $materialName)->first();

            if ($maintenance) {
                $minutes = intval($request->minutes[$index] ?? 0);
                $totalMinutes += $minutes;

                ProductionIssues::create([
                    'production_reports_id' => $report->id,
                    'maintenances_id' => $maintenance->id,
                    'remarks' => $request->description[$index] ?? null,
                    'minutes' => $minutes,
                ]);
            }
        }
    }

    // Update downtime total
    $report->update(['total_downtime' => $totalMinutes]);

    // Save Line QC Rejects
    foreach (['caps', 'bottle', 'label', 'carton'] as $category) {
        $defectKeys = $request->input("qc_{$category}_defect", []);
        $qtyKeys = $request->input("qc_{$category}_qty", []);

        foreach ($defectKeys as $index => $defectName) {
            if ($defectName) {
                $defect = Defect::where('defect_name', $defectName)
                    ->where('category', ucfirst($category))
                    ->first();

                if ($defect) {
                    LineQcReject::create([
                        'production_reports_id' => $report->id,
                        'defects_id' => $defect->id,
                        'quantity' => $qtyKeys[$index] ?? 0,
                    ]);
                }
            }
        }
    }

    return redirect()->route('report.index')->with('success', 'Production report saved successfully.');
}



public function view($id)
{
    $report = ProductionReport::with([
        'issues.maintenance',
        'lineQcRejects.defect', // ✅ now plural to match the relationship
        'line',
        'standard',
    ])->findOrFail($id);

    return view('report.view', ['reports' => $report]);
}
public function edit(ProductionReport $report)
{
    $lines = Line::orderBy('line_number')->get();
    $skus = Standard::orderBy('description')->get();
    $maintenances = Maintenance::orderBy('name')->orderBy('type')->get();
    $defects = Defect::all();

    $lineOptions = $lines->mapWithKeys(fn($line) => [$line->line_number => 'Line ' . $line->line_number]);
    $materialsOptions = $maintenances->pluck('name', 'name');

// Prepare issues array for Alpine
$issues = $report->issues->map(function ($issue) {
    return [
        'material' => $issue->maintenance->name ?? '', // use name, not ID
        'description' => $issue->remarks,
        'minutes' => $issue->minutes,
    ];
});

if (old('materials')) {
    $issues = collect(old('materials'))->map(function ($material, $index) {
        return [
            'material' => $material,
            'description' => old('description')[$index] ?? '',
            'minutes' => old('minutes')[$index] ?? '',
        ];
    });
}

// Group existing rejects into the correct structure
$qcRejects = [
    'Caps' => [],
    'Bottle' => [],
    'Label' => [],
    'Carton' => [],
];

// Map existing database values to Alpine-ready structure
foreach ($report->lineQcRejects as $reject) {
    $category = ucfirst(strtolower($reject->defect->category ?? ''));
    if (isset($qcRejects[$category])) {
        $qcRejects[$category][] = [
            'defect' => $reject->defect->defect_name,
            'qty' => $reject->quantity,
        ];
    }
}

// In case of validation error, prefer old() input
if (old('qc_caps_defect')) {
    foreach (['caps', 'bottle', 'label', 'carton'] as $cat) {
        $defects = old("qc_{$cat}_defect", []);
        $quantities = old("qc_{$cat}_qty", []);
        $qcRejects[ucfirst($cat)] = [];

        foreach ($defects as $i => $defectName) {
            $qcRejects[ucfirst($cat)][] = [
                'defect' => $defectName,
                'qty' => $quantities[$i] ?? '',
            ];
        }
    }
}

return view('report.edit', [
    'report' => $report,
    'lines' => $lines,
    'skus' => $skus,
    'maintenances' => $maintenances,
    'defects' => $defects,
    'lineOptions' => $lineOptions,
    'materialsOptions' => $materialsOptions,
    'issues' => $issues,
    'qcRejects' => $qcRejects,
]);
}

public function update(Request $request, ProductionReport $report)
{
    $validated = $request->validate([
        'production_date' => 'required|date',
        'shift' => 'required|string',
        'line' => 'required|integer|exists:lines,line_number',
        'ac1' => 'nullable|integer',
        'ac2' => 'nullable|integer',
        'ac3' => 'nullable|integer',
        'ac4' => 'nullable|integer',
        'manpower_present' => 'nullable|integer',
        'manpower_absent' => 'nullable|integer',
        'sku' => 'nullable|string|exists:standard,description',
        'fbo_fco' => 'nullable|string',
        'lbo_lco' => 'nullable|string',
        'total_outputCase' => 'nullable|integer',
        'filler_speed' => 'nullable|integer',
        'opp_labeler_speed' => 'nullable|integer',
        'opp_labels' => 'nullable|integer',
        'shrinkfilm' => 'nullable|integer',
        'caps_filling' => 'nullable|integer',
        'bottle_filling' => 'nullable|integer',
        'blow_molding_output' => 'nullable|integer',
        'speed_blow_molding' => 'nullable|integer',
        'preform_blow_molding' => 'nullable|integer',
        'bottles_blow_molding' => 'nullable|integer',
        'qa_remarks' => 'nullable|string',
        'with_label' => 'nullable|integer',
        'without_label' => 'nullable|integer',
    ]);

    // Bottle code re-generation
    $productionDate = Carbon::parse($validated['production_date']);
    $expiryDate = $productionDate->copy()->addYear()->format('d F Y');
    $time = '00:01';
    $lineCode = str_pad($validated['line'], 2, '0', STR_PAD_LEFT);
    $bottleCode = "EXP {$expiryDate}\n{$time} {$lineCode}";

    $report->update([
        ...$validated,
        'bottle_code' => $bottleCode,
    ]);

    // 🔁 Re-sync production issues
    $report->issues()->delete();
    $totalMinutes = 0;
    if ($request->has(['materials', 'description', 'minutes'])) {
        foreach ($request->materials as $index => $materialName) {
            $maintenance = Maintenance::where('name', $materialName)->first();
            if ($maintenance) {
                $minutes = intval($request->minutes[$index] ?? 0);
                $totalMinutes += $minutes;
                ProductionIssues::create([
                    'production_reports_id' => $report->id,
                    'maintenances_id' => $maintenance->id,
                    'remarks' => $request->description[$index] ?? null,
                    'minutes' => $minutes,
                ]);
            }
        }
    }
    $report->update(['total_downtime' => $totalMinutes]);

    // 🔁 Re-sync Line QC Rejects
    $report->lineQcRejects()->delete();
    foreach (['caps', 'bottle', 'label', 'carton'] as $category) {
        $defectKeys = $request->input("qc_{$category}_defect", []);
        $qtyKeys = $request->input("qc_{$category}_qty", []);
        foreach ($defectKeys as $index => $defectName) {
            if ($defectName) {
                $defect = Defect::where('defect_name', $defectName)
                    ->where('category', ucfirst($category))
                    ->first();
                if ($defect) {
                    LineQcReject::create([
                        'production_reports_id' => $report->id,
                        'defects_id' => $defect->id,
                        'quantity' => $qtyKeys[$index] ?? 0,
                    ]);
                }
            }
        }
    }

    return redirect()->route('report.index')->with('success', 'Production report updated successfully.');
}

    public function destroy(ProductionReport $report)
    {
        $report->delete();

        return redirect()->route('report.index')->with('success', 'Defect deleted successfully.');
    }
}
