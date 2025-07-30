<?php

namespace App\Http\Controllers;

use App\Models\ProductionReport;
use Illuminate\Http\Request;
use App\Models\Standard;

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
              ->orWhere('bottles_per_case', 'like', "%{$search}%")
              ->orWhere('mat_no', 'like', "%{$search}%")
              ->orWhere('group', 'like', "%{$search}%")
              ->orWhere('preform_weight', 'like', "%{$search}%")
              ->orWhere('ldpe_size', 'like', "%{$search}%");
        });
    }

    // 📌 Sorting
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');

    if (in_array($sort, [
        'description','size','bottles_per_case',
        'mat_no','group','preform_weight','ldpe_size'
    ])) {
        $query->orderBy($sort, $direction);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $standards = $query->paginate(20);

    return view('configuration.standard.index', [
        'standards' => $standards,
        'currentSort' => $sort,
        'currentDirection' => $direction
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
        // Validate request data
        $validated = $request->validate([
            'group' => 'required|string',
            'mat_no' => 'nullable|string',
            'size' => 'required|string',
            'description' => 'required|string',
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

        $standard = Standard::create($validated);

        return view('configuration.standard.view', compact('standard'))
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
        // Validate request data
        $validated = $request->validate([
            'group' => 'required|string',
            'mat_no' => 'nullable|string',
            'size' => 'required|string',
            'description' => 'required|string',
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

        return redirect()
            ->route('configuration.standard.view', $standard)
            ->with('success', 'Standard updated successfully!');
    }

    /**
     * Remove the specified standard from storage.
     * Prevent deletion if the standard is used in Production Reports.
     */
    public function destroy(Standard $standard)
    {
        // Check if standard is used in Production Reports
        $isUsed = ProductionReport::where('sku', $standard->description)->exists();

        if ($isUsed) {
            return redirect()
                ->route('configuration.standard.view', $standard->id)
                ->withErrors([
                    'standard_delete' => "SKU \"{$standard->description}\" is used in Production Reports and cannot be deleted."
                ]);
        }

        $standard->delete();

        return redirect()
            ->route('configuration.standard.index')
            ->with('success', 'Standard deleted successfully.');
    }
}
