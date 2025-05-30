<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $instructor = $user->instructor;

        $stats = [
            'total_bookings' => $instructor->bookings()->count(),
            'completed_bookings' => $instructor->bookings()->where('status', 'completed')->count(),
            'total_students' => $instructor->bookings()->distinct('user_id')->count('user_id'),
            'total_suburbs' => $instructor->suburbs()->count(),
            'total_revenue' => $instructor->bookings()->where('status', 'completed')->sum('price'),
            'this_month_bookings' => $instructor->bookings()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
        ];

        return view('instructor.profile.show', compact('user', 'instructor', 'stats'));
    }

    public function edit()
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        return view('instructor.profile.edit', compact('user', 'instructor'));
    }

    public function update(InstructorProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        $validated = $request->validated();

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->update(['profile_image' => $path]);
        }

        // Update instructor details
        $instructor->update([
            'bio' => $validated['bio'],
            'experience_years' => $validated['experience_years'],
            'license_number' => $validated['license_number'],
            'teaching_style' => $validated['teaching_style'] ?? null,
            'languages' => $validated['languages'] ?? [],
            'qualifications' => $validated['qualifications'] ?? null,
        ]);

        return redirect()->route('instructor.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}