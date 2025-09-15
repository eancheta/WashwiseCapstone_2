@extends('layouts.blade')

@section('title', 'Customer Reviews')

@section('content')
<div class="p-6 bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#002B5C] mb-4 sm:mb-0">
            Customer Reviews
        </h1>

        <!-- Return Button -->
        <a href="{{ route('carwashownerdashboard') }}"
           class="px-6 py-3 bg-[#002B5C] text-white font-semibold rounded-xl shadow-md hover:bg-[#FF2D2D] transition transform hover:-translate-y-0.5">
            ← Return to Dashboard
        </a>
    </div>

    @if($shops->count() > 0)
        @foreach($shops as $shop)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 hover:shadow-2xl transition">
                <!-- Shop Header -->
                <div class="bg-gradient-to-r from-[#002B5C] to-[#00509E] text-white p-5 flex justify-between items-center">
                    <h2 class="text-2xl font-bold">{{ $shop->name }}</h2>
                    <span class="text-sm bg-white text-[#002B5C] px-3 py-1 rounded-full font-semibold shadow">
                        {{ $shop->feedback->count() }} Reviews
                    </span>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-6">{{ $shop->address }}</p>

                    @if($shop->feedback->count() > 0)
                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                            @foreach($shop->feedback as $review)
                                <div class="bg-gray-50 border-l-4 border-[#002B5C] p-4 rounded-lg hover:bg-gray-100 transition shadow-sm">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="font-semibold text-gray-800">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <p class="text-yellow-500 font-semibold">⭐ {{ $review->rating }}</p>
                                    </div>
                                    <p class="text-gray-700 mb-2">{{ $review->comment }}</p>
                                    <p class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6">No reviews yet for this shop.</p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-gray-500 text-center text-lg mt-12">You don’t own any shops yet.</p>
    @endif
</div>
@endsection
