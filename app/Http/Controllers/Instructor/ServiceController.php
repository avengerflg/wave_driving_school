<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('active', true)
            ->orderBy('name')
            ->get();

        return view('instructor.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        if (!$service->active) {
            abort(404);
        }

        $bookings = auth()->user()->instructor->bookings()
            ->where('service_id', $service->id)
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_bookings' => auth()->user()->instructor->bookings()
                ->where('service_id', $service->id)
                ->count(),
            'completed_bookings' => auth()->user()->instructor->bookings()
                ->where('service_id', $service->id)
                ->where('status', 'completed')
                ->count(),
            'revenue' => auth()->user()->instructor->bookings()
                ->where('service_id', $service->id)
                ->where('status', 'completed')
                ->sum('price')
        ];

        return view('instructor.services.show', compact('service', 'bookings', 'stats'));
    }
}