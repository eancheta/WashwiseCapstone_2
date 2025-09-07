@extends('layouts.blade')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-4">Leave Feedback for {{ $shop->name }}</h1>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4">
            ❌ {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('customer.feedback.store', $shop->id) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="rating" class="block font-medium">Rating</label>
            <select name="rating" id="rating" class="w-full border rounded p-2">
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('rating', 5) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="mb-4">
            <label for="comment" class="block font-medium">Comment</label>
            <textarea name="comment" id="comment" rows="4" class="w-full border rounded p-2">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Submit Feedback
        </button>
    </form>
</div>
@endsection
