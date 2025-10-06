<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Washwise') }}</title>

    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        $entryName = 'resources/js/app.ts';
    @endphp

    @if ($manifest && isset($manifest[$entryName]))
        {{-- JS --}}
        <script type="module" src="{{ asset('build/' . $manifest[$entryName]['file']) }}"></script>

        {{-- CSS --}}
        @if (!empty($manifest[$entryName]['css']))
            @foreach ($manifest[$entryName]['css'] as $cssFile)
                <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
            @endforeach
        @endif
    @else
        {{-- Fallback --}}
        <link rel="stylesheet" href="{{ asset('build/assets/app-DnjuimWl.css') }}">
        <script type="module" src="{{ asset('build/assets/app-p4oQ-QsI.js') }}"></script>
    @endif

    @routes
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
