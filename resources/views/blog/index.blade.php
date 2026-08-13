@extends('layouts.public')
@section('title', 'Blog | ' . ($settings['site_name'] ?? ''))
@section('description', 'Guides on car hire, logistics and vehicle documentation in Nigeria.')

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / Blog</div>
        <div class="eyebrow">Guides &amp; resources</div>
        <h1>Everything worth knowing before you hire</h1>
        <p class="lead">Costs, comparisons, checklists and paperwork, written from the dispatch desk.</p>
    </div>
</section>

@if($featured && ! request()->hasAny(['q', 'category']))
<section class="section section--tight">
    <div class="shell">
        <article class="featured">
            @if($featured->cover_image)
                <img src="{{ asset('storage/' . $featured->cover_image) }}" alt="{{ $featured->title }}">
            @endif
            <div>
                <div class="eyebrow">Featured · {{ $featured->category }}</div>
                <h2>{{ $featured->title }}</h2>
                <p class="lead">{{ $featured->excerpt }}</p>
                <a class="btn btn--primary" href="{{ route('article', $featured->slug) }}">Read the guide →</a>
            </div>
        </article>
    </div>
</section>
@endif

<section class="section section--tight section--edge">
    <div class="shell">
        <form method="GET" class="filters" style="align-items:center">
            <a class="filter" href="{{ route('blog') }}" aria-pressed="{{ $activeCategory ? 'false' : 'true' }}">All</a>
            @foreach($categories as $category)
                <a class="filter" href="{{ route('blog', ['category' => $category]) }}"
                   aria-pressed="{{ $activeCategory === $category ? 'true' : 'false' }}">{{ $category }}</a>
            @endforeach
            <span style="flex:1"></span>
            @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
            <input type="search" name="q" value="{{ $term }}" placeholder="Search articles" style="max-width:240px">
        </form>

        <div class="grid grid--3 stagger">
            @forelse($posts as $post)
                <article class="card">
                    @if($post->cover_image)
                        <div class="card__media"><img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy"></div>
                    @endif
                    <div class="card__body">
                        <div class="postCard__cat">{{ $post->category }}</div>
                        <h3>{{ $post->title }}</h3>
                        <p>{{ Str::limit($post->excerpt, 130) }}</p>
                        <div class="card__foot">
                            <div class="postCard__meta" style="margin-bottom:.6rem">
                                {{ optional($post->published_at)->format('M Y') }} · {{ $post->read_minutes }} min read
                            </div>
                            <a class="btn btn--ghost btn--sm" href="{{ route('article', $post->slug) }}">Read article →</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="emptyState" style="grid-column:1/-1">
                    Nothing matches that search yet. Try another term or clear the filters.
                </div>
            @endforelse
        </div>

        <div class="pagination">{{ $posts->links() }}</div>
    </div>
</section>

@endsection
