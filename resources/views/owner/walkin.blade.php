@extends('layouts.blade')

<style>
    /* Hide navbar/header only for this page */
    nav, header {
        display: none !important;
    }
</style>

@section('content')
<div class="min-h-screen relative flex items-center justify-center bg-[#002B5C] py-10 px-4 overflow-hidden">

    <!-- Return Button (Outside the box, top-left) -->
    <div class="absolute top-4 left-4">
        <a href="{{ route('carwashownerdashboard') }}"
           class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-black rounded-lg text-sm font-medium shadow-md hover:bg-[#FF2D2D] hover:text-white transition">
            ⬅ <span>Return</span>
        </a>
    </div>

    <!-- Decorative blurred background shapes -->
    <div class="absolute top-10 -left-20 w-72 h-72 bg-[#FF2D2D] opacity-20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 -right-20 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>

    <!-- Walk-in Form Card -->
    <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl p-10 z-10">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-center text-[#002B5C] mb-4">
            Add Walk-in Appointment
        </h1>
        <p class="text-gray-500 text-center mb-8 text-sm sm:text-base">
            Fill in the details to schedule a walk-in appointment.
        </p>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-center font-medium shadow-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-center font-medium shadow-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('owner.walkin.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @csrf

            <div class="col-span-2 sm:col-span-1">
                <label for="name" class="block font-semibold text-gray-800 mb-2">Customer Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="contact_no" class="block font-semibold text-gray-800 mb-2">Contact No</label>
                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}"
                       class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('contact_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="size_of_the_car" class="block font-semibold text-gray-800 mb-2">Car Size</label>
                <select name="size_of_the_car" id="size_of_the_car"
                        class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    <option value="">Select</option>
                    <option value="HatchBack" {{ old('size_of_the_car') == 'HatchBack' ? 'selected' : '' }}>HatchBack</option>
                    <option value="Sedan" {{ old('size_of_the_car') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="SUV" {{ old('size_of_the_car') == 'SUV' ? 'selected' : '' }}>SUV</option>
                    <option value="Pickup" {{ old('size_of_the_car') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="Van" {{ old('size_of_the_car') == 'Van' ? 'selected' : '' }}>Van</option>
                    <option value="Motorcycle" {{ old('size_of_the_car') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                </select>
                @error('size_of_the_car') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="slot_number" class="block font-semibold text-gray-800 mb-2">Slot Number</label>
                <input type="number" name="slot_number" id="slot_number" value="{{ old('slot_number', 1) }}" min="1" max="4"
                       class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('slot_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="date_of_booking" class="block font-semibold text-gray-800 mb-2">Date</label>
                <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}"
                       class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('date_of_booking') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label for="time_of_booking" class="block font-semibold text-gray-800 mb-2">Time</label>
                <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}"
                       class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('time_of_booking') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <button type="submit"
                        class="w-full bg-[#002B5C] text-white py-3 rounded-xl font-semibold text-lg hover:bg-[#1E8449] transition shadow-md hover:shadow-lg">
                    Add Walk-in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
