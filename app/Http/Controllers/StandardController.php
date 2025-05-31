<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standard;

class StandardController extends Controller
{
    public function index(Request $request)
    {
        $query = Standard::query();

        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhere('size', 'like', "%{$search}%")
                ->orWhere('mat_no', 'like', "%{$search}%")
                ->orWhere('group', 'like', "%{$search}%");
            });
        }

        $standards = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('metrics.standard.index', compact('standards'));
    }
    public function add()
    {
        return view('metrics.standard.add');
    }
    public function store(Request $request)
    {
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
        return view('metrics.standard.view', compact('standard'))->with('success', 'Standard saved successfully!');
    }
    public function view(Standard $standard) 
    {
        return view('metrics.standard.view', compact('standard'));
    }
    // Show the edit form
    public function editForm(Standard $standard)
    {
        return view('metrics.standard.edit', compact('standard'));
    }
    // Handle form submission
    public function edit(Request $request, Standard $standard)
    {
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

        return redirect()->route('metrics.standard.view', $standard)->with('success', 'Standard updated successfully!');
    }
    public function destroy(Standard $standard)
    {
        $standard->delete();

        return redirect()->route('metrics.standard.index')->with('success', 'Standard deleted successfully.');
    }

}
