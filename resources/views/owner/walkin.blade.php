<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Walk-in - {{ config('app.name', 'Washwise') }}</title>
  @vite(['resources/js/app.ts', 'resources/css/app.css'])
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    body {
      background: linear-gradient(135deg, #002B5C 0%, #004C97 100%);
      font-family: 'Inter', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      color: #fff;
      overflow-y: auto;
    }

    header {
      text-align: center;
      margin-top: 40px;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 800;
    }

    p.subtitle {
      color: #cbd5e1;
      font-size: 1.1rem;
      margin-top: 0.5rem;
    }

    form {
      width: 100%;
      max-width: 900px;
      background-color: #fff;
      color: #000;
      margin-top: 30px;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    }

    label {
      font-weight: 600;
      color: #002B5C;
    }

    input, select {
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
      padding: 0.75rem;
      margin-top: 0.25rem;
      margin-bottom: 1rem;
      font-size: 1rem;
      color: #333;
    }

    button {
      background-color: #002B5C;
      color: #fff;
      width: 100%;
      padding: 1rem;
      border-radius: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #004C97;
    }
  </style>
</head>
<body>

  <header>
    <h1>Add Walk-in Appointment</h1>
    <p class="subtitle">Fill in the details to schedule a walk-in appointment.</p>
  </header>

  @if (session('success'))
    <div class="mt-4 bg-green-100 text-green-800 p-3 rounded-lg w-full max-w-3xl text-center">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mt-4 bg-red-100 text-red-800 p-3 rounded-lg w-full max-w-3xl text-center">
      {{ session('error') }}
    </div>
  @endif

  <form action="{{ route('owner.walkin.store') }}" method="POST">
    @csrf

    <label for="name">Customer Name</label>
    <input type="text" name="name" id="name" value="{{ old('name') }}" required>

    <label for="contact_no">Contact No</label>
    <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" required>

    <label for="size_of_the_car">Car Size</label>
    <select name="size_of_the_car" id="size_of_the_car" required>
      <option value="">Select</option>
      <option value="HatchBack" {{ old('size_of_the_car') == 'HatchBack' ? 'selected' : '' }}>HatchBack</option>
      <option value="Sedan" {{ old('size_of_the_car') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
      <option value="SUV" {{ old('size_of_the_car') == 'SUV' ? 'selected' : '' }}>SUV</option>
      <option value="Pickup" {{ old('size_of_the_car') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
      <option value="Van" {{ old('size_of_the_car') == 'Van' ? 'selected' : '' }}>Van</option>
      <option value="Motorcycle" {{ old('size_of_the_car') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
    </select>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
      <div>
        <label for="date_of_booking">Date</label>
        <input type="date" name="date_of_booking" id="date_of_booking" value="{{ old('date_of_booking') }}" required>
      </div>
      <div>
        <label for="time_of_booking">Time</label>
        <input type="time" name="time_of_booking" id="time_of_booking" value="{{ old('time_of_booking') }}" required>
      </div>
    </div>

    <label for="slot_number">Slot Number</label>
    <input type="number" name="slot_number" id="slot_number" value="{{ old('slot_number', 1) }}" min="1" max="4" required>

    <button type="submit">Add Walk-in</button>
  </form>

</body>
</html>
