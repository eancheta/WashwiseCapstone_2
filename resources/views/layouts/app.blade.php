<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if(isset($page) && isset($page['props']['title']))
            {{ $page['props']['title'] }}
        @elseif(isset($pageTitle))
            {{ $pageTitle }}
        @else
            WashWise
        @endif
    </title>

    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        $entryName = 'resources/js/app.ts';
    @endphp

    {{-- Load CSS from manifest --}}
    @if ($manifest && isset($manifest[$entryName]) && !empty($manifest[$entryName]['css']))
        @foreach ($manifest[$entryName]['css'] as $cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endforeach
    @endif

    {{-- Load JS only on Inertia pages --}}
    @if (isset($page) && $manifest && isset($manifest[$entryName]))
        <script type="module" src="{{ asset('build/' . $manifest[$entryName]['file']) }}"></script>
    @endif

    @routes

    {{-- Only include Inertia head if Inertia is used --}}
    @if (isset($page))
        @inertiaHead
    @endif
</head>
<body class="font-sans antialiased">
    @if (isset($page))
        {{-- Inertia SPA root --}}
        @inertia
    @else
        {{-- Traditional Blade content --}}
        @yield('content')
    @endif
</body>
</html>
