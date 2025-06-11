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

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('production_date', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('line', 'like', "%{$search}%")
                    ->orWhere('total_outputCase', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $reports = $query->paginate(10)->appends($request->query());

        return view('report.index', compact('reports'));
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
            'manpower_present' => 'nullable|integer',
            'manpower_absent' => 'nullable|integer',
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

        // Insert Status entry
        Status::create([
            'user_id' => Auth::id(),
            'production_report_id' => $report->id,
            'status' => 'Submitted',
        ]);

        return redirect()->route('report.index')->with('success', 'Production report saved successfully.');
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

        $isValidated = Status::where('production_report_id', $report->id)
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
        $lines = Line::orderBy('line_number')->get();
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
            'manpower_present' => 'nullable|integer',
            'manpower_absent' => 'nullable|integer',
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

        // Re-sync production issues
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

        // Re-sync Line QC Rejects
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

        // Log status as Edited
        Status::create([
            'user_id' => Auth::id(),
            'production_report_id' => $report->id,
            'status' => 'Edited',
        ]);

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
