<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service; // Add this
use App\Models\Instructor; // Add this

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::take(3)->get(); // Get 3 services to display
        $instructors = Instructor::with('user')->take(4)->get(); // Get 4 instructors with their user data
        
        return view('home', compact('services', 'instructors'));
    }

    public function about()
    {
        return view('about');
    }

    public function services()
    {
        return view('services');
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        // Validate and process contact form submission
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Process the contact form (e.g., send email, save to database)
        
        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function faq()
    {
        return view('faq');
    }
}
