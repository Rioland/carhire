{{-- Standalone service page, e.g. /services/moving-truck --}}
@extends('layouts.public')
@section('title', $service->name . ' | ' . ($settings['site_name'] ?? ''))
@section('description', Str::limit($service->summary, 155))

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / Services / {{ $service->name }}</div>
        <div class="eyebrow">Service</div>
        <h1>{{ $service->name }}</h1>
        <p class="lead">{{ $service->summary }}</p>
        <div class="hero__actions" style="margin-top:1.5rem">
            <button class="btn btn--amber" data-book data-service="{{ $service->name }}">Request a quote</button>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="split">
            <div>
                @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}"
                         style="border-radius:var(--radius); margin-bottom:2rem; aspect-ratio:16/9; object-fit:cover; width:100%">
                @endif

                <div class="prose">
                    {!! $service->body ?: '<p>' . e($service->summary) . '</p>' !!}
                </div>

                @if($vehicles->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Vehicles for this service</h2>
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
                                                data-service="{{ $service->name }}">Book now</button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <h2 style="margin-top:2.5rem">Where we deliver</h2>
                @foreach($cities as $city)
                    <div class="dirArea">
                        <h3>{{ $city->name }}</h3>
                        <div class="dirLinks">
                            @foreach($city->locations as $location)
                                <a href="{{ route('location.service', $service->slug . '-' . $location->slug) }}">{{ $location->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($faqs->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Questions</h2>
                    <div class="faq" data-faq>
                        @foreach($faqs as $faq)
                            <div class="faq__item" data-open="false">
                                <button class="faq__q" aria-expanded="false">{{ $faq->question }}</button>
                                <div class="faq__a">{{ $faq->answer }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="sticky">
                <div class="panel">
                    <div class="eyebrow">Get started</div>
                    <h3>Book {{ strtolower($service->name) }}</h3>
                    @include('partials.booking-fields', ['vehicles' => $vehicles, 'context' => 'service'])
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection
