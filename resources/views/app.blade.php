<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline script for system dark mode --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? "system" }}';
            if (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Background colors for light/dark --}}
    <style>
        html { background-color: oklch(1 0 0); }
        html.dark { background-color: oklch(0.145 0 0); }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @routes

    @if (app()->environment('local'))
        {{-- Local dev: load main app + current page --}}
        @vite([
            'resources/js/app.ts',
            "resources/js/pages/{$page['component']}.vue"
        ])
    @else
        {{-- Production: read manifest.json for hashed files --}}
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
            $entries = [
                'resources/js/app.ts',
                "resources/js/pages/{$page['component']}.vue"
            ];
        @endphp

        @if ($manifest)
            @foreach ($entries as $entry)
                @if (isset($manifest[$entry]))
                    {{-- JS Module --}}
                    <script type="module" src="{{ asset('build/' . $manifest[$entry]['file']) }}"></script>

                    {{-- CSS linked to this entry --}}
                    @if (!empty($manifest[$entry]['css']))
                        @foreach ($manifest[$entry]['css'] as $cssFile)
                            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
                        @endforeach
                    @endif
                @endif
            @endforeach
        @endif
    @endif

    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
