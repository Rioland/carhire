@extends('layouts.public')
@section('title', 'All locations | ' . ($settings['site_name'] ?? ''))
@section('description', 'Every area we deliver vehicles to, and the services available in each one.')

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / Locations</div>
        <div class="eyebrow">Coverage</div>
        <h1>Every area we deliver to</h1>
        <p class="lead">Pick your area to see what is available nearby, or message us and we will bring the vehicle to you.</p>
    </div>
</section>

<section class="section">
    <div class="shell">
        @forelse($cities as $city)
            <div class="dirBlock">
                <div class="sectionHead" style="margin-bottom:.5rem">
                    <div>
                        <div class="eyebrow">{{ $city->state }}</div>
                        <h2 style="margin-bottom:.25rem">{{ $city->name }}</h2>
                        <p style="color:var(--muted); margin:0">{{ $city->locations->count() }} areas · {{ $services->count() }} services</p>
                    </div>
                    <a class="btn btn--ghost btn--sm" href="{{ route('city', $city->slug) }}">{{ $city->name }} overview →</a>
                </div>

                @foreach($city->locations as $location)
                    <div class="dirArea">
                        <h3>{{ $location->name }}</h3>
                        <div class="dirLinks">
                            @foreach($services as $service)
                                <a href="{{ route('location.service', $service->slug . '-' . $location->slug) }}">
                                    {{ $service->link_label ?: $service->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="emptyState">No locations published yet. Add cities and areas from the dashboard.</div>
        @endforelse
    </div>
</section>

@endsection
