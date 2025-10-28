<template>
  <!-- QR Image Preview Modal -->
  <div
    v-if="showQrPreview"
    class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
  >
    <img
      :src="qrPreviewSrc"
      alt="QR Code Preview"
      class="max-h-[80vh] rounded-xl shadow-2xl border-4 border-white"
    />
    <button
      @click="showQrPreview = false"
      class="absolute top-6 right-8 text-white text-3xl font-bold"
    >
      ✕
    </button>
  </div>

  <div class="min-h-screen py-10 px-4 flex justify-center bg-[#F8FAFC]">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-lg p-6">
      <h1 class="text-3xl font-extrabold text-center text-[#002B5C] mb-6 tracking-tight">
        Confirm & Pay for <span class="text-[#FF2D2D]">{{ shop.name }}</span>
      </h1>

      <div v-if="$page.props.flash.success" class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-base font-medium">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-base font-medium">
        {{ $page.props.flash.error }}
      </div>

      <div v-if="error || !booking" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-base font-medium">
        {{ error || 'Please submit booking details to proceed with payment.' }}
        <div class="mt-2">
          <a :href="`/shop/${shop.id}/book`" class="text-[#002B5C] underline font-semibold">Go to Booking Form</a>
        </div>
      </div>

      <!-- Booking Info -->
      <div v-if="booking" class="space-y-4 mb-6">
        <div class="mb-6 text-center">
          <h2 class="text-base font-bold text-[#182235] mb-2">
            Scan to Pay <span class="font-normal text-gray-500">(QR Codes)</span>
          </h2>

          <!-- QR Codes Table (like previous page) -->
          <div class="overflow-x-auto">
            <table class="min-w-full border rounded">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-center">QR Code 1</th>
                  <th class="px-3 py-2 text-center">QR Code 2</th>
                  <th class="px-3 py-2 text-center">QR Code 3</th>
                  <th class="px-3 py-2 text-center">QR Code 4</th>
                  <th class="px-3 py-2 text-center">QR Code 5</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="px-3 py-3 text-center">
                    <img v-if="shop.qr_code" :src="shop.qr_code" class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition" @click="openQr(shop.qr_code)" />
                  </td>
                  <td class="px-3 py-3 text-center">
                    <img v-if="shop.qr_code2" :src="shop.qr_code2" class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition" @click="openQr(shop.qr_code2)" />
                  </td>
                  <td class="px-3 py-3 text-center">
                    <img v-if="shop.qr_code3" :src="shop.qr_code3" class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition" @click="openQr(shop.qr_code3)" />
                  </td>
                  <td class="px-3 py-3 text-center">
                    <img v-if="shop.qr_code4" :src="shop.qr_code4" class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition" @click="openQr(shop.qr_code4)" />
                  </td>
                  <td class="px-3 py-3 text-center">
                    <img v-if="shop.qr_code5" :src="shop.qr_code5" class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition" @click="openQr(shop.qr_code5)" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-sm text-gray-500 mt-2">Click a QR code to preview.</p>
        </div>

        <!-- Booking Details (like previous page) -->
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Name:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.name }}</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Email:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.email }}</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Size:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.size_of_the_car }}</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Contact:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.contact_no }}</p>
        </div>
        <div class="flex gap-4">
          <div>
            <h3 class="text-sm font-semibold text-gray-700">Date:</h3>
            <p class="text-base text-[#182235] font-medium">{{ booking.date_of_booking }}</p>
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-700">Time:</h3>
            <p class="text-base text-[#182235] font-medium">{{ booking.time_of_booking }}</p>
          </div>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Slot Number:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.slot_number }}</p>
        </div>
        <div v-if="booking.services_offered">
          <h3 class="text-sm font-semibold text-gray-700">Service offered:</h3>
          <p class="text-base text-[#182235] font-medium">{{ booking.services_offered }}</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Payment Amount:</h3>
          <p class="text-base text-[#FF2D2D] font-bold">PHP 50.00</p>
        </div>
      </div>

      <!-- Payment Form -->
      <form v-if="booking" @submit.prevent="confirm" class="space-y-4" enctype="multipart/form-data">
        <div>
          <label class="block text-base font-bold text-[#182235] mb-2">
            Upload Payment Proof: <span class="font-normal text-gray-500">(screenshot of receipt)</span>
          </label>
          <input
            @change="handleFile"
            type="file"
            accept="image/jpeg,image/png"
            class="w-full mt-1 p-2 border-2 border-[#182235] bg-[#F8FAFC] text-[#182235] rounded-lg font-semibold cursor-pointer"
            required
          />
          <div v-if="form.errors.payment_proof" class="text-red-600 text-sm mt-1">{{ form.errors.payment_proof }}</div>
        </div>

        <button
          type="submit"
          class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-bold text-lg hover:opacity-90 transition disabled:opacity-50"
          :disabled="form.processing"
        >
          <span v-if="form.processing">Booking...</span>
          <span v-else>Confirm Booking</span>
        </button>
      </form>
    </div>
  </div>
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-6">

    <!-- About -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">About WashWise</h2>
      <p class="text-sm leading-relaxed">
        WashWise is your trusted platform for booking car wash services — connecting customers and business owners for a smoother, cleaner experience every day.
      </p>
    </div>
    <!-- Contact Info -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">Contact Us</h2>
      <ul class="text-sm space-y-1">
        <li>📞 +63 992 759 4673</li>
        <li>✉️ washwise00@gmail.com</li>
      </ul>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="border-t border-gray-700 mt-8 pt-4 text-center text-xs text-gray-400">
    © {{ new Date().getFullYear() }} WashWise. All rights reserved.
    <br class="sm:hidden" /> Developed by <span class="text-[#FF2D2D] font-semibold">Washwise Team.</span>
  </div>
</footer>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

interface Shop {
  id: number
  name: string
  qr_code?: string
  qr_code2?: string
  qr_code3?: string
  qr_code4?: string
  qr_code5?: string
}

interface Booking {
  name: string
  email: string
  size_of_the_car: string
  contact_no: string
  date_of_booking: string
  time_of_booking: string
  slot_number: number
  services_offered?: string
}

interface Props {
  shop: Shop
  booking: Booking | null
  pageTitle: string
  error?: string
}

const props = defineProps<Props>()


// QR Modal
const showQrPreview = ref(false)
const qrPreviewSrc = ref('')
const openQr = (src: string) => {
  if (!src) return
  qrPreviewSrc.value = src
  showQrPreview.value = true
}

// Form
const form = useForm({
  name: props.booking?.name ?? '',
  email: props.booking?.email ?? '',
  size_of_the_car: props.booking?.size_of_the_car ?? '',
  contact_no: props.booking?.contact_no ?? '',
  date_of_booking: props.booking?.date_of_booking ?? '',
  time_of_booking: props.booking?.time_of_booking ?? '',
  slot_number: props.booking?.slot_number ?? 1,
  payment_amount: 50,
  payment_proof: null as File | null,
  services_offered: props.booking?.services_offered ?? ''
})

const handleFile = (e: Event) => {
  const input = e.target as HTMLInputElement
  if (input?.files?.[0]) {
    form.payment_proof = input.files[0]
  }
}


const confirm = () => {
  if (!form.payment_proof) {
    form.errors.payment_proof = 'Please upload payment proof (screenshot).'
    return
  }

  form.post(`/customer/book/${props.shop.id}/confirm`, {
    forceFormData: true,
    onSuccess: () => Inertia.visit('/settings/appearance'),
    onError: (errors) => console.error('Payment submission error:', errors),
  })
}

</script>
