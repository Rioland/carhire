@extends('layouts.public')
@section('title', $city->meta_title ?: ('Car hire in ' . $city->name . ' | ' . ($settings['site_name'] ?? '')))
@section('description', $city->meta_description ?: $city->tagline)

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('directory') }}">Locations</a> / {{ $city->name }}</div>
        <div class="eyebrow">{{ $city->state }}</div>
        <h1>Car hire in {{ $city->name }}</h1>
        <p class="lead">{{ $city->tagline }}</p>
        <div class="hero__plates" style="margin-top:1.5rem">
            @if($city->office_address)
                <span class="plate plate--dark"><span class="plate__tab">OFFICE</span><span class="plate__value">{{ $city->office_address }}</span></span>
            @endif
            @if($city->airport_branch)
                <span class="plate plate--dark"><span class="plate__tab">AIR</span><span class="plate__value">{{ $city->airport_branch }}</span></span>
            @endif
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="split">
            <div>
                @if($city->intro)
                    <div class="prose"><p>{{ $city->intro }}</p></div>
                @endif

                @if($city->highlightList())
                    <h2 style="margin-top:2rem">What we handle in {{ $city->name }}</h2>
                    <ul class="checklist" style="color:var(--slate)">
                        @foreach($city->highlightList() as $highlight)<li>{{ $highlight }}</li>@endforeach
                    </ul>
                @endif

                @if($city->locations->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Areas we deliver to</h2>
                    <div class="dirLinks">
                        @foreach($city->locations as $location)
                            @php $primary = $services->first(); @endphp
                            <a href="{{ $primary ? route('location.service', $primary->slug . '-' . $location->slug) : route('directory') }}">{{ $location->name }}</a>
                        @endforeach
                    </div>
                @endif

                @if($vehicles->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Available in {{ $city->name }}</h2>
                    <div class="grid grid--3">
                        @foreach($vehicles as $vehicle)
                            <article class="card">
                                @if($vehicle->image)
                                    <div class="card__media"><img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->name }}" loading="lazy"></div>
                                @endif
                                <div class="card__body">
                                    <h3 style="font-size:1rem">{{ $vehicle->name }}</h3>
                                    <div class="card__rates">
                                        <span class="plate"><span class="plate__tab">DAY</span><span class="plate__value">{{ $vehicle->priceLabel() }}</span></span>
                                    </div>
                                    <div class="card__foot">
                                        <button class="btn btn--primary btn--sm btn--block" data-book
                                                data-vehicle-id="{{ $vehicle->id }}" data-vehicle-name="{{ $vehicle->name }}"
                                                data-service="{{ $city->name }}">Book now</button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="sticky">
                <div class="panel">
                    <div class="eyebrow">{{ $city->name }} desk</div>
                    <h3>Book a vehicle here</h3>
                    @include('partials.booking-fields', ['vehicles' => $vehicles, 'context' => 'city'])
                </div>
            </aside>
        </div>
    </div>
</section>

@if($testimonials->isNotEmpty())
<section class="section section--dark">
    <div class="shell">
        <div class="eyebrow">Client feedback</div>
        <div class="reviews" data-carousel>
            @foreach($testimonials as $testimonial)
                <article class="review">
                    <div class="review__stars">{{ str_repeat('★', $testimonial->rating) }}</div>
                    <p class="review__quote">{{ $testimonial->quote }}</p>
                    <div class="review__who">
                        <div class="review__avatar">{{ $testimonial->initials() }}</div>
                        <div>
                            <div class="review__name">{{ $testimonial->name }}</div>
                            <div class="review__role">{{ $testimonial->role }}</div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
