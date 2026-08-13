@extends('admin.layout')
@section('title', 'Booking ' . $booking->reference)

@section('content')

<div class="topbar">
    <div>
        <h1>{{ $booking->name }}</h1>
        <p class="topbar__sub">
            <a href="{{ route('admin.bookings.index') }}">← All bookings</a> ·
            <span class="mono">{{ $booking->reference }}</span> ·
            received {{ $booking->created_at->format('j M Y \a\t H:i') }}
        </p>
    </div>
    <div style="display:flex; gap:.5rem">
        <a class="btn btn--ghost" href="tel:{{ $booking->phone }}">Call</a>
        <a class="btn btn--primary" target="_blank" rel="noopener"
           href="https://wa.me/{{ $clientNumber }}?text={{ rawurlencode('Hello ' . $booking->name . ', regarding your booking ' . $booking->reference . ' —') }}">WhatsApp client</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:minmax(0,1.4fr) minmax(0,1fr); gap:1.25rem; align-items:start">
    <div class="panel">
        <div class="panel__head"><h2>Request details</h2></div>
        <div class="tableWrap">
            <table>
                <tbody>
                <tr><th style="width:180px">Phone</th><td class="mono">{{ $booking->phone }}</td></tr>
                <tr><th>Email</th><td>{{ $booking->email ?: '—' }}</td></tr>
                <tr><th>Vehicle</th><td>{{ $booking->vehicle_name ?: 'Not specified' }}</td></tr>
                <tr><th>Service</th><td>{{ $booking->service ?: '—' }}</td></tr>
                <tr><th>Pickup date</th><td>{{ optional($booking->pickup_date)->format('j F Y') ?: '—' }}</td></tr>
                <tr><th>Pickup location</th><td>{{ $booking->pickup_location ?: '—' }}</td></tr>
                <tr><th>Destination</th><td>{{ $booking->destination ?: '—' }}</td></tr>
                <tr><th>Duration</th><td>{{ $booking->days ? $booking->days . ' ' . Str::plural('day', $booking->days) : '—' }}</td></tr>
                <tr><th>Notes</th><td>{{ $booking->notes ?: '—' }}</td></tr>
                <tr><th>Came from</th><td style="word-break:break-all; font-size:.8125rem">{{ $booking->source_url ?: '—' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="panel">
            <div class="panel__head"><h2>Manage</h2></div>
            <div class="panel__body">
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                    @csrf @method('PUT')

                    <div class="field" style="margin-bottom:1rem">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            @foreach(\App\Models\Booking::STATUSES as $option)
                                <option value="{{ $option }}" @selected($booking->status === $option)>{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field" style="margin-bottom:1rem">
                        <label for="admin_notes">Internal notes</label>
                        <textarea id="admin_notes" name="admin_notes" placeholder="Who is handling this, quoted price, follow-up date">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                    </div>

                    <button class="btn btn--primary btn--block" type="submit">Save</button>
                </form>

                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" style="margin-top:1rem"
                      onsubmit="return confirm('Delete this booking permanently?')">
                    @csrf @method('DELETE')
                    <button class="btn btn--danger btn--block" type="submit">Delete booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
