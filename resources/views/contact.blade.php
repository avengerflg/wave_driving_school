<!-- resources/views/contact.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="display-4 fw-bold text-center mb-5">Contact Us</h1>
            
            <div class="row g-5">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Get in Touch</h4>
                            
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5>Email</h5>
                                    <p class="mb-0">info@wavedrivingschool.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5>Phone</h5>
                                    <p class="mb-0">(02) 1234 5678</p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5>Head Office</h5>
                                    <p class="mb-0">123 Main Street<br>Sydney, NSW 2000<br>Australia</p>
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5>Office Hours</h5>
                                    <p class="mb-0">Monday - Friday: 9am - 5pm<br>Saturday: 9am - 1pm<br>Sunday: Closed</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Follow Us</h5>
                                <div class="d-flex mt-3">
                                    <a href="#" class="text-primary me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                                    <a href="#" class="text-primary me-3"><i class="fab fa-twitter fa-lg"></i></a>
                                    <a href="#" class="text-primary me-3"><i class="fab fa-instagram fa-lg"></i></a>
                                    <a href="#" class="text-primary"><i class="fab fa-linkedin-in fa-lg"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Send Us a Message</h4>
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form action="{{ route('contact.send') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Your Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Your Phone (optional)</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Your Message</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26512.84435186454!2d151.20000000000002!3d-33.87!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b12ae401e8b983f%3A0x5017d681632ccc0!2sSydney%20NSW%202000%2C%20Australia!5e0!3m2!1sen!2sus!4v1650000000000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
