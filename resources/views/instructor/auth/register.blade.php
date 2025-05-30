<!DOCTYPE html>
<html lang="en" class="light-style">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Instructor Registration</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #696cff;
            --primary-hover: #5f62e6;
            --surface-color: #fff;
            --text-primary: #566a7f;
            --text-secondary: #697a8d;
            --border-color: #d9dee3;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .registration-wrapper {
            padding: 2rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .registration-card {
            background: var(--surface-color);
            border-radius: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 800px;
            margin: auto;
            position: relative;
            overflow: hidden;
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
        }

        .card-body {
            padding: 3rem 2rem;
        }

        .brand-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .brand-logo img {
            height: 48px;
            object-fit: contain;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            border-color: var(--border-color);
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
        }

        .select2-container--default .select2-selection--multiple {
            border-radius: 0.5rem;
            border-color: var(--border-color);
            padding: 0.3rem;
        }

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 1rem;
            object-fit: cover;
            border: 2px solid var(--border-color);
            padding: 4px;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .form-password-toggle .input-group-text {
            border-radius: 0 0.5rem 0.5rem 0;
            cursor: pointer;
        }

        .alert {
            border-radius: 0.5rem;
            border: none;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="registration-wrapper">
        <div class="registration-card">
            <div class="card-body">
                <!-- Brand Logo -->
                <div class="brand-logo">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="Logo">
                </div>

                <!-- Header -->
                <div class="text-center mb-4">
                    <h4 class="mb-2" style="color: var(--text-primary)">Begin Your Journey as an Instructor 🚗</h4>
                    <p class="text-muted">Fill in your information to get started</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Registration Form -->
                <form action="{{ route('instructor.register.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h5 class="form-section-title">Personal Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Home Suburb</label>
                                    <select class="form-select" name="suburb_id" required>
                                        <option value="">Select your suburb</option>
                                        @foreach($suburbs as $suburb)
                                        <option value="{{ $suburb->id }}" {{ old('suburb_id') == $suburb->id ? 'selected' : '' }}>
                                            {{ $suburb->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="form-section">
                        <h5 class="form-section-title">Professional Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">License Number</label>
                                    <input type="text" class="form-control" name="license_number" value="{{ old('license_number') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Service Areas</label>
                                    <select class="select2 form-control" name="service_suburbs[]" multiple required>
                                        @foreach($suburbs as $suburb)
                                        <option value="{{ $suburb->id }}">{{ $suburb->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Bio</label>
                                    <textarea class="form-control" name="bio" rows="4">{{ old('bio') }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" name="profile_image" accept="image/*" onchange="previewImage(this)">
                                    <div class="mt-2 text-center">
                                        <img id="preview" src="#" alt="Preview" class="profile-preview" style="display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Security Section -->
                    <div class="form-section">
                        <h5 class="form-section-title">Account Security</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group form-password-toggle">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" required>
                                        <span class="input-group-text cursor-pointer">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-password-toggle">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password_confirmation" required>
                                        <span class="input-group-text cursor-pointer">
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <span class="text-muted">Already have an account?</span>
                        <a href="{{ route('instructor.login') }}" class="ms-1">Sign in instead</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select service areas',
                allowClear: true
            });

            // Password toggle
            $('.form-password-toggle .input-group-text').on('click', function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-show').addClass('bx-hide');
                }
            });
        });

        // Image preview function
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>