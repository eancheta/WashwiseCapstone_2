<!-- resources/views/public/shop_booking.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Book a Service at {{ $shop->name }}</h1>

    {{-- Shop details --}}
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">{{ $shop->name }}</h4>
            <p class="mb-1"><strong>Address:</strong> {{ $shop->address }}</p>
            <p class="mb-1"><strong>District:</strong> {{ $shop->district }}</p>
            @if($shop->description)
                <p class="mb-1"><strong>Description:</strong> {{ $shop->description }}</p>
            @endif
            @if($shop->services_offered)
                <p class="mb-0"><strong>Services:</strong> {{ $shop->services_offered }}</p>
            @endif
        </div>
    </div>

    {{-- Booking form --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customer.book', $shop->id) }}" method="POST">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                {{-- Display validation errors --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="size_of_the_car" class="form-label">Size of the Car</label>
                    <select id="size_of_the_car" name="size_of_the_car"
                            class="form-select @error('size_of_the_car') is-invalid @enderror" required>
                        <option value="">Select a size</option>
                        <option value="HatchBack">HatchBack</option>
                        <option value="Sedan">Sedan</option>
                        <option value="MPV">MPV</option>
                        <option value="SUV">SUV</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Van">Van</option>
                        <option value="Motorcycle">Motorcycle</option>
                    </select>
                    @error('size_of_the_car')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contact_no" class="form-label">Contact Number</label>
                    <input type="text" id="contact_no" name="contact_no"
                           class="form-control @error('contact_no') is-invalid @enderror"
                           value="{{ old('contact_no') }}" required>
                    @error('contact_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="time_of_booking" class="form-label">Time of Booking</label>
                    <input type="time" id="time_of_booking" name="time_of_booking"
                           class="form-control @error('time_of_booking') is-invalid @enderror" required>
                    @error('time_of_booking')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="date_of_booking" class="form-label">Date of Booking</label>
                    <input type="date" id="date_of_booking" name="date_of_booking"
                           class="form-control @error('date_of_booking') is-invalid @enderror" required>
                    @error('date_of_booking')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slot_number" class="form-label">Slot Number</label>
                    <input type="number" id="slot_number" name="slot_number" min="1"
                           class="form-control @error('slot_number') is-invalid @enderror"
                           value="{{ old('slot_number') }}" required>
                    @error('slot_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Book Now</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
