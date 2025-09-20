<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    @if (app()->environment('local'))
        @vite(['resources/js/app.ts'])
    @else
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
            $entryName = 'resources/js/app.ts';
        @endphp

        @if ($manifest && isset($manifest[$entryName]))
            <script type="module" src="{{ '/build/' . $manifest[$entryName]['file'] }}"></script>
            @if (!empty($manifest[$entryName]['css']))
                @foreach ($manifest[$entryName]['css'] as $cssFile)
                    <link rel="stylesheet" href="{{ '/build/' . $cssFile }}">
                @endforeach
            @endif
        @else
            {{-- Fallback: make sure these filenames exist in public/build/assets/ --}}
            <link rel="stylesheet" href="{{ '/build/assets/app-DHXcDaMw.css' }}">
            <script type="module" src="{{ '/build/assets/app-BTFfPyZd.js' }}"></script>
        @endif
    @endif

    {{-- Ziggy routes --}}
    @routes
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
