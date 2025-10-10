@extends('layouts.app')

@section('content')
<style>
    /* Hide navbar/header only, not our custom button */
    header, nav.site-navbar {
        display: none !important;
    }
</style>

<div class="min-h-screen relative flex items-center justify-center bg-[#F8FAFC] py-10 px-4">

    <!-- 🔙 Return Button (always visible) -->
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('carwashownerdashboard') }}"
           class="flex items-center gap-2 px-4 py-2 bg-[#002B5C] text-white rounded-lg text-sm sm:text-base font-semibold shadow-md hover:bg-[#FF2D2D] transition-all duration-200">
            ← Return
        </a>
    </div>

    <!-- 🔹 Main Card -->
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl p-8 sm:p-10 z-10 overflow-hidden">

        <!-- Decorative Blurs -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#FF2D2D] opacity-10 rounded-full blur-3xl z-0"></div>
        <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-[#002B5C] opacity-10 rounded-full blur-3xl z-0"></div>

        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold text-center text-[#002B5C] mb-2">
                Add Walk-in Appointment
            </h1>
            <p class="text-gray-500 text-center mb-6 text-sm sm:text-base">
                Fill in the details below to register a new walk-in customer.
            </p>

            {{-- ✅ Success Message --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ❌ Error Message --}}
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center font-semibold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- 🧾 Walk-In Form -->
            <form action="{{ route('owner.walkin.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-[#182235] mb-2">Customer Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition"
                        placeholder="Enter customer name" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_no" class="block text-sm font-bold text-[#182235] mb-2">Contact Number</label>
                    <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}"
                        class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition"
                        placeholder="e.g. 09XXXXXXXXX" required>
                    @error('contact_no')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Car Size -->
                <div>
                    <label for="size_of_the_car" class="block text-sm font-bold text-[#182235] mb-2">Car Size</label>
                    <select name="size_of_the_car" id="size_of_the_car"
                        class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition" required>
                        <option value="">Select car size</option>
                        <option value="HatchBack" {{ old('size_of_the_car') == 'HatchBack' ? 'selected' : '' }}>HatchBack</option>
                        <option value="Sedan" {{ old('size_of_the_car') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                        <option value="SUV" {{ old('size_of_the_car') == 'SUV' ? 'selected' : '' }}>SUV</option>
                        <option value="Pickup" {{ old('size_of_the_car') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="Van" {{ old('size_of_the_car') == 'Van' ? 'selected' : '' }}>Van</option>
                        <option value="Motorcycle" {{ old('size_of_the_car') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    </select>
                    @error('size_of_the_car')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="date_of_booking" class="block text-sm font-bold text-[#182235] mb-2">Date</label>
                        <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}"
                            class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition" required>
                        @error('date_of_booking')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time_of_booking" class="block text-sm font-bold text-[#182235] mb-2">Time</label>
                        <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}"
                            class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition" required>
                        @error('time_of_booking')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Slot -->
                <div>
                    <label for="slot_number" class="block text-sm font-bold text-[#182235] mb-2">Slot Number</label>
                    <input type="number" name="slot_number" id="slot_number"
                        value="{{ old('slot_number', 1) }}" min="1" max="4"
                        class="w-full border-2 border-gray-300 rounded-lg p-3 text-gray-900 focus:ring-2 focus:ring-[#FF2D2D] focus:border-[#FF2D2D] transition"
                        placeholder="1 to 4" required>
                    @error('slot_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-[#FF2D2D] text-white py-3 rounded-xl font-semibold text-lg hover:bg-[#002B5C] hover:shadow-lg transition active:scale-95">
                    ➕ Add Walk-in
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
