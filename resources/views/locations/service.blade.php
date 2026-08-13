{{-- Programmatic page: one service in one area, e.g. /locations/moving-truck-lekki-phase-1 --}}
@extends('layouts.public')

@php $headline = $service->headlineFor($location); @endphp

@section('title', $headline . ' | ' . ($settings['site_name'] ?? ''))
@section('description', Str::limit(($service->summary ?: '') . ' Available in ' . $location->name . ', ' . ($city->name ?? '') . '.', 155))

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs">
            <a href="{{ route('home') }}">Home</a> /
            <a href="{{ route('directory') }}">Locations</a> /
            <a href="{{ route('city', $city->slug) }}">{{ $city->name }}</a> /
            {{ $location->name }}
        </div>
        <div class="eyebrow">{{ $service->name }}</div>
        <h1>{{ $headline }}</h1>
        <p class="lead">{{ $service->summary }}</p>
        <div class="hero__actions" style="margin-top:1.5rem">
            <button class="btn btn--amber" data-book data-service="{{ $headline }}">Request this now</button>
            @if(! empty($settings['whatsapp_number']))
                <a class="btn btn--onDark" target="_blank" rel="noopener"
                   href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}?text={{ rawurlencode('Hello, I need ' . $headline) }}">WhatsApp</a>
            @endif
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="split">
            <div>
                <div class="prose">
                    <p>
                        We deliver {{ strtolower($service->name) }} anywhere in {{ $location->name }}, and across the rest of
                        {{ $city->name }}. Give us the date and pickup point and the vehicle arrives ready, fuelled and with a
                        driver who knows the area.
                        @if($location->landmarks)
                            We run regularly around {{ $location->landmarks }}.
                        @endif
                    </p>

                    @if($location->blurb)
                        <p>{{ $location->blurb }}</p>
                    @endif

                    @if($service->body)
                        {!! $service->body !!}
                    @endif

                    <h2>How booking works</h2>
                    <ol>
                        <li>Send the date, pickup point and how long you need the vehicle.</li>
                        <li>We confirm availability and the exact price, with nothing hidden.</li>
                        <li>The vehicle is delivered to your address in {{ $location->name }}.</li>
                    </ol>
                </div>

                @if($vehicles->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Vehicles available in {{ $location->name }}</h2>
                    <div class="grid grid--3 stagger">
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
                                                data-service="{{ $headline }}">Book now</button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                @if($faqs->isNotEmpty())
                    <h2 style="margin-top:2.5rem">Common questions</h2>
                    <div class="faq" data-faq>
                        @foreach($faqs as $faq)
                            <div class="faq__item" data-open="false">
                                <button class="faq__q" aria-expanded="false">{{ $faq->question }}</button>
                                <div class="faq__a"><div class="faq__aInner">{{ $faq->answer }}</div></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="sticky">
                <div class="panel">
                    <div class="eyebrow">{{ $location->name }}</div>
                    <h3>Book {{ strtolower($service->name) }}</h3>
                    @include('partials.booking-fields', ['vehicles' => $vehicles, 'context' => 'area'])
                </div>

                @if($siblings->isNotEmpty())
                    <div class="panel" style="margin-top:1.25rem">
                        <div class="eyebrow">Also in {{ $location->name }}</div>
                        <div class="dirLinks">
                            @foreach($siblings as $sibling)
                                <a href="{{ route('location.service', $sibling->slug . '-' . $location->slug) }}">{{ $sibling->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($nearby->isNotEmpty())
                    <div class="panel" style="margin-top:1.25rem">
                        <div class="eyebrow">Nearby areas</div>
                        <div class="dirLinks">
                            @foreach($nearby as $area)
                                <a href="{{ route('location.service', $service->slug . '-' . $area->slug) }}">{{ $area->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

@endsection
