<?php

namespace App\Http\Controllers;

use App\Models\ProductionReport;
use Illuminate\Http\Request;
use App\Models\Defect;
use App\Models\Maintenance;
use App\Models\Line;
use App\Models\LineQcReject;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    /**
     * Show configuration index page.
     */
    public function index()
    {
        $lines = Line::orderBy('line_number', 'asc')->get();
        $defects = Defect::latest()->take(5)->get();
        $maintenances = Maintenance::latest()->take(5)->get();
        return view('configuration.index', compact('lines', 'defects', 'maintenances'));
    }

    // ===================== Defect =====================

    /**
     * List defects with search and sort.
     */
    public function defect(Request $request)
    {
        $query = Defect::query();

        // Search filter
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('defect_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['defect_name', 'category', 'description', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $defects = $query->orderBy($sort, $direction)->paginate(10);

        return view('configuration.defect.index', compact('defects'));
    }

    /**
     * View a single defect.
     */
    public function view_defect(Defect $defect)
    {
        return view('configuration.defect.view', compact('defect'));
    }

    /**
     * Show add defect form.
     */
    public function add_defect()
    {
        return view('configuration.defect.add');
    }

    /**
     * Store a new defect.
     */
    public function defect_store(Request $request)
    {
        $validated = $request->validate([
            'defect_name' => 'required|string|unique:defects,defect_name',
            'category' => 'required|in:Caps,Bottle,Label,Carton',
            'description' => 'nullable|string',
        ]);

        $defect = Defect::create($validated);

        return redirect()->route('configuration.defect.view', $defect)
                         ->with('success', 'Defect added successfully!');
    }

    /**
     * Show edit defect form.
     */
    public function defect_edit(Defect $defect)
    {
        return view('configuration.defect.edit', compact('defect'));
    }

    /**
     * Update a defect.
     */
    public function defect_update(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'defect_name' => [
                'required',
                'string',
                Rule::unique('defects', 'defect_name')->ignore($defect->id),
            ],
            'category' => 'required|in:Caps,Bottle,Label,Carton',
            'description' => 'nullable|string',
        ]);

        $defect->update($validated);

        return redirect()->route('configuration.defect.view', $defect)
                         ->with('success', 'Defect updated successfully!');
    }

    /**
     * Delete a defect if not used in QC Rejects.
     */
    public function defect_destroy(Defect $defect)
    {
        $isUsed = LineQcReject::where('sku', $defect->id)->exists();

        if ($isUsed) {
            return redirect()->back()->withErrors([
                'defect_delete' => "Defect \"{$defect->defect_name}\" is used in QC Rejects and cannot be deleted."
            ]);
        }

        $defect->delete();

        return redirect()->route('configuration.defect.index')->with('success', 'Defect deleted successfully.');
    }

    // ===================== Maintenance =====================

    /**
     * List maintenances with search and sort.
     */
    public function maintenance(Request $request)
    {
        $query = Maintenance::query();

        // Search filter
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('production_date', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Pagination
        $maintenances = $query->paginate(10)->appends($request->query());

        return view('configuration.maintenance.index', compact('maintenances'));
    }

    /**
     * Store a new maintenance record.
     */
    public function maintenance_store(Request $request)
    {
        $request->merge(['_context' => 'add']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:maintenances,name',
            'type' => 'required|in:EPL,OPL',
            '_context' => 'required|in:add',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator, 'addForm')
                ->withInput()
                ->with('error_source', 'add');
        }

        Maintenance::create($validator->validated());

        return redirect()->back()->with('success', 'Maintenance added successfully.');
    }

    /**
     * Show edit maintenance form.
     */
    public function maintenance_edit(Maintenance $maintenance)
    {
        return view('configuration.maintenance.edit', compact('maintenance'));
    }

    /**
     * Update a maintenance record.
     */
    public function maintenance_update(Request $request, Maintenance $maintenance)
    {
        $request->merge(['_context' => 'edit']);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('maintenances', 'name')->ignore($maintenance->id),
            ],
            'type' => 'required|in:EPL,OPL',
            '_context' => 'required|in:edit',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_source', 'edit');
        }

        $maintenance->update($validator->validated());

        return redirect()->back()->with('success', 'Maintenance updated successfully.');
    }

    /**
     * Delete a maintenance record if not used.
     */
    public function maintenance_destroy(Maintenance $maintenance)
    {
        $isUsed = LineQcReject::where('id', $maintenance->id)->exists();

        if ($isUsed) {
            return redirect()->route('configuration.maintenance.index')->withErrors([
                'maintenance_delete' => "\"{$maintenance->name}\" is currently in use and cannot be deleted."
            ]);
        }

        $maintenance->delete();

        return redirect()->route('configuration.maintenance.index')->with('success', 'Maintenance record deleted successfully.');
    }

    // ===================== Line =====================

    /**
     * List lines with optional search.
     */
    public function line(Request $request)
    {
        $lines = Line::query()
            ->when($request->search, fn($q) =>
                $q->where('line_number', 'like', '%' . $request->search . '%')
            )
            ->orderBy('line_number', 'asc')
            ->get();

        return view('configuration.line.index', compact('lines'));
    }

    /**
     * Store a new line or restore if soft-deleted.
     */
    public function line_store(Request $request)
    {
        $validated = $request->validate([
            'line_number' => 'required|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Check for existing soft-deleted line
        $trashedLine = Line::withTrashed()
            ->where('line_number', $validated['line_number'])
            ->first();

        if ($trashedLine) {
            if ($trashedLine->trashed()) {
                $trashedLine->restore();
                $trashedLine->update(['status' => $validated['status']]);
                return redirect()->back()->with('success', "Line {$validated['line_number']} was restored.");
            } else {
                return redirect()->back()
                    ->withErrors(['line_number' => 'The line number has already been taken.'])
                    ->withInput();
            }
        }

        Line::create($validated);

        return redirect()->back()->with('success', 'Line saved successfully!');
    }

    /**
     * Update a line's status.
     */
    public function line_update(Request $request, $line_number)
    {
        $line = Line::findOrFail($line_number);

        $validated = $request->validate([
            'status' => 'required|in:Active,Inactive',
        ]);

        $line->update($validated);

        return redirect()->back()->with('success', 'Line updated successfully!');
    }

    /**
     * Delete a line if not used in production reports.
     */
    public function line_destroy($line_number)
    {
        $line = Line::findOrFail($line_number);

        $isUsed = ProductionReport::where('line', $line_number)->exists();

        if ($isUsed) {
            return redirect()->back()->withErrors([
                'line_delete' => "Line {$line_number} is in use in production reports and cannot be deleted."
            ]);
        }

        $line->delete();

        return redirect()->back()->with('success', "Line {$line_number} deleted successfully.");
    }
}