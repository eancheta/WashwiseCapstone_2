@extends('layouts.blade')
<style>
    /* Hide navbar/header only for this page */
    nav, header {
        display: none !important;
    }
</style>

@section('content')
<div class="min-h-screen relative flex items-center justify-center bg-[#F8FAFC] py-8 px-2">

    <!-- Return Button (Outside the box, top-left) -->
    <div class="absolute top-4 left-4">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-black rounded-lg text-sm font-medium shadow-md hover:bg-[#FF2D2D] hover:text-white transition">
            ⬅ <span>Return</span>
        </a>
    </div>

    <!-- Feedback Card -->
    <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-lg p-8 z-10">
        <!-- Decorative background shapes -->
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-[#FF2D2D] opacity-10 rounded-full blur-2xl z-0"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#002B5C] opacity-10 rounded-full blur-2xl z-0"></div>

        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold text-center text-[#002B5C] mb-6 tracking-tight">
                Leave Feedback for <span class="text-[#FF2D2D]">{{ $shop->name }}</span>
            </h1>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4 text-center font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4 text-center font-semibold">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('customer.feedback.store', $shop->id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Star Rating -->
                <div>
                    <label class="block text-base font-bold text-[#182235] mb-2">Rating</label>
                    <div class="flex gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                                   class="hidden peer/star{{ $i }}"
                                   {{ old('rating', 5) == $i ? 'checked' : '' }}>
                            <label for="star{{ $i }}"
                                   class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-300 peer-checked/star{{ $i }}:text-yellow-400 transition"
                                   data-star="{{ $i }}">
                                ★
                            </label>
                        @endfor
                    </div>
                </div>

                <div>
                    <label for="comment" class="block text-base font-bold text-[#182235] mb-2">Comment</label>
                    <textarea name="comment" id="comment" rows="4"
                        class="w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition resize-none"
                        placeholder="Share your experience...">{{ old('comment') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-bold text-lg hover:opacity-90 transition">
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Ensure stars highlight left to right
    document.addEventListener("DOMContentLoaded", function () {
        const stars = document.querySelectorAll('[data-star]');
        const updateStars = (value) => {
            stars.forEach(star => {
                if (parseInt(star.dataset.star) <= value) {
                    star.classList.add("text-yellow-400");
                } else {
                    star.classList.remove("text-yellow-400");
                }
            });
        };

        stars.forEach(star => {
            star.addEventListener("click", () => {
                updateStars(parseInt(star.dataset.star));
            });
        });

        // Initialize with backend value
        const checked = document.querySelector('input[name="rating"]:checked');
        if (checked) updateStars(parseInt(checked.value));
    });
</script>
@endsection
