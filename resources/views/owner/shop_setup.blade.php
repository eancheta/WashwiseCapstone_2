@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Car Wash Shop</h1>

    <form action="{{ route('owner.shop.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Address</label>
        <input type="text" name="address" required>

        <label>District</label>
        <input type="number" name="district" min="1" max="6" required>

        <label>Logo</label>
        <input type="file" name="logo" accept="image/*">

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Services Offered</label>
        <textarea name="services_offered"></textarea>

        <label>QR Code</label>
        <input type="file" name="qr_code" accept="image/*">

        <button type="submit">Create Shop</button>
    </form>
</div>
@endsection
