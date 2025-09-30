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
use App\Models\Notification;
use App\Models\AuditLog;

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

        // 🔍 Filters
        if ($request->filled('production_date_search')) {
            $search = $request->production_date_search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("DATE_FORMAT(production_date, '%M %d, %Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(production_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('sku_search')) {
            $skuSearch = $request->sku_search;
            $query->whereHas('standard', function ($q) use ($skuSearch) {
                $q->where('description', 'like', "%{$skuSearch}%");
            });
        }

        if ($request->filled('line_search')) {
            $query->where('line', 'like', "%{$request->line_search}%");
        }

        if ($request->filled('output_search')) {
            $query->where('total_outputCase', 'like', "%{$request->output_search}%");
        }

        if ($request->filled('submitted_date_search')) {
            $search = $request->submitted_date_search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("DATE_FORMAT(created_at, '%M %d, %Y %h:%i %p') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('status_search')) {
            $status = $request->status_search;
            $query->whereHas('statuses', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // ✅ Sorting logic
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc'));

        $allowedSorts = ['production_date', 'sku', 'line', 'total_outputCase', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        if (!in_array($direction, $allowedDirections)) {
            $direction = 'desc';
        }

        if ($sort === 'sku') {
            $query->orderBy(
                Standard::select('description')
                    ->whereColumn('standards.id', 'production_reports.sku_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        // ✅ Pagination
        $perPage = $request->get('per_page', 25);
        $reports = $query->paginate($perPage)->appends($request->query());
        $totalReports = ProductionReport::count();

        return view('report.index', [
            'reports'          => $reports,
            'totalReports'     => $totalReports,
            'currentSort'      => $sort,
            'currentDirection' => $direction,
        ]);
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
    if ($request->input('mode') === 'no_report') {
        $validated = $request->validate([
            'production_date' => 'required|date',
            'line'            => 'required|integer|exists:lines,line_number',
        ]);

        $lineCode = str_pad($validated['line'], 2, '0', STR_PAD_LEFT);
        $shift    = '00:00H - 24:00H'; // <— force default here
        $sku    = 'No Run';

        // build payload WITHOUT reading shift from the request
        $payload = [
            'shift'                 => $shift,
            'sku_id' => null,
            'fbo_fco'               => null,
            'lbo_lco'               => null,
            'total_outputCase'      => 0,
            'filler_speed'          => 0,
            'opp_labeler_speed'     => 0,
            'opp_labels'            => 0,
            'shrinkfilm'            => 0,
            'caps_filling'          => 0,
            'bottle_filling'        => 0,
            'blow_molding_output'   => 0,
            'speed_blow_molding'    => 0,
            'preform_blow_molding'  => 0,
            'bottles_blow_molding'  => 0,
            'qa_remarks'            => 'No Report',
            'with_label'            => 0,
            'without_label'         => 0,
            'total_sample'          => 0,
            'total_downtime'        => 0,
            'bottle_code'           => "NO RUN",
            'user_id'               => Auth::id(),
        ];

        // Upsert by (date, line)
        $report = ProductionReport::updateOrCreate(
            ['production_date' => $validated['production_date'], 'line' => $validated['line']],
            $payload
        );

        return redirect()->route('report.view', $report)
            ->with('success', 'No Report entry saved.');
    }
        $validated = $request->validate([
            'production_date' => 'required|date',
            'shift' => 'required|string',
            'line' => 'required|integer|exists:lines,line_number',
            'ac1' => 'nullable|integer',
            'ac2' => 'nullable|integer',
            'ac3' => 'nullable|integer',
            'ac4' => 'nullable|integer',
            'sku_id' => 'nullable|integer|exists:standards,id',
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
            ->where('sku_id', $validated['sku_id'])
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
$qcDefects = $request->input('qc_defect', []);
$qcQtys    = $request->input('qc_qty', []);

foreach ($qcDefects as $index => $defectId) {
    if ($defectId) {
        LineQcReject::create([
            'production_reports_id' => $report->id,
            'defects_id'            => $defectId,
            'quantity'              => intval($qcQtys[$index] ?? 0),
        ]);
    }
}


        // Insert Status entry
        Status::create([
            'user_id' => Auth::id(),
            'production_report_id' => $report->id,
            'status' => 'Submitted',
        ]);
        
        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'report_create',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'sku'  => $report->standard->description ?? 'Unknown SKU',
                'line' => "Line {$report->line}",
            ],
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
            'standard', // <-- relationship
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
            'reports'     => $report,
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

// In edit()
$qcRejectsFlat = $report->lineQcRejects->map(fn($reject) => [
    'defect'   => $reject->defect->defect_name,
    'category' => $reject->defect->category,
    'qty'      => $reject->quantity,
])->toArray();


return view('report.edit', [
    'report' => $report,
    'lines' => $lines,
    'skus' => $skus,
    'maintenances' => $maintenances,
    'defects' => $defects,
    'lineOptions' => $lineOptions,
    'materialsOptions' => $materialsOptions,
    'issues' => $issues,
    'qcRejectsFlat' => $qcRejectsFlat,
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

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'report_validate',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'sku'  => $report->standard->description ?? 'Unknown SKU',
                    'line' => "Line {$report->line}",
                ],
            ]);
        }
        
        return redirect()->route('report.view', $id)
            ->with('success', 'Report successfully validated.');
    }


    /**
     * Update the specified production report in storage.
     */
    public function update(Request $request, ProductionReport $report)
    {
        $validated = $request->validate([
            'production_date'      => 'required|date',
            'shift'                => 'required|string',
            'line'                 => 'required|integer|exists:lines,line_number',

            // remove the earlier integer rule to avoid duplicates
            // 'line_efficiency'    => 'nullable|integer',
            'line_efficiency'      => 'nullable|numeric|min:0|max:100', // keep this ✅

            'ac1'                  => 'nullable|integer',
            'ac2'                  => 'nullable|integer',
            'ac3'                  => 'nullable|integer',
            'ac4'                  => 'nullable|integer',
            'sku_id' => 'nullable|integer|exists:standards,id',
            'fbo_fco'              => 'nullable|string',
            'lbo_lco'              => 'nullable|string',
            'total_outputCase'     => 'nullable|integer',
            'filler_speed'         => 'nullable|integer',
            'opp_labeler_speed'    => 'nullable|integer',
            'opp_labels'           => 'nullable|integer',
            'shrinkfilm'           => 'nullable|integer',
            'caps_filling'         => 'nullable|integer',
            'bottle_filling'       => 'nullable|integer',
            'blow_molding_output'  => 'nullable|integer',
            'speed_blow_molding'   => 'nullable|integer',
            'preform_blow_molding' => 'nullable|integer',
            'bottles_blow_molding' => 'nullable|integer',
            'qa_remarks'           => 'nullable|string',
            'with_label'           => 'nullable|integer',
            'without_label'        => 'nullable|integer',
            // 'total_sample'       => 'nullable|integer', // we'll compute this ✅
        ]);

        // ✅ Recompute total_sample from with_label + without_label
        $with    = (int) ($request->input('with_label')    ?? $report->with_label    ?? 0);
        $without = (int) ($request->input('without_label') ?? $report->without_label ?? 0);
        $validated['total_sample'] = $with + $without;

        // Bottle code generation (unchanged)
        $productionDate = \Carbon\Carbon::parse($validated['production_date']);
        $expiryDate     = $productionDate->copy()->addYear()->format('d F Y');
        $time           = '00:01';
        $lineCode       = str_pad($validated['line'], 2, '0', STR_PAD_LEFT);
        $bottleCode     = "EXP {$expiryDate}\n{$time} {$lineCode}";

        // Capture old field, issues, and rejects
        $originalFields = $report->only(array_keys($validated)); // includes total_sample now ✅
        $originalIssues = $report->issues->map(fn($i) => [
            'maintenance' => $i->maintenance->name ?? '',
            'remarks'     => $i->remarks,
            'minutes'     => $i->minutes,
        ])->toArray();
        $originalQcRejects = $report->lineQcRejects->map(fn($r) => [
            'category' => $r->defect->category ?? '',
            'defect'   => $r->defect->defect_name ?? '',
            'quantity' => $r->quantity,
        ])->toArray();

        // Update main report fields (includes recomputed total_sample) ✅
        $report->update([
            ...$validated,
            'bottle_code' => $bottleCode,
        ]);

        // Re-sync production issues (unchanged)
        $report->issues()->delete();
        $newIssues = [];
        $totalMinutes = 0;
        if ($request->has(['materials', 'description', 'minutes'])) {
            foreach ($request->materials as $index => $materialName) {
                $maintenance = \App\Models\Maintenance::where('name', $materialName)->first();
                if ($maintenance) {
                    $minutes = (int) ($request->minutes[$index] ?? 0);
                    $totalMinutes += $minutes;
                    $newIssues[] = [
                        'maintenance' => $maintenance->name,
                        'remarks'     => $request->description[$index] ?? null,
                        'minutes'     => $minutes,
                    ];
                    \App\Models\ProductionIssues::create([
                        'production_reports_id' => $report->id,
                        'maintenances_id'       => $maintenance->id,
                        'remarks'               => $request->description[$index] ?? null,
                        'minutes'               => $minutes,
                    ]);
                }
            }
        }
        $report->update(['total_downtime' => $totalMinutes]);

       // --- Re-sync Line QC Rejects ---
$report->lineQcRejects()->delete();
$newQcRejects = [];

$qcDefects   = $request->input('qc_defect', []);
$qcCategories = $request->input('qc_category', []);
$qcQtys      = $request->input('qc_qty', []);

foreach ($qcDefects as $index => $defectName) {
    if (!$defectName) continue;

    $category = $qcCategories[$index] ?? null;
    $qty      = (int) ($qcQtys[$index] ?? 0);

    // Find matching defect by name (and optionally category)
    $defect = \App\Models\Defect::where('defect_name', $defectName)
                ->when($category, fn($q) => $q->where('category', $category))
                ->first();

    if ($defect) {
        \App\Models\LineQcReject::create([
            'production_reports_id' => $report->id,
            'defects_id'            => $defect->id,
            'quantity'              => $qty,
        ]);

        $newQcRejects[] = [
            'category' => $category,
            'defect'   => $defectName,
            'quantity' => $qty,
        ];
    }
}


        // Compare for change history logging
        $newFields = $report->fresh()->only(array_keys($validated)); // includes new total_sample ✅

        $hasChanges =
            $originalFields !== $newFields ||
            $originalIssues !== $newIssues ||
            $originalQcRejects !== $newQcRejects;

        if ($hasChanges) {
            \App\Models\ProductionReportHistory::create([
                'production_report_id' => $report->id,
                'old_data' => [
                    'fields'     => $originalFields,
                    'issues'     => $originalIssues,
                    'qc_rejects' => $originalQcRejects,
                ],
                'new_data' => [
                    'fields'     => $newFields,
                    'issues'     => $newIssues,
                    'qc_rejects' => $newQcRejects,
                ],
                'updated_by' => \Auth::id(),
                'updated_at' => now(),
            ]);
        }

        $user = Auth::user();
        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;

        $skuTag  = "<span style=\"color:#23527c;font-weight:bold;\">{$report->standard->description}</span>";
        $lineTag = "<span style=\"color:#23527c;font-weight:bold;\">Line {$report->line}</span>";
        $userTag = "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>";

        Notification::create([
            'user_id'              => null,
            'type'                 => 'report_edit',
            'production_report_id' => $report->id,
            'message'              => "{$skuTag} | {$lineTag} was edited by {$userTag}.",
            'is_read'              => false,
            'required_permission'  => 'report.edit',
            'url'                  => route('report.view', $report->id), // 👈 here too
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'report_edit',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'sku'  => $report->standard->description ?? 'Unknown SKU',
                'line' => "Line {$report->line}",
            ],
        ]);

        return redirect()
            ->route('report.view', $report->id)
            ->with('success', 'Production report updated successfully.');
    }

    /**
     * Generate and stream a PDF of the specified production report.
     */
    public function viewPDF(ProductionReport $report)
    {
        $report->load(['line', 'standard', 'issues.maintenance', 'lineQcRejects.defect']);

        $pdf = Pdf::loadView('pdf.production_report', compact('report'));

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'report_pdf',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                // Format the production_date to "September 28, 2025"
                'report'  =>  ' Daily Production Report' . '-' . $report->production_date->format('F j, Y')
            ],
        ]);

        return $pdf->stream('Production - ' . $report->production_date->format('F j, Y') . '.pdf');
    }
}
