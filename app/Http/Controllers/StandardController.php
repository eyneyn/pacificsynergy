<?php

namespace App\Http\Controllers;

use App\Models\ProductionReport;
use Illuminate\Http\Request;
use App\Models\Standard;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StandardController extends Controller
{
    /**
     * Display a listing of the standards, with optional search.
     */
    public function index(Request $request)
    {
        $query = Standard::query();

        // 🔍 Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhere('size', 'like', "%{$search}%")
                ->orWhere('bottles_per_case', 'like', "%{$search}%");
            });
        }

        // 📌 Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if (in_array($sort, [
            'description','size','bottles_per_case'
        ])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination (✅ move after sorting)
        $perPage  = $request->get('per_page', 25);
        $standards  = $query->paginate($perPage)->appends($request->query());
        $totalStandards = Standard::count();

        return view('configuration.standard.index', [
            'standards' => $standards,
            'currentSort' => $sort,
            'currentDirection' => $direction,
            'totalStandards' => $totalStandards
        ]);
    }

    /**
     * Show the form for creating a new standard.
     */
    public function add()
    {
        return view('configuration.standard.add');
    }

    /**
     * Store a newly created standard in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string',
            'mat_no' => 'nullable|string',
            'size' => 'required|string',
            'description' => [
                'required',
                'string',
                Rule::unique('standards', 'description')->whereNull('deleted_at'), // ✅ ignore soft deleted
            ],
            'bottles_per_case' => 'required|integer|min:0',
            'preform_weight' => 'required|string',
            'ldpe_size' => 'required|string',
            'cases_per_roll' => 'required|integer|min:0',
            'caps' => 'required|string',
            'opp_label' => 'required|string',
            'barcode_sticker' => 'required|string',
            'alt_preform_for_350ml' => 'required|numeric|min:0',
            'preform_weight2' => 'required|numeric|min:0',
        ]);

        // 🔎 Check if a soft-deleted standard with same description exists
        $existing = Standard::onlyTrashed()->where('description', $request->description)->first();

        if ($existing) {
            // Restore it instead of creating a new row
            $existing->restore();
            $existing->update($validated);

            Notification::standardEvent('restored', $existing);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'standard_restore',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'standard'  => $existing->description ?? 'Unknown Standard',
                ],
            ]);

            return redirect()
                ->route('configuration.standard.view', $existing)
                ->with('success', "Standard '{$existing->description}' restored successfully!");
        }

        // Normal create
        $standard = Standard::create($validated);
        Notification::standardEvent('added', $standard);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'standard_add',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'standard'  => $standard->description ?? 'Unknown Standard',
            ],
        ]);

        return redirect()
            ->route('configuration.standard.view', $standard)
            ->with('success', 'Standard saved successfully!');
    }

    /**
     * Display the specified standard.
     */
    public function view(Standard $standard)
    {
        return view('configuration.standard.view', compact('standard'));
    }

    /**
     * Show the form for editing the specified standard.
     */
    public function editForm(Standard $standard)
    {
        return view('configuration.standard.edit', compact('standard'));
    }

    /**
     * Update the specified standard in storage.
     */
    public function edit(Request $request, Standard $standard)
    {
        $validated = $request->validate([
            'group' => 'required|string',
            'mat_no' => 'nullable|string',
            'size' => 'required|string',
            'description' => [
                'required',
                'string',
                Rule::unique('standards', 'description')->ignore($standard->id), // ✅ fix
            ],
            'bottles_per_case' => 'required|integer|min:1',
            'preform_weight' => 'required|string',
            'ldpe_size' => 'required|string',
            'cases_per_roll' => 'required|integer|min:1',
            'caps' => 'required|string',
            'opp_label' => 'required|string',
            'barcode_sticker' => 'required|string',
            'alt_preform_for_350ml' => 'required|numeric|min:0',
            'preform_weight2' => 'required|numeric|min:0',
        ]);

        $standard->update($validated);
        Notification::standardEvent('updated', $standard);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'standard_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'standard'  => $standard->description ?? 'Unknown Standard',
            ],
        ]);

        return redirect()
            ->route('configuration.standard.view', $standard)
            ->with('success', "Standard '{$standard->description}' updated successfully!");
    }

    /**
     * Remove the specified standard from storage.
     * Prevent deletion if the standard is used in Production Reports.
     */
    public function destroy(Standard $standard)
    {
        $standard->delete();
        Notification::standardEvent('deleted', $standard);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'standard_delete',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'standard'  => $standard->description ?? 'Unknown Standard',
            ],
        ]);
        

        return redirect()
            ->route('configuration.standard.index')
            ->with('success', "Standard '{$standard->description}' deleted successfully.");
    }
}
