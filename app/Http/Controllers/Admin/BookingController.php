<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query()->with('vehicle');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($term = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('reference', 'like', "%{$term}%");
            });
        }

        return view('admin.bookings.index', [
            'bookings' => $query->latest()->paginate(25)->withQueryString(),
            'status' => $status,
            'term' => $term ?? null,
            'counts' => Booking::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Booking $booking)
    {
        $number = preg_replace('/\D+/', '', (string) Setting::get('whatsapp_number'));
        $clientNumber = preg_replace('/\D+/', '', $booking->phone);

        if (str_starts_with($clientNumber, '0')) {
            $clientNumber = '234' . substr($clientNumber, 1);
        }

        return view('admin.bookings.show', compact('booking', 'number', 'clientNumber'));
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($request->validate([
            'status' => 'required|in:' . implode(',', Booking::STATUSES),
            'admin_notes' => 'nullable|string|max:2000',
        ]));

        return back()->with('status', 'Booking updated.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('status', 'Booking deleted.');
    }
}
