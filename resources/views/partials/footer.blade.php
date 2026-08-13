@php
    $brand = $settings['site_name'] ?? config('app.name');
    $socials = array_filter([
        'FB' => $settings['facebook_url'] ?? null,
        'IG' => $settings['instagram_url'] ?? null,
        'X'  => $settings['twitter_url'] ?? null,
        'TT' => $settings['tiktok_url'] ?? null,
    ]);
@endphp

<footer class="footer">
    <div class="shell">
        <div class="footer__grid">
            <div>
                <h4>{{ $brand }}</h4>
                <p style="max-width:34ch">{{ $settings['tagline'] ?? '' }}</p>
                @if($socials)
                    <div class="social">
                        @foreach($socials as $label => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}">{{ $label }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('home') }}#fleet">Our fleet</a></li>
                    <li><a href="{{ route('blog') }}">Blog</a></li>
                    <li><a href="{{ route('home') }}#faq">FAQ</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4>Locations</h4>
                <ul>
                    @foreach($navCities as $city)
                        <li><a href="{{ route('city', $city->slug) }}">{{ $city->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('directory') }}">All locations →</a></li>
                </ul>
            </div>

            <div>
                <h4>Contact</h4>
                <ul>
                    @if(! empty($settings['phone']))
                        <li><a href="tel:{{ preg_replace('/\s+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a></li>
                    @endif
                    @if(! empty($settings['email']))
                        <li><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></li>
                    @endif
                    @if(! empty($settings['address_primary']))<li>{{ $settings['address_primary'] }}</li>@endif
                    @if(! empty($settings['address_secondary']))<li>{{ $settings['address_secondary'] }}</li>@endif
                    @if(! empty($settings['business_hours']))<li>{{ $settings['business_hours'] }}</li>@endif
                </ul>
            </div>
        </div>

        <div class="footer__bottom">
            <span>&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</span>
            <span>
                @foreach($footerPages as $page)
                    <a href="{{ route('page', $page->slug) }}">{{ $page->title }}</a>@if(! $loop->last) &nbsp;·&nbsp; @endif
                @endforeach
            </span>
        </div>
    </div>
</footer>
