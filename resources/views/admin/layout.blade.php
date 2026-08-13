<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Dashboard') · {{ $settings['site_name'] ?? 'Admin' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Public+Sans:wght@400;500;600&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

@php
    $brand = $settings['site_name'] ?? 'Admin';
    $pendingBookings = \App\Models\Booking::where('status', 'new')->count();
@endphp

<div class="app">
    <aside class="sidebar">
        <a class="sidebar__brand" href="{{ route('admin.dashboard') }}">
            <span class="sidebar__mark">{{ mb_substr($brand, 0, 2) }}</span>
            <span>Dashboard</span>
        </a>
        <div class="sidebar__site"><a href="{{ route('home') }}" target="_blank" rel="noopener">View the website →</a></div>

        <nav>
            <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Overview</a>
            <a href="{{ route('admin.bookings.index') }}" @if(request()->routeIs('admin.bookings.*')) aria-current="page" @endif>
                Bookings
                @if($pendingBookings) <span class="pill">{{ $pendingBookings }}</span> @endif
            </a>
        </nav>

        <div class="sidebar__label">Content</div>
        <nav>
            @foreach(config('admin') as $key => $section)
                <a href="{{ route('admin.resource.index', $key) }}"
                   @if(request()->route('resource') === $key) aria-current="page" @endif>{{ $section['label'] }}</a>
            @endforeach
        </nav>

        <div class="sidebar__label">Configuration</div>
        <nav>
            <a href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif>Site settings</a>
        </nav>

        <div class="sidebar__foot">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Sign out ({{ auth()->user()->name }})</button>
            </form>
        </div>
    </aside>

    <main class="main">
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>Fix these first:</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
