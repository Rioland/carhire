@extends('layouts.public')
@section('title', 'Contact | ' . ($settings['site_name'] ?? ''))
@section('description', 'Reach the dispatch desk by phone, WhatsApp or email.')

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / Contact</div>
        <div class="eyebrow">Get in touch</div>
        <h1>Tell us what you need moved</h1>
        <p class="lead">{{ $settings['business_hours'] ?? '' }}</p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="split">
            <div>
                <h2>Send a booking request</h2>
                <p class="lead" style="margin-bottom:1.5rem">Fill this in and we will confirm availability and price. Nothing is charged here.</p>
                <div class="panel">
                    @include('partials.booking-fields', ['vehicles' => $vehicles, 'context' => 'contact'])
                </div>
            </div>

            <aside>
                <div class="panel">
                    <div class="eyebrow">Direct lines</div>
                    <ul class="checklist" style="color:var(--slate); font-size:.9375rem">
                        @if(! empty($settings['phone']))<li><a href="tel:{{ preg_replace('/\s+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a></li>@endif
                        @if(! empty($settings['email']))<li><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></li>@endif
                        @if(! empty($settings['whatsapp_number']))
                            <li><a href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">WhatsApp</a></li>
                        @endif
                    </ul>
                </div>

                @if($cities->isNotEmpty())
                    <div class="panel" style="margin-top:1.25rem">
                        <div class="eyebrow">Offices</div>
                        @foreach($cities as $city)
                            <div style="padding:.75rem 0; border-bottom:1px solid var(--line)">
                                <strong>{{ $city->name }}</strong>
                                <div style="color:var(--muted); font-size:.875rem">{{ $city->office_address }}</div>
                                @if($city->airport_branch)
                                    <div style="color:var(--muted); font-size:.875rem">{{ $city->airport_branch }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

@endsection
