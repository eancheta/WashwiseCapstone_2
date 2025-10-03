@extends('layouts.app')

@section('content')
<div class="container py-6">
    <h2 class="mb-4 text-2xl font-semibold">Edit Shop Details</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('owner.shop.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name of Car Wash</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $shop->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" name="address" value="{{ old('address', $shop->address) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">District</label>
            <input type="text" class="form-control" name="district" value="{{ old('district', $shop->district) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4">{{ old('description', $shop->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Services Offered</label>
            <input type="text" class="form-control" name="services_offered" value="{{ old('services_offered', $shop->services_offered) }}">
            <small class="text-muted">Comma-separated (example: Wash, Vacuum, Wax)</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Car Wash Logo</label><br>
            @if(!empty($shop->logo))
                <img src="{{ $shop->logo }}" alt="Shop Logo" width="120" style="display:block;margin-bottom:8px;">
            @endif
            <input type="file" name="logo" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">QR Code</label><br>
            @if(!empty($shop->qr_code))
                <img src="{{ $shop->qr_code }}" alt="Shop QR Code" width="120" style="display:block;margin-bottom:8px;">
            @endif
            <input type="file" name="qr_code" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Shop</button>
    </form>
</div>
@endsection
