<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | {{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    <main style="font-family: sans-serif; text-align: center; padding: 4rem 1rem;">
        <h1 style="font-size: 4rem; margin: 0;">404</h1>
        <p>Page not found.</p>
        <p><a href="{{ url('/') }}">Return to home</a></p>
    </main>
</body>
</html>
