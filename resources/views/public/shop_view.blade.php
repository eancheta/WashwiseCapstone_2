@extends('layouts.app')


@section('content')
<div class="container">
    <h1>{{ $shop->name }}</h1>
    <p><strong>Address:</strong> {{ $shop->address }}</p>
    <p><strong>District:</strong> {{ $shop->district }}</p>
    <p><strong>Description:</strong> {{ $shop->description }}</p>
    <p><strong>Services Offered:</strong> {{ $shop->services_offered }}</p>

    @if($shop->logo)
        <img src="{{ asset('storage/'.$shop->logo) }}" alt="Shop Logo" width="200">
    @endif

    @if($shop->qr_code)
        <img src="{{ asset('storage/'.$shop->qr_code) }}" alt="QR Code" width="200">
    @endif

    <a href="{{ route('public.shop.booking', $shop->id) }}">Book Now</a>
</div>
@endsection
