@extends('layouts.public')

@section('title', $settings['meta_title'] ?? ($settings['site_name'] ?? 'Car hire'))
@section('description', $settings['meta_description'] ?? '')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'AutoRental',
    'name' => $settings['site_name'] ?? '',
    'description' => $settings['meta_description'] ?? '',
    'telephone' => $settings['phone'] ?? '',
    'email' => $settings['email'] ?? '',
    'url' => url('/'),
    'areaServed' => $cities->pluck('name')->all(),
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => $settings['stat_rating'] ?? '4.9',
        'reviewCount' => max($testimonials->count(), 1),
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

{{-- ============================ Hero ============================ --}}
<section class="hero">
    <div class="hero__media">
        @if(! empty($settings['hero_video']))
            <video autoplay muted loop playsinline poster="{{ ! empty($settings['hero_image']) ? asset('storage/' . $settings['hero_image']) : '' }}">
                <source src="{{ $settings['hero_video'] }}" type="video/mp4">
            </video>
        @elseif(! empty($settings['hero_image']))
            <img src="{{ asset('storage/' . $settings['hero_image']) }}" alt="">
        @endif
    </div>

    <div class="shell">
        <div class="hero__inner">
            <div>
                <div class="eyebrow">{{ $settings['hero_eyebrow'] ?? 'Nationwide' }}</div>
                <h1>{{ $settings['hero_heading'] ?? 'Vehicles and drivers you can plan around' }}</h1>
                <p class="hero__sub">{{ $settings['hero_subheading'] ?? '' }}</p>

                <div class="hero__actions">
                    <button class="btn btn--amber" data-book>Request a vehicle</button>
                    @if(! empty($settings['whatsapp_number']))
                        <a class="btn btn--onDark" href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">Message on WhatsApp</a>
                    @endif
                </div>

                <div class="hero__plates">
                    @foreach($services->take(4) as $service)
                        <span class="plate plate--dark">
                            <span class="plate__tab">SVC</span>
                            <span class="plate__value">{{ $service->name }}</span>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="dispatch">
                <div class="dispatch__head">
                    <h2>Dispatch desk</h2>
                    <span>Replies in minutes</span>
                </div>
                @include('partials.booking-fields', ['vehicles' => $vehicles, 'context' => 'hero'])
            </div>
        </div>
    </div>
</section>

{{-- ============================ Fleet ============================ --}}
<section class="section" id="fleet">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">The fleet</div>
                <h2>Pick the vehicle, we handle the rest</h2>
                <p class="lead">{{ $settings['fleet_note'] ?? '' }}</p>
            </div>
            @if(! empty($settings['whatsapp_number']))
                <a class="btn btn--ghost" href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">Ask about a vehicle</a>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="filters">
                <button class="filter" data-filter="all" aria-pressed="true">All</button>
                @foreach($categories as $category)
                    <button class="filter" data-filter="{{ $category->slug }}" aria-pressed="false">{{ $category->name }}</button>
                @endforeach
            </div>
        @endif

        <div class="grid grid--4">
            @forelse($vehicles as $vehicle)
                <article class="card" data-category="{{ $vehicle->category?->slug ?? 'other' }}">
                    <div class="card__media @if(! $vehicle->image) card__media--empty @endif">
                        @if($vehicle->image)
                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->name }}" loading="lazy">
                        @else
                            Add photo
                        @endif
                        @if($vehicle->seats)
                            <span class="plate card__tag">
                                <span class="plate__tab">SEATS</span>
                                <span class="plate__value">{{ $vehicle->seats }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="card__body">
                        <h3>{{ $vehicle->name }}</h3>
                        @if($vehicle->category)
                            <div class="card__meta">{{ $vehicle->category->name }}</div>
                        @endif

                        <div class="card__rates">
                            <span class="plate">
                                <span class="plate__tab">{{ mb_strtoupper(mb_substr($vehicle->daily_label ?: 'Day', 0, 3)) }}</span>
                                <span class="plate__value">{{ $vehicle->priceLabel() }}</span>
                            </span>
                            @if($vehicle->secondaryPriceLabel())
                                <span class="plate plate--muted">
                                    <span class="plate__tab">{{ mb_strtoupper(mb_substr($vehicle->secondary_label ?: 'Alt', 0, 3)) }}</span>
                                    <span class="plate__value">{{ $vehicle->secondaryPriceLabel() }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="card__foot">
                            <button class="btn btn--primary btn--sm btn--block" data-book
                                    data-vehicle-id="{{ $vehicle->id }}"
                                    data-vehicle-name="{{ $vehicle->name }}">Book now</button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="emptyState">No vehicles yet. Add your first one from the dashboard.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================ Services ============================ --}}
