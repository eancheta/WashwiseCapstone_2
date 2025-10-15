@extends('layouts.blade')
<style>
    /* Hide navbar/header only for this page */
    nav, header {
        display: none !important;
    }
</style>
@section('content')
<div class="min-h-screen relative flex items-center justify-center bg-[#F8FAFC] py-8 px-2">
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

      // If the server performed a redirect, fetch's `res.redirected` is true and `res.url` holds the final URL.
      // In that case we should navigate the browser to res.url to perform a full page load.
      if (res.redirected) {
        // show success alert briefly then navigate to the redirected URL (full page load)
        await Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Shop updated successfully',
          showConfirmButton: false,
          timer: 900
        });
        window.location.href = res.url;
        return;
      }

      const contentType = (res.headers.get('content-type') || '').toLowerCase();

      // If response is not ok, try to parse JSON errors (if any) or throw generic error
      if (!res.ok) {
        if (contentType.includes('application/json')) {
          const jsonErr = await res.json();
          let errText = 'Validation error';
          if (jsonErr.errors) {
            errText = Object.values(jsonErr.errors).flat().join('\\n');
          } else if (jsonErr.message) {
            errText = jsonErr.message;
          }
          throw new Error(errText);
        } else {
          // HTML error or server error
          throw new Error('Server error: ' + res.status);
        }
      }

      // At this point response is ok and not redirected.
      // If JSON -> parse and handle success/errors.
      if (contentType.includes('application/json')) {
        const json = await res.json();
        if (json.success) {
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: json.message || 'Shop updated successfully',
            showConfirmButton: false,
            timer: 1200
          });
          // force full fresh reload (cache-busting)
          const freshUrl = window.location.pathname + '?_=' + Date.now();
          window.location.href = freshUrl;
          return;
        } else if (json.errors) {
          const msg = Object.values(json.errors).flat().join('\\n');
          throw new Error(msg);
        } else {
          // unknown json body, treat as success
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: json.message || 'Shop updated',
            showConfirmButton: false,
            timer: 1000
          });
          const freshUrl = window.location.pathname + '?_=' + Date.now();
          window.location.href = freshUrl;
          return;
        }
      }

      // If we get here, response is HTML (res.ok && not redirected).
      // That often means the backend returned the updated page HTML. Force a full navigation to the current URL with a cache-busting query to ensure a full fresh load.
      await Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Shop updated successfully',
        showConfirmButton: false,
        timer: 900
      });
      const baseUrl = window.location.href.split('?')[0];
      window.location.href = baseUrl + '?_=' + Date.now();

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
