@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Book at {{ $shop->name }}</h1>

    <form action="{{ route('customer.book.store', $shop->id) }}" method="POST">

        <div>
            <label>Your Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <div>{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Size of the Car</label>
            <select name="size_of_the_car" required>
                <option value="">Select</option>
                <option>HatchBack</option>
                <option>Sedan</option>
                <option>MPV</option>
                <option>SUV</option>
                <option>Pickup</option>
                <option>Van</option>
                <option>Motorcycle</option>
            </select>
            @error('size_of_the_car') <div>{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Contact Number</label>
            <input type="text" name="contact_no" value="{{ old('contact_no') }}" required>
            @error('contact_no') <div>{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Time of Booking</label>
            <input type="time" name="time_of_booking" value="{{ old('time_of_booking') }}" required>
            @error('time_of_booking') <div>{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Date of Booking</label>
            <input type="date" name="date_of_booking" value="{{ old('date_of_booking') }}" required>
            @error('date_of_booking') <div>{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Slot Number</label>
            <input type="number" name="slot_number" min="1" value="{{ old('slot_number') }}" required>
            @error('slot_number') <div>{{ $message }}</div> @enderror
        </div>

        <button type="submit">Book Now</button>
    </form>
</div>
@endsection
