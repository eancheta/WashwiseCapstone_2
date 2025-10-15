@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="absolute top-4 left-4">
    <a href="{{ route('carwashownerdashboard') }}"class="flex items-center gap-2 px-4 py-2 bg-[#002B5C] text-white rounded-lg text-sm font-medium shadow-md hover:bg-[#FF2D2D] hover:text-white transition">
            ⬅ <span>Return</span>
    </a>

    </div>

  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center text-[#182235] mb-6">
      Edit Car Wash Shop
    </h2>

    {{-- Errors (server-side validation) --}}
    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="updateShopForm" action="{{ route('owner.shop.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('POST') {{-- keep as is, your controller expects POST --}}

      {{-- Name --}}
      <div class="mb-4">
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name) }}" class="w-full border rounded-lg px-4 py-2" required>
      </div>

      {{-- Address --}}
      <div class="mb-4">
        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address', $shop->address) }}" class="w-full border rounded-lg px-4 py-2" required>
      </div>

      {{-- District --}}
      <div class="mb-4">
        <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">District</label>
        <input type="text" id="district" name="district" value="{{ old('district', $shop->district) }}" class="w-full border rounded-lg px-4 py-2" required>
      </div>

      {{-- Logo --}}
      <div class="mb-4">
        <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
        <div class="flex items-center space-x-4">
          <input type="file" id="logo" name="logo" class="block w-full text-sm text-gray-700 border rounded-lg cursor-pointer">
          @if ($shop->logo)
            <img src="{{ $shop->logo }}" alt="Shop Logo" class="h-12 w-12 rounded-lg object-cover border">
          @endif
        </div>
      </div>

      {{-- Description --}}
      <div class="mb-4">
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
        <textarea id="description" name="description" class="w-full border rounded-lg px-4 py-2" rows="3" required>{{ old('description', $shop->description) }}</textarea>
      </div>

      {{-- Services --}}
      <div class="mb-4">
        <label for="services_offered" class="block text-sm font-semibold text-gray-700 mb-2">Services Offered</label>
        <textarea id="services_offered" name="services_offered" class="w-full border rounded-lg px-4 py-2" rows="3" required>{{ old('services_offered', $shop->services_offered) }}</textarea>
      </div>

      {{-- QR Code --}}
      <div class="mb-6">
        <label for="qr_code" class="block text-sm font-semibold text-gray-700 mb-2">QR Code</label>
        <div class="flex items-center space-x-4">
          <input type="file" id="qr_code" name="qr_code" class="block w-full text-sm text-gray-700 border rounded-lg cursor-pointer">
          @if ($shop->qr_code)
            <img src="{{ $shop->qr_code }}" alt="QR Code" class="h-12 w-12 rounded-lg object-cover border">
          @endif
        </div>
      </div>

      {{-- Hidden fallback submit for non-JS users --}}
      <noscript>
        <div class="mb-4">
          <button type="submit" class="block w-full bg-red-500 text-white py-2 rounded-lg">Update Shop</button>
        </div>
      </noscript>

    </form>

    {{-- The link styled as button that triggers JS submit --}}
    <a href="#" id="updateShopButton" class="block text-center w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-all duration-300 shadow-md mt-4">
      Update Shop
    </a>


  </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('updateShopButton');
  const form = document.getElementById('updateShopForm');

  if (!btn || !form) {
    console.error('Update button or form not found.');
    return;
  }

  btn.addEventListener('click', async function (e) {
    e.preventDefault();

    // disable button to prevent double clicks
    btn.setAttribute('disabled', 'disabled');
    btn.classList.add('opacity-60', 'cursor-not-allowed');

    const formData = new FormData(form);

    try {
      const res = await fetch(form.action, {
        method: form.getAttribute('method') || 'POST',
        body: formData,
        credentials: 'same-origin',
        redirect: 'follow',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          // DO NOT set 'Content-Type'
        },
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
        timer: 1200
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
      btn.removeAttribute('disabled');
      btn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
  });
});
</script>
@endsection
