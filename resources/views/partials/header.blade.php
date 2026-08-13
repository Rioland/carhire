@php
    $brand = $settings['site_name'] ?? config('app.name');
    $whatsapp = preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? '');
@endphp

<header class="header">
    <div class="shell">
        <div class="header__bar">
            <a class="brand" href="{{ route('home') }}">
                @if(! empty($settings['logo']))
                    <img src="{{ asset('storage/' . $settings['logo']) }}" alt="{{ $brand }}">
                @else
                    <span class="brand__mark">{{ mb_substr($brand, 0, 2) }}</span>
                    <span>{{ $brand }}</span>
                @endif
            </a>

            <nav class="nav">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>

                @if($navServices->isNotEmpty())
                    <div class="nav__group" data-dropdown data-open="false">
                        <button class="nav__toggle" aria-expanded="false">Services ▾</button>
                        <div class="nav__menu">
                            @foreach($navServices as $service)
                                <a href="{{ route('service', $service->slug) }}">{{ $service->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="nav__group" data-dropdown data-open="false">
                    <button class="nav__toggle" aria-expanded="false">Locations ▾</button>
                    <div class="nav__menu">
                        @foreach($navCities as $city)
                            <a href="{{ route('city', $city->slug) }}">{{ $city->name }} car hire</a>
                        @endforeach
                        <a href="{{ route('directory') }}"><strong>All locations →</strong></a>
                    </div>
                </div>

                <a href="{{ route('blog') }}" @if(request()->routeIs('blog')) aria-current="page" @endif>Blog</a>
                <a href="{{ route('contact') }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
            </nav>

            <div class="header__cta">
                @if($whatsapp)
                    <a class="btn btn--primary btn--sm" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp</a>
                @endif
                <a class="btn btn--onDark btn--sm" href="tel:{{ preg_replace('/\s+/', '', $settings['phone'] ?? '') }}">Call</a>
            </div>

            <button class="burger" data-burger aria-label="Open menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>

        <div class="progress" data-progress aria-hidden="true"></div>

        <div class="mobileNav" data-mobile-nav data-open="false">
            <a href="{{ route('home') }}">Home</a>
            @foreach($navServices as $service)
                <a href="{{ route('service', $service->slug) }}">{{ $service->name }}</a>
            @endforeach
            <a href="{{ route('directory') }}">All locations</a>
            <a href="{{ route('blog') }}">Blog</a>
            <a href="{{ route('contact') }}">Contact</a>
            <button class="btn btn--primary btn--block" data-book>Request a vehicle</button>
        </div>
    </div>
</header>
