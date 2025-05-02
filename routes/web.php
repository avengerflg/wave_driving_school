<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\AvailabilityController;
use App\Http\Controllers\Instructor\BookingController as InstructorBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SuburbController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// Redirect root to booking page
Route::get('/', function () {
    return redirect()->route('booking.index');
})->name('home'); // Updated to redirect to booking.index

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Public Routes
Route::controller(HomeController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'submitContact')->name('contact.submit');
    Route::get('/faq', 'faq')->name('faq');
});

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

// Booking Routes
Route::prefix('booking')->name('booking.')->group(function () {
    // Public Booking Routes
    Route::controller(BookingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/suburbs', 'suburbs')->name('suburbs');
        Route::post('/suburbs', 'selectSuburb')->name('suburb.select');
        Route::get('/instructors/{suburb}', 'instructors')->name('instructors');
        Route::post('/instructor', 'selectInstructor')->name('instructor.select');
        Route::get('/availability/{instructor}', 'availability')->name('availability');
    });

    // Protected Booking Routes
    Route::middleware(['auth'])->controller(BookingController::class)->group(function () {
        Route::post('/time', 'selectTime')->name('select-time');
        Route::get('/services', 'services')->name('services');
        Route::post('/service', 'selectService')->name('service.select');
        Route::get('/details', 'details')->name('details');
        Route::post('/details', 'saveDetails')->name('details.save');
        Route::get('/payment', 'payment')->name('payment');
        Route::post('/payment', 'processPayment')->name('payment.process');
        Route::get('/confirmation/{booking}', 'confirmation')->name('confirmation');
    });
});

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return match(auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            default => redirect()->route('client.bookings.index'),
        };
    })->name('dashboard');
});

// Client Routes
Route::middleware(['auth', 'role:user'])->prefix('client')->name('client.')->group(function () {
    Route::controller(ClientBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{booking}', 'show')->name('show');
        Route::put('/{booking}/cancel', 'cancel')->name('cancel');
        Route::post('/{booking}/review', 'storeReview')->name('review');
    });
});

// Instructor Routes
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::controller(InstructorDashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
        Route::patch('/profile', 'updateProfile')->name('profile.update');
    });

    Route::controller(AvailabilityController::class)->prefix('availability')->name('availability.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{availability}', 'destroy')->name('destroy');
        Route::get('/bulk-create', 'bulkCreate')->name('bulk-create');
        Route::post('/bulk', 'bulkStore')->name('bulk-store');
    });

    Route::controller(InstructorBookingController::class)->group(function () {
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/bookings', 'index')->name('bookings.index');
        Route::get('/bookings/{booking}', 'show')->name('bookings.show');
        Route::put('/bookings/{booking}/status', 'updateStatus')->name('bookings.update-status');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'updateSettings')->name('settings.update');
        
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/bookings', 'bookingReports')->name('bookings');
            Route::get('/revenue', 'revenueReports')->name('revenue');
            Route::get('/instructors', 'instructorReports')->name('instructors');
        });
    });

    Route::controller(AdminBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{booking}', 'show')->name('show');
        Route::get('/{booking}/edit', 'edit')->name('edit');
        Route::put('/{booking}', 'update')->name('update');
        Route::delete('/{booking}', 'destroy')->name('destroy');
        Route::put('/{booking}/status', 'updateStatus')->name('update-status');
    });

    Route::controller(InstructorController::class)->prefix('instructors')->name('instructors.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{instructor}', 'show')->name('show');
        Route::get('/{instructor}/edit', 'edit')->name('edit');
        Route::put('/{instructor}', 'update')->name('update');
        Route::delete('/{instructor}', 'destroy')->name('destroy');
        Route::put('/{instructor}/approve', 'approve')->name('approve');
        Route::put('/{instructor}/deactivate', 'deactivate')->name('deactivate');
    });

    Route::resource('users', UserController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('suburbs', SuburbController::class);
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/suburbs/search', [SuburbController::class, 'search'])->name('suburbs.search');
    Route::get('/instructors/{suburb}', [InstructorController::class, 'getBySuburb'])->name('instructors.by-suburb');
    Route::get('/availability/{instructor}/{date}', [AvailabilityController::class, 'getForDate'])->name('availability.for-date');
});
