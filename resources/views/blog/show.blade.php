@extends('layouts.public')
@section('title', ($post->meta_title ?: $post->title) . ' | ' . ($settings['site_name'] ?? ''))
@section('description', $post->meta_description ?: $post->excerpt)

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'author' => ['@type' => 'Organization', 'name' => $settings['site_name'] ?? ''],
    'datePublished' => optional($post->published_at)->toDateString(),
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('blog') }}">Blog</a> / {{ $post->category }}</div>
        <div class="eyebrow">{{ $post->category }}</div>
        <h1>{{ $post->title }}</h1>
        <p class="lead">{{ $post->excerpt }}</p>
        <div class="crumbs" style="margin:1.25rem 0 0">
            {{ optional($post->published_at)->format('j F Y') }} · {{ $post->read_minutes }} min read @if($post->author) · {{ $post->author }} @endif
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="split">
            <article>
                @if($post->cover_image)
                    <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
                         style="border-radius:var(--radius); margin-bottom:2rem; aspect-ratio:16/9; object-fit:cover; width:100%">
                @endif
                <div class="prose">{!! $post->body !!}</div>
            </article>

            <aside class="sticky">
                <div class="panel">
                    <div class="eyebrow">Ready to book</div>
                    <h3>Get a quote for this</h3>
                    <p style="color:var(--slate); font-size:.9375rem">Tell us the dates and city, and we will come back with the vehicle and price.</p>
                    <button class="btn btn--primary btn--block" data-book>Request a vehicle</button>
                    @if(! empty($settings['whatsapp_number']))
                        <a class="btn btn--ghost btn--block" style="margin-top:.5rem"
                           href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">WhatsApp us</a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@if($related->isNotEmpty())
<section class="section section--tight section--edge">
    <div class="shell">
        <div class="eyebrow">Keep reading</div>
        <div class="grid grid--3">
            @foreach($related as $item)
                <article class="card">
                    <div class="card__body">
                        <div class="postCard__cat">{{ $item->category }}</div>
                        <h3 style="font-size:1.0625rem">{{ $item->title }}</h3>
                        <p>{{ Str::limit($item->excerpt, 100) }}</p>
                        <div class="card__foot"><a class="btn btn--ghost btn--sm" href="{{ route('article', $item->slug) }}">Read →</a></div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
