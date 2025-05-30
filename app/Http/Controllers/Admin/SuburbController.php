<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suburb;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuburbController extends Controller
{
    /**
     * Display a listing of suburbs.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $state = $request->input('state');
        
        $suburbs = Suburb::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('postcode', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('active', $status === 'active');
            })
            ->when($state, function ($query, $state) {
                return $query->where('state', $state);
            })
            ->orderBy('name')
            ->paginate(15);
        
        // Get unique states for the filter dropdown
        $states = Suburb::select('state')->distinct()->orderBy('state')->pluck('state');
        
        return view('admin.suburbs.index', compact('suburbs', 'search', 'status', 'state', 'states'));
    }

    /**
     * Show the form for creating a new suburb.
     */
    public function create()
    {
        // Get list of Australian states for dropdown
        $states = $this->getAustralianStates();
        
        return view('admin.suburbs.create', compact('states'));
    }

    /**
     * Store a newly created suburb.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'postcode' => 'required|digits:4',
            'active' => 'boolean',
        ]);
        
        $validated['active'] = $request->has('active');
        
        Suburb::create($validated);
        
        return redirect()->route('admin.suburbs.index')
            ->with('success', 'Suburb created successfully.');
    }

    /**
     * Display the specified suburb.
     */
    public function show(Suburb $suburb)
    {
        // Load relationships for statistics
        $instructorCount = $suburb->instructors()->count();
        $bookingCount = $suburb->bookings()->count();
        
        return view('admin.suburbs.show', compact('suburb', 'instructorCount', 'bookingCount'));
    }

    /**
     * Show the form for editing the specified suburb.
     */
    public function edit(Suburb $suburb)
    {
        $states = $this->getAustralianStates();
        
        return view('admin.suburbs.edit', compact('suburb', 'states'));
    }

    /**
     * Update the specified suburb.
     */
    public function update(Request $request, Suburb $suburb)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'postcode' => 'required|digits:4',
            'active' => 'boolean',
        ]);
        
        $validated['active'] = $request->has('active');
        
        $suburb->update($validated);
        
        return redirect()->route('admin.suburbs.show', $suburb)
            ->with('success', 'Suburb updated successfully.');
    }

    /**
     * Remove the specified suburb.
     */
    public function destroy(Suburb $suburb)
    {
        // Check if suburb is being used by any instructors
        if ($suburb->instructors()->count() > 0) {
            return back()->with('error', 'Cannot delete this suburb as it is associated with one or more instructors.');
        }
        
        // Check if suburb is being used in bookings
        if ($suburb->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete this suburb as it is associated with one or more bookings.');
        }
        
        $suburb->delete();
        
        return redirect()->route('admin.suburbs.index')
            ->with('success', 'Suburb deleted successfully.');
    }
    
    /**
     * Toggle suburb active status.
     */
    public function toggleStatus(Suburb $suburb)
    {
        $suburb->active = !$suburb->active;
        $suburb->save();
        
        $status = $suburb->active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Suburb {$status} successfully.");
    }
    
    /**
     * Import suburbs from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        
        // Remove header row
        if (count($data) > 0) {
            array_shift($data);
        }
        
        $importCount = 0;
        $duplicateCount = 0;
        
        foreach ($data as $row) {
            if (count($row) < 3) continue;
            
            $name = trim($row[0]);
            $state = trim($row[1]);
            $postcode = trim($row[2]);
            
            // Validate postcode
            if (!preg_match('/^\d{4}$/', $postcode)) continue;
            
            // Check for duplicates by name and postcode
            $exists = Suburb::where('name', $name)
                ->where('postcode', $postcode)
                ->exists();
                
            if ($exists) {
                $duplicateCount++;
                continue;
            }
            
            Suburb::create([
                'name' => $name,
                'state' => $state,
                'postcode' => $postcode,
                'active' => true,
            ]);
            
            $importCount++;
        }
        
        $message = "{$importCount} suburbs imported successfully.";
        if ($duplicateCount > 0) {
            $message .= " {$duplicateCount} duplicates skipped.";
        }
        
        return redirect()->route('admin.suburbs.index')
            ->with('success', $message);
    }
    
    /**
     * Export suburbs to CSV file.
     */
    public function export()
    {
        $suburbs = Suburb::orderBy('state')
            ->orderBy('name')
            ->get(['name', 'state', 'postcode', 'active']);
        
        $filename = 'suburbs-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($suburbs) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'State', 'Postcode', 'Active']);
            
            // Add rows
            foreach ($suburbs as $suburb) {
                fputcsv($file, [
                    $suburb->name,
                    $suburb->state,
                    $suburb->postcode,
                    $suburb->active ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get list of Australian states.
     */
    private function getAustralianStates()
    {
        return [
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
        ];
    }
}