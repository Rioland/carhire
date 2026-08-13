@extends('admin.layout')
@section('title', 'Bookings')

@section('content')

<div class="topbar">
    <div>
        <h1>Bookings</h1>
        <p class="topbar__sub">{{ $bookings->total() }} {{ Str::plural('request', $bookings->total()) }}</p>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.bookings.index') }}" aria-current="{{ $status ? 'false' : 'true' }}">All</a>
    @foreach(\App\Models\Booking::STATUSES as $option)
        <a href="{{ route('admin.bookings.index', ['status' => $option]) }}" aria-current="{{ $status === $option ? 'true' : 'false' }}">
            {{ ucfirst($option) }} ({{ $counts[$option] ?? 0 }})
        </a>
    @endforeach
</div>

<form class="toolbar" method="GET">
    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <input type="search" name="q" value="{{ $term }}" placeholder="Name, phone or reference">
    <button class="btn btn--ghost btn--sm" type="submit">Search</button>
</form>

<div class="panel">
    @if($bookings->isEmpty())
        <div class="empty">No bookings match this view.</div>
    @else
        <div class="tableWrap">
            <table>
                <thead>
                <tr>
                    <th>Received</th><th>Reference</th><th>Name</th><th>Phone</th>
                    <th>Vehicle</th><th>Pickup</th><th>Status</th><th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td style="white-space:nowrap">{{ $booking->created_at->format('j M, H:i') }}</td>
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
        <div class="pagination">{{ $bookings->links() }}</div>
    @endif
</div>

@endsection
