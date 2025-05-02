<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register singleton for Settings
        $this->app->singleton('settings', function () {
            return cache()->rememberForever('settings', function () {
                return Setting::pluck('value', 'key')->all();
            });
        });

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL older versions
        Schema::defaultStringLength(191);

        // Use Bootstrap pagination
        Paginator::useBootstrap();

        // Share common data with all views
        View::share('appName', config('app.name'));
        
        // Share settings with all views
        View::composer('*', function ($view) {
            $view->with('settings', app('settings'));
        });

        // Custom Blade Directives
        $this->registerBladeDirectives();

        // Set timezone
        date_default_timezone_set(config('app.timezone', 'UTC'));

        // Custom validation rules
        $this->registerCustomValidationRules();
    }

    /**
     * Register custom Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        // Role checking directive
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->role == {$role}): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // Multiple roles checking directive
        Blade::directive('roles', function ($roles) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, {$roles})): ?>";
        });

        Blade::directive('endroles', function () {
            return "<?php endif; ?>";
        });

        // Money format directive
        Blade::directive('money', function ($amount) {
            return "<?php echo '$' . number_format($amount, 2); ?>";
        });

        // Date format directive
        Blade::directive('date', function ($expression) {
            return "<?php echo Carbon\Carbon::parse($expression)->format('M d, Y'); ?>";
        });

        // Status badge directive
        Blade::directive('status', function ($status) {
            return "<?php echo view('components.status-badge', ['status' => $status])->render(); ?>";
        });
    }

    /**
     * Register custom validation rules.
     */
    protected function registerCustomValidationRules(): void
    {
        // Australian Phone Number
        Validator::extend('aus_phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(\+61|0)[2-478](?:[ -]?[0-9]){8}$/', $value);
        }, 'The :attribute must be a valid Australian phone number.');

        // Australian Postcode
        Validator::extend('aus_postcode', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]{4}$/', $value);
        }, 'The :attribute must be a valid Australian postcode.');

        // Future Date
        Validator::extend('future_date', function ($attribute, $value, $parameters, $validator) {
            return Carbon::parse($value)->isFuture();
        }, 'The :attribute must be a future date.');

        // Business Hours
        Validator::extend('business_hours', function ($attribute, $value, $parameters, $validator) {
            $time = Carbon::parse($value)->format('H:i');
            return $time >= '09:00' && $time <= '17:00';
        }, 'The :attribute must be between 9 AM and 5 PM.');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['settings'];
    }
}
