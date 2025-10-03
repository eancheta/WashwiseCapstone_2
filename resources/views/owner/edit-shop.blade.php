@extends('layouts.blade')

@section('content')
<div class="container">
    <h2>Edit Shop Details</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('owner.shop.update', $shop->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name of Car Wash</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $shop->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" class="form-control" name="address" value="{{ old('address', $shop->address) }}" required>
        </div>

        <div class="mb-3">
            <label>District</label>
            <input type="text" class="form-control" name="district" value="{{ old('district', $shop->district) }}">
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea class="form-control" name="description">{{ old('description', $shop->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Services Offered</label>
            <input type="text" class="form-control" name="services_offered" value="{{ old('services_offered', $shop->services_offered) }}">
        </div>

        <div class="mb-3">
            <label>Car Wash Logo</label><br>
            @if($shop->logo)
                <img src="{{ $shop->logo }}" alt="Shop Logo" width="120"><br>
            @endif
            <input type="file" name="logo" class="form-control mt-2">
        </div>

        <div class="mb-3">
            <label>QR Code</label><br>
            @if($shop->qr_code)
                <img src="{{ $shop->qr_code }}" alt="Shop QR Code" width="120"><br>
            @endif
            <input type="file" name="qr_code" class="form-control mt-2">
        </div>

        <button type="submit" class="btn btn-primary">Update Shop</button>
    </form>
</div>
@endsection
