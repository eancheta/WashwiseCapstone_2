<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walk-in - {{ config('app.name', 'Washwise') }}</title>
    @vite(['resources/js/app.ts', 'resources/css/app.css'])
    <style>
        /* Fade animation */
        .fade {
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }
        .fade.show {
            opacity: 1;
        }

        /* Full-screen layout */
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Background styling */
        body {
            background: linear-gradient(135deg, #002B5C 0%, #004C97 100%);
            color: white;
            font-family: 'Inter', sans-serif;
        }

        /* Center content vertically and horizontally */
        .centered {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Card shadow & animation */
        .card {
            background-color: #fff;
            color: #333;
            border-radius: 1.25rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            width: 95%;
            max-width: 700px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        /* Spinner animation */
        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 2rem auto 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- 🔹 Intro View -->
    <div id="introView" class="centered fade show flex-col text-center">
        <div>
            <h1 class="text-4xl font-extrabold mb-4">Welcome to <span class="text-yellow-300">WashWise</span></h1>
            <p class="text-gray-200 text-lg">Preparing your walk-in appointment form...</p>
            <div class="spinner"></div>
        </div>
    </div>

    <!-- 🔹 Full-Screen Walk-in Form -->
    <div id="formView" class="hidden centered fade">
        <div class="card">
            <!-- Header -->
            <div class="mb-5 text-center">
                <h1 class="text-3xl font-extrabold text-[#002B5C]">Add Walk-in Appointment</h1>
                <p class="text-gray-500 mt-1 text-base">Fill in the details to schedule a walk-in appointment.</p>
            </div>

            <!-- Success/Error Alerts -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm shadow-sm">
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
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_no" class="block font-semibold text-gray-800 mb-2">Contact No</label>
                    <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    @error('contact_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
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
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="date_of_booking" class="block font-semibold text-gray-800 mb-2">Date</label>
                        <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}"
                            class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                        @error('date_of_booking')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time_of_booking" class="block font-semibold text-gray-800 mb-2">Time</label>
                        <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}"
                            class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                        @error('time_of_booking')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="slot_number" class="block font-semibold text-gray-800 mb-2">Slot Number</label>
                    <input type="number" name="slot_number" id="slot_number" value="{{ old('slot_number', 1) }}" min="1" max="4"
                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#002B5C]" required>
                    @error('slot_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#002B5C] text-white py-3 rounded-xl font-semibold hover:bg-[#1E8449] transition shadow-md hover:shadow-lg">
                    Add Walk-in
                </button>
            </form>
        </div>
    </div>

    <script>
        // Switch from intro to form
        setTimeout(() => {
            document.getElementById('introView').classList.remove('show');
            setTimeout(() => {
                document.getElementById('introView').style.display = 'none';
                const formView = document.getElementById('formView');
                formView.classList.remove('hidden');
                formView.classList.add('show');
            }, 800);
        }, 1000);
    </script>
</body>
</html>
