@extends('layouts.public')
@section('title', 'Booking received | ' . ($settings['site_name'] ?? ''))

@section('content')

<section class="section">
    <div class="shell" style="max-width:640px">
        <div class="eyebrow">Reference {{ $booking->reference }}</div>
        <h1>Got it, {{ Str::before($booking->name, ' ') }}.</h1>
        <p class="lead">Your request is with the dispatch desk. Keep this reference for your records.</p>

        <div class="panel" style="margin:2rem 0">
            <ul class="checklist" style="color:var(--slate); font-size:.9375rem">
                @if($booking->vehicle_name)<li>Vehicle: {{ $booking->vehicle_name }}</li>@endif
                @if($booking->pickup_date)<li>Pickup: {{ $booking->pickup_date->format('j M Y') }}</li>@endif
                @if($booking->pickup_location)<li>From: {{ $booking->pickup_location }}</li>@endif
                @if($booking->destination)<li>To: {{ $booking->destination }}</li>@endif
                @if($booking->days)<li>Duration: {{ $booking->days }} {{ Str::plural('day', $booking->days) }}</li>@endif
                <li>Phone: {{ $booking->phone }}</li>
            </ul>
        </div>

        <p><strong>Want it confirmed faster?</strong> Send the same details on WhatsApp and we will reply straight away.</p>
        <a class="btn btn--primary" href="{{ $whatsapp }}" target="_blank" rel="noopener">Continue on WhatsApp</a>
        <a class="btn btn--ghost" href="{{ route('home') }}">Back to the site</a>
    </div>
</section>

@endsection
