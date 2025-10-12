@extends('layouts.blade')

@section('title', 'Customer Reviews')

@section('content')
<div class="p-4 sm:p-6 bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3">
        <h1 class="text-2xl sm:text-4xl font-extrabold text-[#002B5C] leading-tight">
            Customer Reviews
        </h1>

        <!-- Return Button -->
        <a href="{{ route('carwashownerdashboard') }}"
           class="w-full sm:w-auto text-center px-5 py-2 sm:px-6 sm:py-3 bg-[#002B5C] text-white font-semibold rounded-xl shadow-md hover:bg-[#FF2D2D] transition-all">
            ← Return to Dashboard
        </a>
    </div>

    @if($shops->count() > 0)
        @foreach($shops as $shop)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all overflow-hidden mb-6 sm:mb-8">
                <!-- Shop Header -->
                <div class="bg-gradient-to-r from-[#002B5C] to-[#00509E] text-white p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <h2 class="text-xl sm:text-2xl font-bold">{{ $shop->name }}</h2>
                    <span class="text-xs sm:text-sm bg-white text-[#002B5C] px-3 py-1 rounded-full font-semibold shadow">
                        {{ $shop->feedback->count() }} Reviews
                    </span>
                </div>

                <div class="p-4 sm:p-6">
                    <p class="text-gray-600 text-sm sm:text-base mb-5">{{ $shop->address }}</p>

                    @if($shop->feedback->count() > 0)
                        <div class="space-y-3 sm:space-y-4 max-h-80 sm:max-h-96 overflow-y-auto pr-1 sm:pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            @foreach($shop->feedback as $review)
                                <div class="bg-gray-50 border-l-4 border-[#002B5C] p-3 sm:p-4 rounded-lg hover:bg-gray-100 transition shadow-sm">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-1">
                                        <p class="font-semibold text-gray-800 text-sm sm:text-base">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <p class="text-yellow-500 font-semibold text-sm sm:text-base mt-1 sm:mt-0">⭐ {{ $review->rating }}</p>
                                    </div>
                                    <p class="text-gray-700 text-sm sm:text-base mb-1">{{ $review->comment }}</p>
                                    <p class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4 sm:py-6 text-sm sm:text-base">
                            No reviews yet for this shop.
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-gray-500 text-center text-base sm:text-lg mt-12">You don’t own any shops yet.</p>
    @endif
</div>
@endsection
