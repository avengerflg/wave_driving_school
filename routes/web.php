<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Student\BookingController as StudentBookingController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\AvailabilityController;
use App\Http\Controllers\Instructor\BookingController as InstructorBookingController;
use App\Http\Controllers\Instructor\ClientController;
use App\Http\Controllers\Instructor\CalendarController;
use App\Http\Controllers\Instructor\SuburbController as InstructorSuburbController;
use App\Http\Controllers\Instructor\ServiceController as InstructorServiceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SuburbController as AdminSuburbController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Instructor\InstructorAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Student\PackageController as StudentPackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home redirect - Updated to use 'student' role
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('booking.index')
        };
    }
    return redirect()->route('booking.index');
})->name('home');

// Public content routes
Route::controller(HomeController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'submitContact')->name('contact.submit');
    Route::get('/faq', 'faq')->name('faq');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// User Authentication
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

// Logout Route
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Instructor Authentication
Route::prefix('instructor')->name('instructor.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [InstructorAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [InstructorAuthController::class, 'login'])->name('login.submit');
        Route::get('register', [InstructorAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [InstructorAuthController::class, 'register'])->name('register.submit');
    });
});

// Admin Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminController::class, 'login'])->name('login');
    Route::post('login', [AdminController::class, 'authenticate'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Package and Cart Routes
|--------------------------------------------------------------------------
*/

// Public Package Routes
Route::prefix('packages')->name('packages.')->group(function () {
    Route::get('/', [PackageController::class, 'index'])->name('index');
    Route::get('/{id}', [PackageController::class, 'show'])->name('show');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::get('/remove/{index}', [CartController::class, 'remove'])->name('remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    
    // Protected Cart Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/details', [CartController::class, 'details'])->name('details');
        Route::post('/details', [CartController::class, 'saveDetails'])->name('save-details');
        Route::get('/payment', [CartController::class, 'payment'])->name('payment');
        Route::post('/payment', [CartController::class, 'processPayment'])->name('process-payment');
        Route::get('/confirmation/{id?}', [CartController::class, 'confirmation'])->name('confirmation');
    });
});

/*
|--------------------------------------------------------------------------
| Student Routes - Updated with consistent naming
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Booking routes
    Route::controller(StudentBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{booking}', 'show')->name('show');
        Route::put('/{booking}/cancel', 'cancel')->name('cancel');
        Route::post('/{booking}/review', 'storeReview')->name('review');
        Route::get('/{booking}/reschedule', 'rescheduleForm')->name('reschedule.form');
        Route::put('/{booking}/reschedule', 'reschedule')->name('reschedule');
    });
    
    // Package routes for students
    Route::controller(StudentPackageController::class)->prefix('packages')->name('packages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/credits', 'credits')->name('credits');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/orders/{order}', 'showOrder')->name('orders.show');
        Route::get('/{package}', 'show')->name('show');
        Route::post('/redeem/{packageCredit}', 'redeem')->name('redeem');
    });
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Legacy client routes redirect to student routes for backward compatibility
Route::middleware(['auth', 'role:student'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('student.dashboard');
    })->name('dashboard');
    
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('student.bookings.index');
        })->name('index');
        
        Route::get('/{booking}', function ($booking) {
            return redirect()->route('student.bookings.show', $booking);
        })->name('show');
        
        Route::get('/{booking}/reschedule', function ($booking) {
            return redirect()->route('student.bookings.reschedule.form', $booking);
        })->name('reschedule.form');
    });
    
    // Legacy package routes
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('student.packages.index');
        })->name('index');
        
        Route::get('/credits', function () {
            return redirect()->route('student.packages.credits');
        })->name('credits');
        
        Route::get('/orders', function () {
            return redirect()->route('student.packages.orders');
        })->name('orders');
        
        Route::get('/orders/{order}', function ($order) {
            return redirect()->route('student.packages.orders.show', $order);
        })->name('orders.show');
        
        Route::get('/{package}', function ($package) {
            return redirect()->route('student.packages.show', $package);
        })->name('show');
        
        Route::post('/redeem/{packageCredit}', function ($packageCredit) {
            return redirect()->route('student.packages.redeem', $packageCredit);
        })->name('redeem');
    });
});

/*
|--------------------------------------------------------------------------
| Booking System Routes
|--------------------------------------------------------------------------
*/

// Public Booking Process
Route::prefix('booking')->name('booking.')->group(function () {
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

/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    // Dashboard & Profile
    Route::controller(InstructorDashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
        Route::patch('/profile', 'updateProfile')->name('profile.update');
    });

    // Calendar Routes
    Route::prefix('calendar')->name('calendar.')->controller(CalendarController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'getCalendarData')->name('data');
        Route::get('/availability', 'availability')->name('availability');
        Route::post('/availability', 'storeAvailability')->name('storeAvailability');
        Route::post('/store-availability', 'storeAvailability')->name('store-availability');
        Route::delete('/availability/{availability}', 'destroyAvailability')->name('destroyAvailability');
        Route::get('/booking/{booking}', 'getBookingDetails')->name('booking.details');
    });

    // Shortcut for instructor.calendar route
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // Availability Management
    Route::controller(AvailabilityController::class)->prefix('availability')->name('availability.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('/{availability}', 'destroy')->name('destroy');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
        Route::get('/create', 'create')->name('create');
        Route::get('/bulk-create', 'bulkCreate')->name('bulk-create');
        Route::post('/bulk', 'bulkStore')->name('bulk-store');
    });

    // Services Management
    Route::controller(InstructorServiceController::class)->prefix('services')->name('services.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{service}', 'show')->name('show');
    });

    // Booking Management
    Route::controller(InstructorBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store'); // Add this line for creating bookings
        Route::get('/{booking}', 'show')->name('show');
        Route::put('/{booking}/status', 'updateStatus')->name('update-status');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/{booking}/complete', 'complete')->name('complete');
        Route::get('/{booking}/cancel', 'cancel')->name('cancel');
    });

    // Reschedule Booking Routes
    Route::get('/bookings/{booking}/reschedule', [InstructorBookingController::class, 'rescheduleForm'])->name('bookings.reschedule.form');
    Route::put('/bookings/{booking}/reschedule', [InstructorBookingController::class, 'reschedule'])->name('bookings.reschedule');

    // Client Management (still called clients for instructors)
    Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{client}', 'show')->name('show');
        Route::get('/{client}/bookings', 'bookings')->name('bookings');
    });

    // Suburb Management
    Route::controller(InstructorSuburbController::class)->prefix('suburbs')->name('suburbs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{suburb}', 'show')->name('show');
    });

    // Package Management
    Route::controller(App\Http\Controllers\Instructor\PackageController::class)->prefix('packages')->name('packages.')->group(function () {
        Route::get('/', 'index')->name('index');
        
        // Put all specific routes BEFORE the {package} parameter route
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/orders/{order}', 'showOrder')->name('orders.show');
        Route::get('/credits', 'credits')->name('credits');
        Route::get('/lessons', 'packageLessons')->name('lessons');
        Route::get('/clients/{student}/credits', 'studentCredits')->name('student.credits');
        
        // Always put parameter routes LAST
        Route::get('/{package}', 'show')->name('show');
    });
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Settings
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'updateSettings')->name('settings.update');
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/bookings', 'bookingReports')->name('bookings');
            Route::get('/revenue', 'revenueReports')->name('revenue');
            Route::get('/instructors', 'instructorReports')->name('instructors');
        });
    });

    // Order Management Dashboard
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');

    // Admin Calendar Routes - Update these routes
    Route::prefix('calendar')->name('calendar.')->controller(AdminCalendarController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'getCalendarData')->name('data');
        Route::get('/availability', 'availability')->name('availability');
        Route::post('/availability', 'storeAvailability')->name('availability.store');
        Route::delete('/availability/{availability}', 'destroyAvailability')->name('availability.destroy');
        Route::get('/booking/{booking}', 'getBookingDetails')->name('booking.details');
        Route::post('/bookings', 'store')->name('bookings.store');
        Route::put('/bookings/{booking}/status', 'updateBookingStatus')->name('bookings.update-status');
        Route::get('/booking-form-data', 'getBookingFormData')->name('booking-form-data');
        Route::get('/debug-availability', 'debugAvailability')->name('debug-availability');
        Route::get('/debug-instructors', 'debugInstructors')->name('debug-instructors');
    });

    // Instructor Management
    Route::controller(InstructorController::class)->prefix('instructors')->name('instructors.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{instructor}', 'show')->name('show');
        Route::get('/{instructor}/edit', 'edit')->name('edit');
        Route::put('/{instructor}', 'update')->name('update');
        Route::delete('/{instructor}', 'destroy')->name('destroy');
        Route::put('/{instructor}/status', 'updateStatus')->name('update-status');
        Route::get('/{instructor}/calendar', 'calendar')->name('calendar');
        Route::get('/{instructor}/calendar/data', 'getCalendarData')->name('calendar.data');
        Route::get('/{instructor}/schedule', 'calendar')->name('schedule');
        
        // Availability routes
        Route::get('/{instructor}/availability', 'availability')->name('availability');
        Route::post('/{instructor}/availability', 'storeAvailability')->name('availability.store');
        Route::post('/{instructor}/store-availability', 'storeAvailability')->name('store-availability');
        Route::delete('/{instructor}/availability/{availabilityId}', 'destroyAvailability')->name('availability.destroy');
        Route::post('/{instructor}/availability/bulk-delete', 'bulkDeleteAvailability')->name('availability.bulk-delete');
        Route::post('/{instructor}/availability/generate', 'generateAvailability')->name('availability.generate');
        Route::post('/{instructor}/availability/copy', 'copyAvailability')->name('availability.copy');
        Route::get('/{instructor}/bookings', 'bookings')->name('bookings');
        Route::post('/{instructor}/store-booking', 'storeBooking')->name('store-booking');
        Route::get('/{instructor}/calendar/booking/{booking}', 'getBookingDetails')->name('calendar.booking.details');
    });

    // Resource Controllers
    Route::resources([
        'users' => UserController::class,
        'services' => AdminServiceController::class,
        'suburbs' => AdminSuburbController::class,
        'packages' => AdminPackageController::class,
    ]);
    
    // Package Management Routes
    Route::controller(AdminPackageController::class)->prefix('packages')->name('packages.')->group(function () {
        // Status and feature toggling
        Route::patch('/{package}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::patch('/{package}/toggle-featured', 'toggleFeatured')->name('toggle-featured');
        
        // Order management
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/orders/{order}', 'showOrder')->name('orders.show');
        Route::put('/orders/{order}/status', 'updateOrderStatus')->name('orders.update-status');
        
        // Bulk operations
        Route::post('/bulk-update-prices', 'bulkUpdatePrices')->name('bulk-update-prices');
        Route::post('/bulk-toggle-status', 'bulkToggleStatus')->name('bulk-toggle-status');
        
        // Analytics
        Route::get('/analytics', 'analytics')->name('analytics');
        Route::get('/reports', 'reports')->name('reports');
        
        // Credits management
        Route::get('/credits', 'credits')->name('credits');
        Route::get('/credits/{credit}', 'showCredit')->name('credits.show');
        Route::put('/credits/{credit}/status', 'updateCreditStatus')->name('credits.update-status');
        
        // Export and import
        Route::get('/export', 'export')->name('export');
        Route::post('/import', 'import')->name('import');
    });
    
    // Client Management Routes
    Route::controller(App\Http\Controllers\Admin\ClientController::class)->prefix('clients')->name('clients.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{client}', 'show')->name('show');
        Route::get('/{client}/edit', 'edit')->name('edit');
        Route::put('/{client}', 'update')->name('update');
        Route::delete('/{client}', 'destroy')->name('destroy');

        // Additional client-specific routes
        Route::get('/{client}/bookings', 'bookings')->name('bookings');
        Route::put('/{client}/status', 'updateStatus')->name('update-status');
    });
    
    // Suburb Additional Routes
    Route::get('/suburbs/export', [AdminSuburbController::class, 'export'])->name('suburbs.export');
    Route::post('/suburbs/import', [AdminSuburbController::class, 'import'])->name('suburbs.import');
    Route::patch('/suburbs/{suburb}/toggle-status', [AdminSuburbController::class, 'toggleStatus'])->name('suburbs.toggle-status');
    
    // Service Additional Routes
    Route::patch('/services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::put('/services/{service}/toggle-featured', [AdminServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::post('/services/bulk-update-prices', [AdminServiceController::class, 'bulkUpdatePrices'])->name('services.bulk-update-prices');
    
    // Booking Management
    Route::controller(AdminBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::get('/{booking}', 'show')->name('show');
        Route::get('/{booking}/edit', 'edit')->name('edit');
        Route::put('/{booking}', 'update')->name('update');
        Route::put('/{booking}/status', 'updateStatus')->name('update-status');
        Route::delete('/{booking}', 'destroy')->name('destroy');
    });
    
    // Routes for calendar functionality
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/students', function () {
            return App\Models\User::where('role', 'student')->where('status', 'active')->get(['id', 'name']);
        })->name('students');
        
        Route::get('/services', function () {
            return App\Models\Service::where('active', true)->get(['id', 'name', 'price']);
        })->name('services');
        
        Route::get('/packages', function () {
            return App\Models\Package::where('active', true)->get(['id', 'name', 'price', 'lessons']);
        })->name('packages');
        
        Route::get('/instructors/{suburb_id}/availabilities', [InstructorController::class, 'getAvailabilities'])
            ->name('instructors.availabilities');
    });
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/suburbs/search', [AdminSuburbController::class, 'search'])->name('suburbs.search');
    Route::get('/instructors/{suburb}', [InstructorController::class, 'getBySuburb'])->name('instructors.by-suburb');
    Route::get('/availability/{instructor}/{date}', [AvailabilityController::class, 'getForDate'])->name('availability.for-date');
    Route::get('/packages', [PackageController::class, 'getPackages'])->name('packages');
});

// Notification Routes
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::get('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    Route::get('/{id}/redirect', [NotificationController::class, 'redirect'])->name('redirect');
});

// Additional Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});