<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suburb;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuburbsImport;

class SuburbController extends Controller
{
    public function index()
    {
        $suburbs = Suburb::orderBy('name')->paginate(20);
        
        return view('admin.suburbs.index', compact('suburbs'));
    }
    
    public function create()
    {
        return view('admin.suburbs.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'active' => 'boolean',
        ]);
        
        Suburb::create($validated);
        
        return redirect()->route('admin.suburbs.index')->with('success', 'Suburb created successfully!');
    }
    
    public function edit(Suburb $suburb)
    {
        return view('admin.suburbs.edit', compact('suburb'));
    }
    
    public function update(Request $request, Suburb $suburb)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'active' => 'boolean',
        ]);
        
        $suburb->update($validated);
        
        return redirect()->route('admin.suburbs.index')->with('success', 'Suburb updated successfully!');
    }
    
    public function destroy(Suburb $suburb)
    {
        $suburb->delete();
        
        return redirect()->route('admin.suburbs.index')->with('success', 'Suburb deleted successfully!');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);
        
        Excel::import(new SuburbsImport, $request->file('file'));
        
        return redirect()->route('admin.suburbs.index')->with('success', 'Suburbs imported successfully!');
    }
}
