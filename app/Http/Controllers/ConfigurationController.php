<?php

namespace App\Http\Controllers;

use App\Models\ProductionIssues;
use App\Models\ProductionReport;
use Illuminate\Http\Request;
use App\Models\Defect;
use App\Models\Maintenance;
use App\Models\Line;
use App\Models\LineQcReject;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

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

        // 🔍 Global search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('defect_name', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🔍 Column-specific searches
        if ($request->filled('defect_name_search')) {
            $query->where('defect_name', 'like', "%{$request->defect_name_search}%");
        }

        if ($request->filled('category_search')) {
            $query->where('category', 'like', "%{$request->category_search}%");
        }

        if ($request->filled('description_search')) {
            $query->where('description', 'like', "%{$request->description_search}%");
        }

        // 📌 Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc'));

        $allowedSorts = ['defect_name', 'category', 'description', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        // ✅ Apply sorting BEFORE pagination
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage  = $request->get('per_page', 25);
        $defects  = $query->paginate($perPage)->appends($request->query());
        $totalDefects = Defect::count();

        return view('configuration.defect.index', [
            'defects' => $defects,
            'totalDefects' => $totalDefects,
            'currentSort' => $sort,
            'currentDirection' => $direction
        ]);
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
        $validator = Validator::make($request->all(), [
            'defect_name' => [
                'required',
                'string',
                Rule::unique('defects', 'defect_name')->whereNull('deleted_at'),
            ],
            'category' => 'required|in:Caps,Bottle,Label,LDPE Shrinkfilm',
            'description' => 'required|string',
        ]);

        // ✅ Return error to modal like maintenance/line
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        // Check for soft-deleted record and restore if found
        $trashedDefect = Defect::withTrashed()
            ->where('defect_name', $validated['defect_name'])
            ->first();

        if ($trashedDefect && $trashedDefect->trashed()) {
            $trashedDefect->restore();
            $trashedDefect->update($validated);

            return redirect()
                ->route('configuration.defect.view', $trashedDefect)
                ->with('success', "Defect '{$trashedDefect->defect_name}' restored successfully!");
        }

        // Create new defect
        $defect = Defect::create($validated);

        Notification::defectEvent('added', $defect);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'defect_add',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'defect'  => $defect->defect_name ?? 'Unknown Defect',
            ],
        ]);

        return redirect()
            ->route('configuration.defect.view', $defect)
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
        $validator = Validator::make($request->all(), [
            'defect_name' => [
                'required',
                'string',
                Rule::unique('defects', 'defect_name')->ignore($defect->id),
            ],
            'category' => 'required|in:Caps,Bottle,Label,LDPE Shrinkfilm',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        $defect->update($validator->validated());

        Notification::defectEvent('updated', $defect);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'defect_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'defect'  => $defect->defect_name ?? 'Unknown Defect',
            ],
        ]);

        return redirect()->route('configuration.defect.view', $defect)
                        ->with('success', 'Defect updated successfully!');
    }


    /**
     * Delete a defect if not used in QC Rejects.
     */
    public function defect_destroy(Defect $defect)
    {
        $defect->delete(); // soft delete
        Notification::defectEvent('deleted', $defect);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'defect_delete',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'defect'  => $defect->defect_name ?? 'Unknown Defect',
            ],
        ]);

        return redirect()
            ->route('configuration.defect.index') // go back to index page
            ->with('success', "Defect '{$defect->defect_name}' deleted successfully.");
    }

    // ===================== Maintenance =====================

    /**
     * List maintenances with search, sort, and optional trashed filter.
     */
    public function maintenance(Request $request)
    {
        $query = Maintenance::query();

        // Include trashed if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->boolean('only_trashed')) {
            $query->onlyTrashed();
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('maintenances', 'name')->whereNull('deleted_at'),
            ],
            'type' => 'required|in:EPL,OPL',
            '_context' => 'required|in:add',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        // 🔎 Check if soft-deleted record already exists
        $existing = Maintenance::onlyTrashed()->where('name', $request->name)->first();

        if ($existing) {
            // Restore instead of inserting new
            $existing->restore();
            $existing->update(['type' => $request->type]);

            Notification::maintenanceEvent('restored', $existing->name);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'maintenance_restore',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'maintenance'  => $existing->name ?? 'Unknown Maintenance',
                ],
            ]);

            return redirect()->back()->with('success', 'Maintenance restored successfully.');
        }

        // If no existing trashed record, create new
        $maintenance = Maintenance::create($validator->validated());
        Notification::maintenanceEvent('added', $maintenance->name);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'maintenance_store',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'maintenance'  => $maintenance->name ?? 'Unknown Maintenance',
            ],
        ]);

        return redirect()->back()->with('success', 'Maintenance added successfully.');
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
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        $maintenance->update($validator->validated());
        Notification::maintenanceEvent('updated', $maintenance->name);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'maintenance_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'maintenance'  => $maintenance->name ?? 'Unknown Maintenance',
            ],
        ]);

        return redirect()->back()->with('success', 'Maintenance updated successfully.');
    }

    /**
     * Soft delete a maintenance record (always allowed).
     */
    public function maintenance_destroy(Maintenance $maintenance)
    {
        // Instead of blocking, just soft delete
        $maintenance->delete();

        Notification::maintenanceEvent('deleted', $maintenance->name);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'maintenance_destroy',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'maintenance'  => $maintenance->name ?? 'Unknown Maintenance',
            ],
        ]);

        return redirect()->back()->with('success', "Maintenance '{$maintenance->name}' record deleted successfully.");
    }

    /**
     * Restore a soft-deleted maintenance record.
     */
    public function maintenance_restore($id)
    {
        $maintenance = Maintenance::withTrashed()->findOrFail($id);
        $maintenance->restore();

        Notification::maintenanceEvent('restored', $maintenance->name);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'maintenance_restore',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'maintenance'  => $maintenance->name ?? 'Unknown Maintenance',
            ],
        ]);

        return redirect()->route('configuration.maintenance.index')->with('success', 'Maintenance restored successfully.');
    }

    /**
     * Permanently delete a maintenance record.
     */
    public function maintenance_forceDelete($id)
    {
        $maintenance = Maintenance::withTrashed()->findOrFail($id);

        // ⚠️ Only allow if not used in issues
        $isUsed = ProductionIssues::where('maintenances_id', $maintenance->id)->exists();
        if ($isUsed) {
            return redirect()->route('configuration.maintenance.index')
                ->with('error', "\"{$maintenance->name}\" is currently in use and cannot be permanently deleted.");
        }

        $maintenance->forceDelete();

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'maintenance_force_delete',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'maintenance'  => $maintenance->name ?? 'Unknown Maintenance',
            ],
        ]);

        return redirect()->route('configuration.maintenance.index')->with('success', 'Maintenance permanently deleted.');
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
        // do not use validate() directly, because it throws a ValidationException
        $validator = Validator::make($request->all(), [
            'line_number' => 'required|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            // ✅ exactly like maintenance: send first error to session('error')
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        // Check for existing soft-deleted line
        $validated = $validator->validated();
        $trashedLine = Line::withTrashed()
            ->where('line_number', $validated['line_number'])
            ->first();

        if ($trashedLine) {
            if ($trashedLine->trashed()) {
                $trashedLine->restore();
                $trashedLine->update(['status' => $validated['status']]);
                return redirect()->back()->with('success', "Line saved successfully!");
            } else {
                // ✅ send error message to modal too
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The line number has already been taken.');
            }
        }

        $line = Line::create($validated);
        Notification::lineEvent('added', $line->line_number);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'line_store',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'line'  => $line->line_number ?? 'Unknown Line',
            ],
        ]);

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
        Notification::lineEvent('updated', $line->line_number);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'line_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'line'  => $line->line_number ?? 'Unknown Line',
            ],
        ]);

        return redirect()->back()->with('success', 'Line updated successfully!');
    }

    /**
     * Delete a line if not used in production reports.
     */
    public function line_destroy($line_number)
    {
        $line = Line::findOrFail($line_number);

        $line->delete();
        Notification::lineEvent('deleted', $line->line_number);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'line_destroy',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'line'  => $line->line_number ?? 'Unknown Line',
            ],
        ]);
        
        return redirect()->back()->with('success', "Line {$line_number} deleted successfully.");
    }
}