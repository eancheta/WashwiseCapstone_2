@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-[#182235] mb-6">
      Change Password
    </h2>

    @if (session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('owner.password.update') }}" method="POST">
      @csrf

      <!-- Current Password -->
      <div class="mb-4 relative">
        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
          Current Password
        </label>
        <input
          type="password"
          id="current_password"
          name="current_password"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="Enter your current password"
          required
        >
        <button type="button" onclick="togglePassword('current_password', this)"
          class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <!-- New Password -->
      <div class="mb-4 relative">
        <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
          New Password
        </label>
        <input
          type="password"
          id="new_password"
          name="new_password"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="Enter your new password"
          required
        >
        <button type="button" onclick="togglePassword('new_password', this)"
          class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <!-- Confirm Password -->
      <div class="mb-6 relative">
        <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
          Confirm New Password
        </label>
        <input
          type="password"
          id="new_password_confirmation"
          name="new_password_confirmation"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="Confirm your new password"
          required
        >
        <button type="button" onclick="togglePassword('new_password_confirmation', this)"
          class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <button
        type="submit"
        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-all duration-300 shadow-md"
      >
        Save Changes
      </button>
    </form>
  </div>
</div>

<script>
  function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector("i");
    if (input.type === "password") {
      input.type = "text";
      icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.replace("fa-eye-slash", "fa-eye");
    }
  }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
