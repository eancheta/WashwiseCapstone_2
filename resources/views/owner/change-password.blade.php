@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Change Password</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('owner.password.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="current_password">Current Password</label>
            <input type="password" class="form-control" name="current_password" required>
            @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
            @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation">Confirm New Password</label>
            <input type="password" class="form-control" name="new_password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
@endsection
