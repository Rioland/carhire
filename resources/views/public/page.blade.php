@extends('layouts.public')
@section('title', ($page->meta_title ?: $page->title) . ' | ' . ($settings['site_name'] ?? ''))
@section('description', $page->meta_description ?: '')

@section('content')

<section class="pageHead">
    <div class="shell">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / {{ $page->title }}</div>
        <h1>{{ $page->title }}</h1>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="prose">{!! $page->body !!}</div>
    </div>
</section>

@endsection
