<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walk-in - {{ config('app.name', 'Washwise') }}</title>
    @vite(['resources/js/app.ts', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">


    <!-- 🔹 Card -->
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg p-6 sm:p-8 hover:shadow-2xl transition">
        <!-- Form Header -->
        <div class="mb-5">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#002B5C]">Add Walk-in Appointment</h1>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">Fill in the details to schedule a walk-in appointment.</p>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm sm:text-base shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm sm:text-base shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('owner.walkin.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-semibold text-gray-800 mb-2">Customer Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('name')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_no" class="block font-semibold text-gray-800 mb-2">Contact No</label>
                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}"
                       class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('contact_no')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="size_of_the_car" class="block font-semibold text-gray-800 mb-2">Car Size</label>
                <select name="size_of_the_car" id="size_of_the_car"
                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    <option value="">Select</option>
                    <option value="HatchBack" {{ old('size_of_the_car') == 'HatchBack' ? 'selected' : '' }}>HatchBack</option>
                    <option value="Sedan" {{ old('size_of_the_car') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="SUV" {{ old('size_of_the_car') == 'SUV' ? 'selected' : '' }}>SUV</option>
                    <option value="Pickup" {{ old('size_of_the_car') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="Van" {{ old('size_of_the_car') == 'Van' ? 'selected' : '' }}>Van</option>
                    <option value="Motorcycle" {{ old('size_of_the_car') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                </select>
                @error('size_of_the_car')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date_of_booking" class="block font-semibold text-gray-800 mb-2">Date</label>
                    <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}"
                           class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    @error('date_of_booking')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_of_booking" class="block font-semibold text-gray-800 mb-2">Time</label>
                    <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}"
                           class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    @error('time_of_booking')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="slot_number" class="block font-semibold text-gray-800 mb-2">Slot Number</label>
                <input type="number" name="slot_number" id="slot_number" value="{{ old('slot_number', 1) }}" min="1" max="4"
                       class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                @error('slot_number')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-[#002B5C] text-white py-3 rounded-xl font-semibold hover:bg-[#1E8449] transition shadow-md hover:shadow-lg">
                Add Walk-in
            </button>
        </form>
    </div>
</body>
</html>
