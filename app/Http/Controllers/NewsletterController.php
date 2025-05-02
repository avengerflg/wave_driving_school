<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);
        
        Newsletter::updateOrCreate(
            ['email' => $validated['email']],
            ['active' => true]
        );
        
        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
