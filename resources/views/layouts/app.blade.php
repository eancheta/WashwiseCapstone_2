<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    @routes
    @inertiaHead

    @if (app()->environment('local'))
        {{-- Dev: use Vite HMR --}}
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @else
        {{-- Prod: read manifest.json --}}
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
            $entryName = 'resources/js/app.ts';
        @endphp

        @if ($manifest && isset($manifest[$entryName]))
            <script type="module" src="{{ asset('build/' . $manifest[$entryName]['file']) }}"></script>
            @if (!empty($manifest[$entryName]['css']))
                @foreach ($manifest[$entryName]['css'] as $cssFile)
                    <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
                @endforeach
            @endif
        @endif
    @endif
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
