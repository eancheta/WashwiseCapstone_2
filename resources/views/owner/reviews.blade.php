@extends('layouts.blade')

@section('title', 'Customer Reviews')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Customer Reviews</h1>

        <!-- 🔹 Return Button -->
        <a href="{{ route('carwashownerdashboard') }}"
           class="px-4 py-2 bg-[#002B5C] text-white font-semibold rounded-lg shadow hover:bg-[#003366] transition">
            ← Return to Dashboard
        </a>
    </div>

    @if($shops->count() > 0)
        @foreach($shops as $shop)
            <div class="bg-white p-6 shadow rounded-xl mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $shop->name }}
                </h2>
                <p class="text-gray-600 mb-4">{{ $shop->address }}</p>

                @if($shop->feedback->count() > 0)
                    <div class="space-y-4">
                        @foreach($shop->feedback as $review)
                            <div class="border-b pb-3 last:border-none">
                                <p class="font-medium text-gray-800">
                                    {{ $review->user->name ?? 'Anonymous' }}
                                </p>
                                <p class="text-yellow-500">⭐ {{ $review->rating }}</p>
                                <p class="text-gray-700">{{ $review->comment }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $review->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No reviews yet for this shop.</p>
                @endif
            </div>
        @endforeach
    @else
        <p class="text-gray-500">You don’t own any shops yet.</p>
    @endif
</div>
@endsection
