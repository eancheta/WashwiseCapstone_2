```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walk-in - {{ config('app.name', 'Washwise') }}</title>
    @vite(['resources/js/app.ts', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-4 text-[#002B5C]">Add Walk-in Appointment</h1>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('owner.walkin.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-semibold">Customer Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_no" class="block font-semibold">Contact No</label>
                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" class="w-full border rounded p-2" required>
                @error('contact_no')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="size_of_the_car" class="block font-semibold">Car Size</label>
                <select name="size_of_the_car" id="size_of_the_car" class="w-full border rounded p-2" required>
                    <option value="">Select</option>
                    <option value="HatchBack" {{ old('size_of_the_car') == 'HatchBack' ? 'selected' : '' }}>HatchBack</option>
                    <option value="Sedan" {{ old('size_of_the_car') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="SUV" {{ old('size_of_the_car') == 'SUV' ? 'selected' : '' }}>SUV</option>
                    <option value="Pickup" {{ old('size_of_the_car') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="Van" {{ old('size_of_the_car') == 'Van' ? 'selected' : '' }}>Van</option>
                    <option value="Motorcycle" {{ old('size_of_the_car') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                </select>
                @error('size_of_the_car')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date_of_booking" class="block font-semibold">Date</label>
                <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}" class="w-full border rounded p-2" required>
                @error('date_of_booking')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="time_of_booking" class="block font-semibold">Time</label>
                <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}" class="w-full border rounded p-2" required>
                @error('time_of_booking')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slot_number" class="block font-semibold">Slot Number</label>
                <input type="number" name="slot_number" id="slot_number" value="{{ old('slot_number', 1) }}" min="1" max="4" class="w-full border rounded p-2" required>
                @error('slot_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-[#27AE60] text-white py-2 rounded-lg font-semibold hover:bg-[#1E8449] transition">
                Add Walk-in
            </button>
        </form>
    </div>
</body>
</html>
```
