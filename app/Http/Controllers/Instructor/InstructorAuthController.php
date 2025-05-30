<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Suburb;

class InstructorAuthController extends Controller
{
    public function showLogin()
    {
        return view('instructor.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role !== 'instructor') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have instructor access.',
                ]);
            }

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('instructor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        $suburbs = Suburb::orderBy('name')->get();
        return view('instructor.auth.register', compact('suburbs'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'suburb_id' => 'required|exists:suburbs,id',
            'password' => 'required|string|min:8|confirmed',
            'license_number' => 'required|string|max:50|unique:instructors',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'service_suburbs' => 'required|array|min:1',
            'service_suburbs.*' => 'exists:suburbs,id',
        ]);

        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'suburb_id' => $validated['suburb_id'],
                'password' => Hash::make($validated['password']),
                'role' => 'instructor',
                'status' => 'pending', // New registrations are pending by default
            ]);

            // Handle profile image upload
            $profileImage = null;
            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image')->store('instructors', 'public');
            }

            // Create instructor profile
            $instructor = Instructor::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'],
                'bio' => $validated['bio'] ?? null,
                'profile_image' => $profileImage,
                'active' => false, // New instructors are inactive by default
                'suburbs' => $validated['service_suburbs'],
            ]);

            DB::commit();

            // Log in the user
            Auth::login($user);

            // Redirect with message about pending approval
            return redirect()->route('instructor.dashboard')
                ->with('warning', 'Your registration has been submitted successfully. Please wait for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during instructor registration: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('instructor.login');
    }
}