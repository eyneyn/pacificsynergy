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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Status;

class ProductionReportController extends Controller
{
    /**
     * Display a listing of the production reports.
     */
public function index(Request $request)
{
    $query = ProductionReport::with([
        'line',
        'standard',
        'statuses' => function ($q) {
            $q->whereIn('status', ['Submitted', 'Reviewed', 'Validated'])
              ->orderByDesc('id');
        }
    ]);


    // Individual column searches
    if ($request->filled('production_date_search')) {
        $search = $request->production_date_search;
        $query->whereRaw("DATE_FORMAT(production_date, '%M %d, %Y') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("DATE_FORMAT(production_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
    }

    if ($request->filled('sku_search')) {
        $query->where('sku', 'like', "%{$request->sku_search}%");
    }

    if ($request->filled('line_search')) {
        $query->where('line', 'like', "%{$request->line_search}%");
    }

    if ($request->filled('output_search')) {
        $query->where('total_outputCase', 'like', "%{$request->output_search}%");
    }

    if ($request->filled('submitted_date_search')) {
        $search = $request->submitted_date_search;
        $query->whereRaw("DATE_FORMAT(created_at, '%M %d, %Y %h:%i %p') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
    }

    if ($request->filled('status_search')) {
        $status = $request->status_search;
        $query->whereHas('statuses', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    // Sorting
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');
    $query->orderBy($sort, $direction);

    $perPage = $request->get('per_page', 25); // default 25
    $reports = $query->paginate($perPage)->appends($request->query());

    $totalReports = ProductionReport::count();

    return view('report.index', compact('reports','totalReports'));
}


    /**
     * Show the form for creating a new production report.
     */
  public function add()
    {
        $lines = Line::where('status', 'Active')->orderBy('line_number')->get();
        $skus = Standard::orderBy('description')->get();
        $maintenances = Maintenance::orderBy('name')->orderBy('type')->get();
        $defects = Defect::all();

        // Prepare options for dropdowns
        $lineOptions = $lines->mapWithKeys(fn($line) => [$line->line_number => 'Line ' . $line->line_number]);
        $materialsOptions = $maintenances->pluck('name', 'name');

        return view('report.add', [
            'lines' => $lines,
            'skus' => $skus,
            'maintenances' => $maintenances,
            'defects' => $defects,
            'lineOptions' => $lineOptions,
            'materialsOptions' => $materialsOptions,
        ]);
    }

    /**
     * Store a newly created production report in storage.
     */
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
            'sku' => 'nullable|string|exists:standards,description',
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

        // Duplicate check: same date + sku + output
        $duplicate = ProductionReport::where('production_date', $validated['production_date'])
            ->where('sku', $validated['sku'])
            ->where('total_outputCase', $validated['total_outputCase'])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'duplicate' => 'Duplicate entry: This production report already exists.',
            ]);
        }

        // Generate bottle code
        $productionDate = Carbon::parse($validated['production_date']);
        $expiryDate = $productionDate->copy()->addYear()->format('d F Y');
        $time = '00:01';
        $lineCode = str_pad($validated['line'], 2, '0', STR_PAD_LEFT);
        $bottleCode = "EXP {$expiryDate}\n{$time} {$lineCode}";

        // Calculate total sample
        $totalSample = intval($validated['with_label'] ?? 0) + intval($validated['without_label'] ?? 0);

        // Create Production Report
        $report = ProductionReport::create([
            ...$validated,
            'bottle_code' => $bottleCode,
            'total_sample' => $totalSample,
            'user_id' => Auth::id(),
        ]);

        // Sum downtime and create issues
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
        foreach (['caps', 'bottle', 'label', 'ldpe_shrinkfilm'] as $category) {
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

        // Insert Status entry
        Status::create([
            'user_id' => Auth::id(),
            'production_report_id' => $report->id,
            'status' => 'Submitted',
        ]);

        return redirect()->route('report.view', $report)
            ->with('success', 'Production report saved successfully.');
    }

    /**
     * Display the specified production report.
     */
    public function view($id)
    {
        $report = ProductionReport::with([
            'issues.maintenance',
            'lineQcRejects.defect',
            'line',
            'standard',
            'statuses.user',
            'user',
        ])->findOrFail($id);

        $currentUserId = Auth::id();
        $reportOwnerId = $report->user_id;

        // Prevent the creator from inserting a "Reviewed" status
        if ($currentUserId !== $reportOwnerId) {
            $alreadyReviewed = Status::where('user_id', $currentUserId)
                ->where('production_report_id', $report->id)
                ->where('status', 'Reviewed')
                ->doesntExist();

            if ($alreadyReviewed) {
                Status::create([
                    'user_id' => $currentUserId,
                    'production_report_id' => $report->id,
                    'status' => 'Reviewed',
                ]);
            }
        }

        $isValidated = Status::where('production_report_id', $report->id,)
            ->where('status', 'Validated')
            ->exists();

        return view('report.view', [
            'reports' => $report,
            'isValidated' => $isValidated,
        ]);
    }

    /**
     * Show the form for editing the specified production report.
     */
    public function edit(ProductionReport $report)
    {
        $lines = Line::where('status', 'Active')->orderBy('line_number')->get();
        $skus = Standard::orderBy('description')->get();
        $maintenances = Maintenance::orderBy('name')->orderBy('type')->get();
        $defects = Defect::all();

        $lineOptions = $lines->mapWithKeys(fn($line) => [$line->line_number => 'Line ' . $line->line_number]);
        $materialsOptions = $maintenances->pluck('name', 'name');

        // Prepare issues array for Alpine
        $issues = $report->issues->map(function ($issue) {
            return [
                'material' => $issue->maintenance->name ?? '',
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
            'LDPE Shrinkfilm' => [],
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
            foreach (['caps', 'bottle', 'label', 'ldpe shrinkfilm'] as $cat) {
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

    /**
     * Validate the specified production report.
     */
    public function validateReport($id)
    {
        $report = ProductionReport::findOrFail($id);

        // Prevent duplicate 'Validated'
        $alreadyValidated = Status::where('user_id', Auth::id())
            ->where('production_report_id', $report->id)
            ->where('status', 'Validated')
            ->exists();

        if (! $alreadyValidated) {
            Status::create([
                'user_id' => Auth::id(),
                'production_report_id' => $report->id,
                'status' => 'Validated',
            ]);
        }

        return redirect()->route('report.view', $id)->with('success', 'Report successfully validated.');
    }

    /**
     * Update the specified production report in storage.
     */
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
        'sku' => 'nullable|string|exists:standards,description',
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
        'line_efficiency' => 'nullable|numeric|min:0|max:100'
    ]);

    // Bottle code generation
    $productionDate = Carbon::parse($validated['production_date']);
    $expiryDate = $productionDate->copy()->addYear()->format('d F Y');
    $time = '00:01';
    $lineCode = str_pad($validated['line'], 2, '0', STR_PAD_LEFT);
    $bottleCode = "EXP {$expiryDate}\n{$time} {$lineCode}";

    // Capture old field, issues, and rejects
    $originalFields = $report->only(array_keys($validated));
    $originalIssues = $report->issues->map(fn($i) => [
        'maintenance' => $i->maintenance->name ?? '',
        'remarks' => $i->remarks,
        'minutes' => $i->minutes,
    ])->toArray();
    $originalQcRejects = $report->lineQcRejects->map(fn($r) => [
        'category' => $r->defect->category ?? '',
        'defect' => $r->defect->defect_name ?? '',
        'quantity' => $r->quantity,
    ])->toArray();

    // Update main report fields
    $report->update([
        ...$validated,
        'bottle_code' => $bottleCode,
    ]);

    // Re-sync production issues
    $report->issues()->delete();
    $newIssues = [];
    $totalMinutes = 0;
    if ($request->has(['materials', 'description', 'minutes'])) {
        foreach ($request->materials as $index => $materialName) {
            $maintenance = Maintenance::where('name', $materialName)->first();
            if ($maintenance) {
                $minutes = intval($request->minutes[$index] ?? 0);
                $totalMinutes += $minutes;
                $newIssues[] = [
                    'maintenance' => $maintenance->name,
                    'remarks' => $request->description[$index] ?? null,
                    'minutes' => $minutes,
                ];
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

    // Re-sync Line QC Rejects
    $report->lineQcRejects()->delete();
    $newQcRejects = [];
$categoryMap = [
    'caps' => 'Caps',
    'bottle' => 'Bottle',
    'label' => 'Label',
    'ldpe_shrinkfilm' => 'LDPE Shrinkfilm', // underscore in key
];

foreach ($categoryMap as $categoryKey => $normalizedCategory) {
    $defectKeys = $request->input("qc_{$categoryKey}_defect", []);
    $qtyKeys = $request->input("qc_{$categoryKey}_qty", []);

    foreach ($defectKeys as $index => $defectName) {
        if ($defectName) {
            $defect = Defect::where('defect_name', $defectName)
                ->where('category', $normalizedCategory)
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


    // Compare for change history logging
    $newFields = $report->fresh()->only(array_keys($validated));

    $hasChanges =
        $originalFields !== $newFields ||
        $originalIssues !== $newIssues ||
        $originalQcRejects !== $newQcRejects;

    if ($hasChanges) {
        \App\Models\ProductionReportHistory::create([
            'production_report_id' => $report->id,
            'old_data' => [
                'fields' => $originalFields,
                'issues' => $originalIssues,
                'qc_rejects' => $originalQcRejects,
            ],
            'new_data' => [
                'fields' => $newFields,
                'issues' => $newIssues,
                'qc_rejects' => $newQcRejects,
            ],
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    return redirect()->route('report.view', $report->id)->with('success', 'Production report updated successfully.');
}


    /**
     * Generate and stream a PDF of the specified production report.
     */
    public function viewPDF(ProductionReport $report)
    {
        $report->load(['line', 'standard', 'issues.maintenance', 'lineQcRejects.defect']);
        $pdf = Pdf::loadView('pdf.production_report', compact('report'));

        return $pdf->stream('Production - ' . $report->production_date . '.pdf');
    }
}
