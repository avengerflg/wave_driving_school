<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PackageController extends Controller
{
    /**
     * Display all available packages
     */
    public function index()
    {
        $packages = Package::where('active', true)
            ->orderBy('name')
            ->get();
        
        return view('packages.index', compact('packages'));
    }

    /**
     * Display package details
     */
    public function show($id)
    {
        $package = Package::findOrFail($id);
        return view('packages.show', compact('package'));
    }
}