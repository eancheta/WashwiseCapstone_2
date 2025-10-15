<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user('carwashowner'),
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('flash.success') ?? $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('flash.error') ?? $request->session()->get('error'),
            ],
        ]);
    }

    public function resolveComponent(string $component): string
    {
        return str_replace('.', '/', 'pages.' . $component);
    }
}
