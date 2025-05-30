<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $clients = User::where('role', 'student') // Changed from 'client' to 'student'
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.clients.index', compact('clients', 'search', 'status'));
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_number' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $imagePath;
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Set role as student (not client)
        $validated['role'] = 'student';

        $client = User::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client.
     *
     * @param  \App\Models\User  $client
     * @return \Illuminate\View\View
     */
    public function show(User $client)
    {
        // Make sure we're only showing students
        if ($client->role !== 'student') {
            abort(404);
        }
        
        // Get client's bookings
        $bookings = Booking::where('user_id', $client->id)
            ->with(['instructor.user', 'service', 'suburb'])
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.clients.show', compact('client', 'bookings'));
    }

    /**
     * Show the form for editing the specified client.
     *
     * @param  \App\Models\User  $client
     * @return \Illuminate\View\View
     */
    public function edit(User $client)
    {
        // Make sure we're only editing students
        if ($client->role !== 'student') {
            abort(404);
        }
        
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $client)
    {
        // Make sure we're only updating students
        if ($client->role !== 'student') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($client->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_number' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($client->profile_image) {
                Storage::disk('public')->delete($client->profile_image);
            }
            
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $imagePath;
        }

        // Only update password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client.
     *
     * @param  \App\Models\User  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $client)
    {
        // Make sure we're only deleting students
        if ($client->role !== 'student') {
            abort(404);
        }

        // Delete profile image if exists
        if ($client->profile_image) {
            Storage::disk('public')->delete($client->profile_image);
        }
        
        // Delete the client
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
    
    /**
     * Display all bookings for a specific client.
     *
     * @param  \App\Models\User  $client
     * @return \Illuminate\View\View
     */
    public function bookings(User $client)
    {
        // Make sure we're only showing bookings for students
        if ($client->role !== 'student') {
            abort(404);
        }
        
        $bookings = Booking::where('user_id', $client->id)
            ->with(['instructor.user', 'service', 'suburb'])
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('admin.clients.bookings', compact('client', 'bookings'));
    }
    
    /**
     * Update client status (active/inactive).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, User $client)
    {
        // Make sure we're only updating status for students
        if ($client->role !== 'student') {
            abort(404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        
        $client->update([
            'status' => $validated['status']
        ]);
        
        return back()->with('success', 'Client status updated successfully.');
    }
}