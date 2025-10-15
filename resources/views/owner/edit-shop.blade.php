@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
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
        headers: {
          'X-Requested-With': 'XMLHttpRequest', // ensures $request->ajax() === true in Laravel
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          // DO NOT set 'Content-Type' - browser will set correct multipart boundary
        },
      });

      // If response is JSON, parse it. If it's HTML (redirect), treat as success.
      const contentType = res.headers.get('content-type') || '';

      if (!res.ok) {
        // try to parse JSON error
        if (contentType.includes('application/json')) {
          const json = await res.json();
          let errText = 'Validation error';
          if (json.errors) {
            // join validation errors
            errText = Object.values(json.errors).flat().join('\\n');
          } else if (json.message) {
            errText = json.message;
          }
          throw new Error(errText);
        } else {
          throw new Error('Server error: ' + res.status);
        }
      }

      if (contentType.includes('application/json')) {
        const json = await res.json();
        if (json.success) {
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: json.message || 'Shop updated successfully',
            showConfirmButton: false,
            timer: 1500
          });
          location.reload();
          return;
        } else if (json.errors) {
          const msg = Object.values(json.errors).flat().join('\\n');
          throw new Error(msg);
        } else {
          // unknown json, treat as success
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: json.message || 'Shop updated',
            showConfirmButton: false,
            timer: 1200
          });
          location.reload();
          return;
        }
      } else {
        // Response is HTML (likely Laravel redirected). Treat as success:
        await Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Shop updated successfully',
          showConfirmButton: false,
          timer: 1200
        });
        // If server redirected to another page, location.reload() will navigate to that page only if the server used a location header. To be safe, go to current URL:
        location.reload();
      }

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
