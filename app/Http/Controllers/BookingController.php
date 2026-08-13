<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:180',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'service' => 'nullable|string|max:120',
            'pickup_date' => 'nullable|date',
            'pickup_location' => 'nullable|string|max:180',
            'destination' => 'nullable|string|max:180',
            'days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string|max:2000',
            'website' => 'nullable|max:0', // honeypot
        ]);

        unset($data['website']);

        $vehicle = empty($data['vehicle_id']) ? null : Vehicle::find($data['vehicle_id']);

        $booking = Booking::create($data + [
            'reference' => 'BK-' . strtoupper(Str::random(6)),
            'vehicle_name' => $vehicle?->name,
            'status' => 'new',
            'source_url' => $request->input('source_url', url()->previous()),
        ]);

        return redirect()->route('booking.thanks', $booking->reference);
    }

    public function thanks(string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        $number = preg_replace('/\D+/', '', (string) Setting::get('whatsapp_number'));

        $lines = array_filter([
            'Booking ' . $booking->reference,
            'Name: ' . $booking->name,
            $booking->vehicle_name ? 'Vehicle: ' . $booking->vehicle_name : null,
            $booking->service ? 'Service: ' . $booking->service : null,
            $booking->pickup_date ? 'Pickup: ' . $booking->pickup_date->format('d M Y') : null,
            $booking->pickup_location ? 'From: ' . $booking->pickup_location : null,
            $booking->destination ? 'To: ' . $booking->destination : null,
            $booking->days ? 'Days: ' . $booking->days : null,
        ]);

        $whatsapp = 'https://wa.me/' . $number . '?text=' . rawurlencode(implode("\n", $lines));

        return view('public.booking-thanks', compact('booking', 'whatsapp'));
    }
}
