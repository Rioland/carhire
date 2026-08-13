<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

@php
    $brand = $settings['site_name'] ?? config('app.name');
    $pageTitle = trim($__env->yieldContent('title')) ?: ($settings['meta_title'] ?? $brand);
    $pageDescription = trim($__env->yieldContent('description')) ?: ($settings['meta_description'] ?? '');
    $shareImage = ($settings['og_image'] ?? null) ? asset('storage/' . $settings['og_image']) : null;
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
@if(! empty($settings['meta_keywords']))
    <meta name="keywords" content="{{ $settings['meta_keywords'] }}">
@endif
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($shareImage)
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $shareImage }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Public+Sans:wght@400;500;600&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">

@stack('head')
@if(! empty($settings['analytics_head']))
    {!! $settings['analytics_head'] !!}
@endif
</head>
<body>

@include('partials.header')

<main>
    @yield('content')
</main>

@include('partials.footer')
@include('partials.booking-modal')

@if(! empty($settings['whatsapp_number']))
    <a class="floatChat" href="https://wa.me/{{ preg_replace('/\D+/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener">
        ✆ <span>WhatsApp us</span>
    </a>
@endif

<script src="{{ asset('assets/js/site.js') }}" defer></script>
@stack('scripts')
</body>
</html>
