<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    {{-- Dev: use Vite for HMR --}}
    @if (app()->environment('local'))
        @vite(['resources/js/app.ts'])
    @else
        {{-- Prod: read manifest.json and use the real built filenames (root-relative to avoid protocol issues) --}}
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
            // Primary entry used by your app (matches what Vite manifest keys contain)
            $entryName = 'resources/js/app.ts';
        @endphp

        @if ($manifest && isset($manifest[$entryName]))
            {{-- JS module entry --}}
            <script type="module" src="{{ '/build/' . $manifest[$entryName]['file'] }}"></script>

            {{-- CSS files linked to the entry (if any) --}}
            @if (!empty($manifest[$entryName]['css']))
                @foreach ($manifest[$entryName]['css'] as $cssFile)
                    <link rel="stylesheet" href="{{ '/build/' . $cssFile }}">
                @endforeach
            @endif
        @else
            {{-- Fallback (use exact filenames from your build if manifest cannot be read) --}}
            <link rel="stylesheet" href="{{ '/build/assets/app-DHXcDaMw.css' }}">
            <script type="module" src="{{ '/build/assets/app-BTFfPyZd.js' }}"></script>
        @endif
    @endif

    {{-- Inertia --}}
    @routes
    @vite('resources/js/app.ts')
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
