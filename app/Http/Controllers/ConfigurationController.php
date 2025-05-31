<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Defect;
use App\Models\Maintenance;
use App\Models\Line;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    public function index()
    {   
        $lines = Line::orderBy('line_number', 'asc')->get();
        $defects = Defect::latest()->take(5)->get();
        $maintenances = Maintenance::latest()->take(5)->get();
        return view('metrics.configuration', compact('lines','defects','maintenances'));
    }
    //Defect
    public function defect(Request $request)
    {   
        $query = Defect::query();
        
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('defect_name', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $defects = $query->orderBy('created_at', 'desc')->paginate(perPage: 10);

        return view('metrics.defect.index', compact('defects'));
    }
    public function view_defect(Defect $defect) 
    {
        return view('metrics.defect.view', compact('defect'));
    }
    public function add_defect()
    {
        return view('metrics.defect.add');
    }
    public function defect_store(Request $request)
    {
        $validated = $request->validate([
            'defect_name' => 'required|string',
            'category' => 'required|in:Caps,Bottle,Label,Carton',
            'description' => 'nullable|string',
        ]);

        // Store and assign the new defect
        $defect = Defect::create($validated);

        // Redirect to defect view
        return redirect()->route('metrics.defect.view', $defect)->with('success', 'Defect added successfully!');
    }
    public function defect_edit(Defect $defect)
    {
        return view('metrics.defect.edit', compact('defect'));
    }
    public function defect_update(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'defect_name' => 'required|string',
            'category' => 'required|in:Caps,Bottle,Label,Carton',
            'description' => 'nullable|string',
        ]);

        $defect->update($validated);

        return redirect()->route('metrics.defect.view', $defect)->with('success', 'Defect updated successfully!');

    }
    public function defect_destroy(Defect $defect)
    {
        $defect->delete();

        return redirect()->route('metrics.defect.index')->with('success', 'Defect deleted successfully.');
    }
    //Maintenance
    public function maintenance(Request $request)
    {
        $query = Maintenance::query();

        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // 👇 paginate() replaces get()
        $maintenances = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('metrics.maintenance.index', compact('maintenances'));
    }
    public function view_maintenance(Maintenance $maintenance) 
    {
        return view('metrics.maintenance.view', compact('maintenance'));
    }
    public function add_maintenance()
    {
        return view('metrics.maintenance.add');
    }
    public function maintenance_store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique('maintenances', 'name')],
            'type' => 'required|string|in:EPL,OPL',
        ], [
            'name.unique' => 'The machine name has already been taken.',
        ]);

        $maintenance = Maintenance::create($validated);

        return redirect()
            ->route('metrics.maintenance.view', $maintenance)
            ->with('success', 'Maintenance added successfully!');
    }
    public function maintenance_edit(Maintenance $maintenance)
    {
        return view('metrics.maintenance.edit', compact('maintenance'));
    }
    public function maintenance_update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique('maintenances', 'name')],
            'type' => 'required|string|in:EPL,OPL',
        ], [
            'name.unique' => 'The machine name has already been taken.',
        ]);

        $maintenance->update($validated);

        return redirect()->route('metrics.maintenance.view', $maintenance)->with('success', 'Maintenance record updated successfully!');
    }
    public function maintenance_destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('metrics.maintenance.index')->with('success', 'Maintenance record deleted successfully.');
    }
public function line_store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'line_number' => 'required|integer|unique:lines,line_number',
        'status' => 'required|in:Active,Inactive',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput()
            ->with('show_modal', 'new-line');
    }

    Line::create($validator->validated());

    return redirect()->back()->with('success', 'Line saved successfully!');
}
    public function line_update(Request $request, Line $line)
    {
        $validated = $request->validate([
            'status' => 'required|in:Active,Inactive',
        ]);

        $line->update($validated);

        return redirect()->back()
        ->with('success', 'Line updated successfully!');
    }
    public function line_destroy(Line $line)
    {
        $line->delete();

        return redirect()->route('metrics.configuration')->with('success', 'Line deleted successfully.');
    }
    public function formula()
    {
        return view('metrics.formula');
    }
}