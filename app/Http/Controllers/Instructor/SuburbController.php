<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Suburb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuburbController extends Controller
{
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        $query = Suburb::query()
            ->where('active', true)
            ->withCount(['bookings' => function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            }]);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('postcode', 'like', "%{$search}%");
            });
        }

        // Apply state filter
        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        $suburbs = $query->orderBy('name')
                        ->paginate(10)
                        ->withQueryString();

        $states = Suburb::distinct('state')->pluck('state');

        return view('instructor.suburbs.index', compact('suburbs', 'states'));
    }

    public function show(Suburb $suburb)
    {
        if (!$suburb->active) {
            abort(404);
        }

        $instructor = Auth::user()->instructor;

        $bookings = $instructor->bookings()
            ->where('suburb_id', $suburb->id)
            ->with(['user', 'service'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_bookings' => $instructor->bookings()
                ->where('suburb_id', $suburb->id)
                ->count(),
            'completed_bookings' => $instructor->bookings()
                ->where('suburb_id', $suburb->id)
                ->where('status', 'completed')
                ->count(),
            'revenue' => $instructor->bookings()
                ->where('suburb_id', $suburb->id)
                ->where('status', 'completed')
                ->sum('price')
        ];

        return view('instructor.suburbs.show', compact('suburb', 'bookings', 'stats'));
    }
}