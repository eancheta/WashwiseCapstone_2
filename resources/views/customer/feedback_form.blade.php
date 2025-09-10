@extends('layouts.blade')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F8FAFC] py-8 px-2">
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

                <div>
                    <label for="rating" class="block text-base font-bold text-[#182235] mb-2">Rating</label>
                    <select name="rating" id="rating" class="w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition">
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating', 5) == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
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
@endsection
