@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
        <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('carwashownerdashboard') }}"
           class="flex items-center gap-2 px-4 py-2 bg-[#002B5C] text-white rounded-lg text-sm sm:text-base font-semibold shadow-md hover:bg-[#FF2D2D] transition-all duration-200">
            ← Return
        </a>
    </div>
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center text-[#182235] mb-6">
      Edit Car Wash Shop
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

    <form action="{{ route('owner.shop.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Name --}}
      <div class="mb-4">
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
        <input
          type="text"
          id="name"
          name="name"
          value="{{ old('name', $shop->name) }}"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="Enter shop name"
          required
        >
      </div>

      {{-- Address --}}
      <div class="mb-4">
        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
        <input
          type="text"
          id="address"
          name="address"
          value="{{ old('address', $shop->address) }}"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="Enter shop address"
          required
        >
      </div>

      {{-- District --}}
      <div class="mb-4">
        <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">District</label>
        <input
          type="text"
          id="district"
          name="district"
          value="{{ old('district', $shop->district) }}"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
          placeholder="1–6"
          required
        >
      </div>

      {{-- Logo --}}
      <div class="mb-4">
        <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
        <div class="flex items-center space-x-4">
          <input
            type="file"
            id="logo"
            name="logo"
            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none"
          >
          @if ($shop->logo)
            <img src="{{ $shop->logo }}" alt="Shop Logo" class="h-12 w-12 rounded-lg object-cover border">
          @endif
        </div>
      </div>

      {{-- Description --}}
      <div class="mb-4">
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
        <textarea
          id="description"
          name="description"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
          rows="3"
          placeholder="Write shop description"
          required
        >{{ old('description', $shop->description) }}</textarea>
      </div>

      {{-- Services Offered --}}
      <div class="mb-4">
        <label for="services_offered" class="block text-sm font-semibold text-gray-700 mb-2">Services Offered</label>
        <textarea
          id="services_offered"
          name="services_offered"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
          rows="3"
          placeholder="List services"
          required
        >{{ old('services_offered', $shop->services_offered) }}</textarea>
      </div>

      {{-- QR Code --}}
      <div class="mb-6">
        <label for="qr_code" class="block text-sm font-semibold text-gray-700 mb-2">QR Code</label>
        <div class="flex items-center space-x-4">
          <input
            type="file"
            id="qr_code"
            name="qr_code"
            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none"
          >
          @if ($shop->qr_code)
            <img src="{{ $shop->qr_code }}" alt="QR Code" class="h-12 w-12 rounded-lg object-cover border">
          @endif
        </div>
      </div>

      {{-- Submit Button --}}
      <button
        type="submit"
        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-all duration-300 shadow-md"
      >
        Update Shop
      </button>
    </form>
  </div>
</div>
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('updateShopForm');
  const btn = document.getElementById('updateShopSubmitBtn');

  if (!form) {
    console.error('Update form not found.');
    return;
  }

  // Intercept the normal submit to handle via fetch and show SweetAlert (no reload)
  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // disable button to prevent double clicks
    if (btn) {
      btn.setAttribute('disabled', 'disabled');
      btn.classList.add('opacity-60', 'cursor-not-allowed');
    }

    const formData = new FormData(form);

    try {
      const res = await fetch(form.action, {
        method: form.getAttribute('method') || 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          // DO NOT set Content-Type — browser will set multipart boundary
        },
        redirect: 'follow'
      });

      // Treat non-OK as error (try to surface validation messages if JSON)
      if (!res.ok) {
        const contentType = (res.headers.get('content-type') || '').toLowerCase();
        if (contentType.includes('application/json')) {
          const errJson = await res.json();
          const msg = errJson && errJson.errors ? Object.values(errJson.errors).flat().join('\n') : (errJson.message || 'Validation error');
          throw new Error(msg);
        }
        throw new Error('Server error: ' + res.status);
      }

      // SUCCESS: show the SweetAlert exactly as you wanted — NO reload
      await Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Shop updated successfully',
        showConfirmButton: false,
        timer: 900
      });

      // NOTE: no location.reload or navigation here — keep user on the same page

    } catch (err) {
      console.error(err);
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message || 'Something went wrong'
      });
    } finally {
      if (btn) {
        btn.removeAttribute('disabled');
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
      }
    }
  });
});
</script>
@endsection