@if($services->isNotEmpty())
<section class="section section--dark">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">Beyond car hire</div>
                <h2>Everything else we move</h2>
                <p class="lead">Trucks, escorts, interstate runs and long-term contracts run out of the same dispatch desk.</p>
            </div>
        </div>

        <div class="grid grid--3">
            @foreach($services as $service)
                <article class="card" style="background:var(--asphalt-2); border-color:var(--line-dark)">
                    @if($service->image)
                        <div class="card__media"><img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" loading="lazy"></div>
                    @endif
                    <div class="card__body">
                        <h3 style="color:#fff">{{ $service->name }}</h3>
                        <p style="color:#a8b2b7; font-size:.9375rem; margin:0">{{ $service->summary }}</p>
                        <div class="card__foot">
                            <a class="btn btn--onDark btn--sm" href="{{ route('service', $service->slug) }}">See {{ strtolower($service->name) }} →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================ About + stats ============================ --}}
<section class="section section--dark section--edge" style="background:var(--asphalt-2)">
    <div class="shell">
        <div class="split">
            <div class="reveal">
                <div class="eyebrow">Why us</div>
                <h2>{{ $settings['about_heading'] ?? '' }}</h2>
                <p class="lead">{{ $settings['about_body'] ?? '' }}</p>

                <div class="stats" style="margin-top:2rem">
                    <div class="stat">
                        <div class="stat__value">{{ $settings['stat_years'] ?? '—' }}</div>
                        <div class="stat__label">Years running</div>
                    </div>
                    <div class="stat">
                        <div class="stat__value">{{ $settings['stat_clients'] ?? '—' }}</div>
                        <div class="stat__label">Clients served</div>
                    </div>
                    <div class="stat">
                        <div class="stat__value">{{ $settings['stat_vehicles'] ?? '—' }}</div>
                        <div class="stat__label">Vehicles</div>
                    </div>
                    <div class="stat">
                        <div class="stat__value">{{ $settings['stat_rating'] ?? '—' }}</div>
                        <div class="stat__label">Average rating</div>
                    </div>
                </div>
            </div>

            <div class="panel panel--dark reveal">
                <h3>What every booking includes</h3>
                <ul class="checklist" style="margin-top:1rem">
                    @foreach(preg_split('/\r\n|\r|\n/', (string) ($settings['trust_points'] ?? '')) as $point)
                        @if(trim($point))<li>{{ trim($point) }}</li>@endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ============================ Cities ============================ --}}
@if($cities->isNotEmpty())
<section class="section" id="locations">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">Where we operate</div>
                <h2>Delivery across every city we cover</h2>
            </div>
            <a class="btn btn--ghost" href="{{ route('directory') }}">Browse all areas</a>
        </div>

        <div class="grid grid--2">
            @foreach($cities as $city)
                <article class="cityCard">
                    <div class="cityCard__top">
                        <div>
                            <h3>{{ $city->name }}</h3>
                            <div class="cityCard__state">{{ $city->state }}</div>
                        </div>
                        @if($city->rating)
                            <span class="plate">
                                <span class="plate__tab">RATED</span>
                                <span class="plate__value">{{ $city->rating }}</span>
                            </span>
                        @endif
                    </div>

                    @if($city->areas_summary)
                        <div class="card__meta">{{ $city->areas_summary }}</div>
                    @endif

                    @if($city->highlightList())
                        <ul>
                            @foreach(array_slice($city->highlightList(), 0, 4) as $highlight)
                                <li>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="cityCard__links">
                        <a class="btn btn--primary btn--sm" href="{{ route('city', $city->slug) }}">{{ $city->name }} fleet →</a>
                        <button class="btn btn--ghost btn--sm" data-book data-service="{{ $city->name }} hire">Book here</button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================ Reviews ============================ --}}
