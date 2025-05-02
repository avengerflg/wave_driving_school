<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Suburb;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $suburbs = Suburb::orderBy('name')->get();
        return view('auth.register', compact('suburbs'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'suburb_id' => ['required', 'exists:suburbs,id'],
            'terms' => ['required', 'accepted'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Default role to 'student' if not provided or invalid
        $role = isset($data['role']) && in_array($data['role'], ['student', 'instructor']) ? $data['role'] : 'student';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'suburb_id' => $data['suburb_id'],
            'role' => $role,
            'status' => $role === 'instructor' ? 'pending' : 'active',
        ]);

        // If registering as instructor, create instructor profile
        if ($role === 'instructor') {
            $user->instructorProfile()->create([
                'bio' => $data['bio'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'car_model' => $data['car_model'] ?? null,
                'transmission_type' => $data['transmission_type'] ?? null,
            ]);
        }

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : $this->registrationResponse($user);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Check if there's a booking in progress
        if (session()->has('booking')) {
            return redirect()->route('booking.services')
                ->with('success', 'Registration successful! You can now continue with your booking.');
        }

        return null;
    }

    /**
     * Get the response for a successful registration.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function registrationResponse($user)
    {
        $message = 'Registration successful!';
        
        if ($user->role === 'instructor') {
            $message .= ' Your account is pending approval. We will notify you once your account is approved.';
            return redirect()->route('login')->with('success', $message);
        }

        return redirect()->route('client.bookings.index')->with('success', $message);
    }
}