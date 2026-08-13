<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Sign in</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Public+Sans:wght@400;500;600&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

<div class="login">
    <div class="login__card">
        <h1 style="font-size:1.375rem">Sign in</h1>
        <p style="color:var(--a-muted); font-size:.9375rem; margin-top:0">Dashboard for {{ $settings['site_name'] ?? 'your website' }}.</p>

        @if($errors->any())
            <div class="errors">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="field" style="margin-bottom:1rem">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field" style="margin-bottom:1rem">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div class="check" style="margin-bottom:1.25rem">
                <input id="remember" type="checkbox" name="remember" value="1">
                <label for="remember">Keep me signed in</label>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Sign in</button>
        </form>
    </div>
</div>

</body>
</html>
