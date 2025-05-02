<!-- resources/views/services.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="display-4 fw-bold text-center mb-5">Our Services</h1>
    
    <div class="row g-4">
        @foreach($services as $service)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4>{{ $service->name }}</h4>
                    <p class="text-muted">{{ $service->description }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">${{ number_format($service->price, 2) }}</span>
                        <span class="text-muted">{{ $service->duration }} minutes</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <div class="d-grid">
                        <a href="{{ route('booking.index') }}" class="btn btn-outline-primary">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- FAQ Section -->
    <div class="mt-5 pt-5">
        <h2 class="fw-bold text-center mb-4">Frequently Asked Questions</h2>
        
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        What should I bring to my first driving lesson?
                    </button>
                    <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        What should I bring to my first driving lesson?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        For your first driving lesson, please bring your learner's permit or provisional license, comfortable shoes suitable for driving, and any glasses or contact lenses if you require them for driving. It's also a good idea to bring a bottle of water and wear comfortable clothing.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        How many lessons will I need before taking my driving test?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        The number of lessons required varies from person to person, depending on your prior experience, natural aptitude, and how quickly you learn. On average, most students require between 10-20 hours of professional instruction before they're ready for their test. Your instructor will provide ongoing feedback on your progress and let you know when they think you're ready.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Can I use your car for my driving test?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we offer a test package that includes the use of our car for your driving test. This includes pick-up before the test, a short warm-up drive, use of the car during the test, and drop-off afterward. Many students find this reduces their anxiety as they're already familiar with the vehicle.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        What happens if I need to cancel or reschedule my lesson?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We understand that plans can change. We request at least 24 hours' notice for cancellations or rescheduling. Cancellations with less than 24 hours' notice may incur a fee of 50% of the lesson cost. No-shows or cancellations with less than 2 hours' notice may be charged the full lesson fee. You can cancel or reschedule through your online account or by calling our office.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Do you offer lessons on weekends?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we offer lessons seven days a week, including weekends and some evenings, to accommodate different schedules. Weekend slots are popular and tend to book up quickly, so we recommend booking these in advance.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item border-0 mb-3 shadow-sm">
                <h2 class="accordion-header" id="headingSix">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        What areas do you service?
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We provide driving lessons in many suburbs across Australia. When booking, you can select your preferred suburb, and we'll match you with an instructor who services that area. If you're unsure whether we cover your area, please contact our office, and we'll be happy to assist.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action Section -->
    <div class="py-5 mt-5 bg-primary text-white rounded">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">Ready to Start Your Driving Journey?</h2>
                    <p class="lead mb-0">Book your first lesson today and take the first step towards getting your license.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('booking.index') }}" class="btn btn-light btn-lg">Book Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
