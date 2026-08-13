<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Location;
use App\Models\Post;
use App\Models\ServiceType;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $locations = Location::where('is_active', true)->count();
        $services = ServiceType::where('is_active', true)->where('show_in_directory', true)->count();

        return view('admin.dashboard', [
            'newBookings' => Booking::where('status', 'new')->count(),
            'weekBookings' => Booking::where('created_at', '>=', now()->subDays(7))->count(),
            'vehicles' => Vehicle::where('is_active', true)->count(),
            'cities' => City::where('is_active', true)->count(),
            'locations' => $locations,
            'posts' => Post::where('is_published', true)->count(),
            'generatedPages' => $locations * $services,
            'recent' => Booking::latest()->limit(8)->get(),
        ]);
    }
}