@if($testimonials->isNotEmpty())
<section class="section section--dark" id="reviews">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">{{ $settings['stat_rating'] ?? '4.9' }} average</div>
                <h2>What clients say afterwards</h2>
            </div>
            <div class="carouselNav">
                <button data-carousel-prev aria-label="Previous reviews">←</button>
                <button data-carousel-next aria-label="Next reviews">→</button>
            </div>
        </div>

        <div class="reviews" data-carousel>
            @foreach($testimonials as $testimonial)
                <article class="review">
                    <div class="review__stars">{{ str_repeat('★', $testimonial->rating) }}</div>
                    <p class="review__quote">{{ $testimonial->quote }}</p>
                    <div class="review__who">
                        <div class="review__avatar">{{ $testimonial->initials() }}</div>
                        <div>
                            <div class="review__name">{{ $testimonial->name }}</div>
                            <div class="review__role">{{ $testimonial->role }}@if($testimonial->service) · {{ $testimonial->service }}@endif</div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================ FAQ ============================ --}}
@if($faqs->isNotEmpty())
<section class="section" id="faq">
    <div class="shell">
        <div class="split">
            <div>
                <div class="eyebrow">Questions</div>
                <h2>Before you book</h2>
                <div class="faq" data-faq>
                    @foreach($faqs as $faq)
                        <div class="faq__item" data-open="{{ $loop->first ? 'true' : 'false' }}">
                            <button class="faq__q" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">{{ $faq->question }}</button>
                            <div class="faq__a">{{ $faq->answer }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="sticky">
                <div class="panel">
                    <div class="eyebrow">Still unsure</div>
                    <h3>Talk to a person</h3>
                    <p style="color:var(--slate); font-size:.9375rem">Tell us the trip and we will recommend the vehicle and the price. No obligation.</p>
                    @if(! empty($settings['phone']))
                        <a class="btn btn--ghost btn--block" style="margin-bottom:.5rem" href="tel:{{ preg_replace('/\s+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a>
                    @endif
                    <button class="btn btn--primary btn--block" data-book>Request a callback</button>
                </div>
            </aside>
        </div>
    </div>
</section>

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($faq) => [
        '@type' => 'Question',
        'name' => $faq->question,
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
    ])->all(),
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif

{{-- ============================ Blog teaser ============================ --}}
@if($posts->isNotEmpty())
<section class="section section--tight section--edge">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">Guides</div>
                <h2>Worth reading before you hire</h2>
            </div>
            <a class="btn btn--ghost" href="{{ route('blog') }}">All articles</a>
        </div>

        <div class="grid grid--4">
            @foreach($posts as $post)
                <article class="card">
                    @if($post->cover_image)
                        <div class="card__media"><img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy"></div>
                    @endif
                    <div class="card__body">
                        <div class="postCard__cat">{{ $post->category }}</div>
                        <h3 style="font-size:1rem">{{ $post->title }}</h3>
                        <p>{{ Str::limit($post->excerpt, 90) }}</p>
                        <div class="card__foot">
                            <a class="btn btn--ghost btn--sm" href="{{ route('article', $post->slug) }}">Read →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================ Contact ============================ --}}
<section class="section section--dark" id="contact">
    <div class="shell">
        <div class="sectionHead reveal">
            <div>
                <div class="eyebrow">Contact</div>
                <h2>Reach the dispatch desk</h2>
                <p class="lead">{{ $settings['business_hours'] ?? '' }}</p>
            </div>
        </div>

        <div class="grid grid--3">
            @if(! empty($settings['whatsapp_number']))
                <div class="contactCard">
                    <h3>WhatsApp</h3>
                    <p>Fastest route to a confirmed booking.</p>
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">Start a chat</a>
                </div>
            @endif
            @if(! empty($settings['phone']))
                <div class="contactCard">
                    <h3>Phone</h3>
                    <p>For urgent or same-day requests.</p>
                    <a href="tel:{{ preg_replace('/\s+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a>
                </div>
            @endif
            @if(! empty($settings['email']))
                <div class="contactCard">
                    <h3>Email</h3>
                    <p>Quotes, invoices and corporate accounts.</p>
                    <a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
