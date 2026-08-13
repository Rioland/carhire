@extends('admin.layout')
@section('title', 'Overview')

@section('content')

<div class="topbar">
    <div>
        <h1>Overview</h1>
        <p class="topbar__sub">Everything the website is showing right now.</p>
    </div>
    <a class="btn btn--ghost" href="{{ route('home') }}" target="_blank" rel="noopener">Open the website</a>
</div>

<div class="cards">
    <div class="metric metric--accent">
        <div class="metric__value">{{ $newBookings }}</div>
        <div class="metric__label">New bookings</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ $weekBookings }}</div>
        <div class="metric__label">Last 7 days</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ $vehicles }}</div>
        <div class="metric__label">Live vehicles</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ $cities }}</div>
        <div class="metric__label">Cities</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ $locations }}</div>
        <div class="metric__label">Areas</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ number_format($generatedPages) }}</div>
        <div class="metric__label">Location pages</div>
    </div>
    <div class="metric">
        <div class="metric__value">{{ $posts }}</div>
        <div class="metric__label">Published articles</div>
    </div>
</div>

<div class="note">
    Every area you add is automatically paired with every service, so {{ $locations }} areas currently produce
    <strong>{{ number_format($generatedPages) }} location pages</strong> with their own URLs. Add one area and a full
    set appears; add one service and every area gains a page.
</div>

<div class="panel">
    <div class="panel__head">
        <h2>Latest booking requests</h2>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.bookings.index') }}">See all</a>
    </div>

    @if($recent->isEmpty())
        <div class="empty">No booking requests yet. They will appear here as soon as someone uses a form on the website.</div>
    @else
        <div class="tableWrap">
            <table>
                <thead>
                <tr>
                    <th>Reference</th><th>Name</th><th>Phone</th><th>Vehicle</th><th>Pickup</th><th>Status</th><th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($recent as $booking)
                    <tr>
                        <td class="mono" style="font-size:.8125rem">{{ $booking->reference }}</td>
                        <td>{{ $booking->name }}</td>
                        <td class="mono" style="font-size:.8125rem">{{ $booking->phone }}</td>
                        <td>{{ $booking->vehicle_name ?: '—' }}</td>
                        <td>{{ optional($booking->pickup_date)->format('j M Y') ?: '—' }}</td>
                        <td><span class="badge badge--{{ $booking->status }}">{{ $booking->status }}</span></td>
                        <td class="actions"><a class="btn btn--ghost btn--sm" href="{{ route('admin.bookings.show', $booking) }}">Open</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
