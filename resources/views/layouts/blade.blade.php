<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- Your app CSS/JS (compiled with Vite or plain) --}}
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        {{-- Navbar --}}
        <nav class="bg-white shadow mb-6 p-4">
            <a href="/" class="font-bold text-lg">{{ config('app.name', 'Laravel') }}</a>
        </nav>

        {{-- Page Content --}}
        <main class="container mx-auto px-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
