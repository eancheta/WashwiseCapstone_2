<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4 flex justify-center">
    <div v-if="shop" class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Form Section -->
      <div class="space-y-5">
        <h1 class="text-2xl font-bold text-center text-[#002B5C] mb-6">
          Book at {{ shop.name }}
        </h1>

        <!-- Success & Error Alerts -->
        <div v-if="$page.props.flash.success" class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm">
          {{ $page.props.flash.error }}
        </div>
        <div v-if="shopError" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm">
          {{ shopError }}
        </div>
        <div v-if="overlapError" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm">
          {{ overlapError }}
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Your Name</label>
            <input type="text" v-model="form.name"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Email Address</label>
            <input type="email" v-model="form.email"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Size of the Car -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Size of the Car</label>
            <select v-model="form.size_of_the_car"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required>
              <option value="">Select</option>
              <option value="HatchBack">HatchBack</option>
              <option value="Sedan">Sedan</option>
              <option value="MPV">MPV</option>
              <option value="SUV">SUV</option>
              <option value="Pickup">Pickup</option>
              <option value="Van">Van</option>
              <option value="Motorcycle">Motorcycle</option>
            </select>
            <div v-if="form.errors.size_of_the_car" class="text-red-600 text-sm mt-1">{{ form.errors.size_of_the_car }}</div>
          </div>

          <!-- Contact -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Contact Number</label>
            <input type="text" v-model="form.contact_no"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.contact_no" class="text-red-600 text-sm mt-1">{{ form.errors.contact_no }}</div>
          </div>

          <!-- Time -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Time of Booking</label>
            <input type="time" v-model="form.time_of_booking"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.time_of_booking" class="text-red-600 text-sm mt-1">{{ form.errors.time_of_booking }}</div>
          </div>

          <!-- Date -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Date of Booking</label>
            <input type="date" v-model="form.date_of_booking"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.date_of_booking" class="text-red-600 text-sm mt-1">{{ form.errors.date_of_booking }}</div>
          </div>

          <!-- Slot -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Slot Number</label>
            <input type="number" v-model.number="form.slot_number" min="1" max="4"
              class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
              required />
            <div v-if="form.errors.slot_number" class="text-red-600 text-sm mt-1">{{ form.errors.slot_number }}</div>
          </div>

          <!-- Button -->
          <button type="submit"
            class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-semibold hover:bg-red-700 transition disabled:opacity-50"
            :disabled="form.processing || hasOverlap">
            <span v-if="form.processing">Processing...</span>
            <span v-else>Pay Now</span>
          </button>
        </form>
      </div>

      <!-- Shop Details Section -->
      <div class="space-y-5">
        <h2 class="text-xl font-semibold text-[#002B5C] mb-4">Shop Details</h2>

        <div class="flex justify-center">
          <img :src="shop.logo ? `${backendBaseUrl}/storage/${shop.logo}` : `${backendBaseUrl}/images/default-carwash.png`"
            @error="handleImageError($event, `${backendBaseUrl}/images/default-carwash.png`)"
            alt="Shop Logo"
            class="h-24 w-24 object-contain rounded-lg border border-gray-200" />
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">Name</h3>
          <p class="text-gray-900">{{ shop.name }}</p>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">Address</h3>
          <p class="text-gray-900">{{ shop.address }}</p>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">District</h3>
          <p class="text-gray-900">{{ shop.district || 'Not specified' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">Description</h3>
          <p class="text-gray-900">{{ shop.description || 'No description available' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">Services Offered</h3>
          <p class="text-gray-900">{{ shop.services_offered || 'No services listed' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-700">QR Code</h3>
          <img :src="shop.qr_code ? `${backendBaseUrl}/storage/${shop.qr_code}` : `${backendBaseUrl}/images/default-qr.png`"
            @error="handleImageError($event, `${backendBaseUrl}/images/default-qr.png`)"
            alt="Shop QR Code"
            class="h-32 w-32 object-contain rounded-lg border border-gray-200" />
        </div>
      </div>
    </div>

    <!-- If shop not found -->
    <div v-else class="text-red-600 font-medium">Shop not found. Please select a valid shop.</div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

interface Shop {
  id: number
  name: string
  address: string
  district?: string
  logo?: string
  description?: string
  services_offered?: string
  qr_code?: string
}

interface Props {
  shop: Shop | null
  errors: Record<string, string>
  bookings: Array<{ time_of_booking: string; slot_number: number }>
}

const props = defineProps<Props>()

const backendBaseUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost/Washwise'

const form = useForm({
  name: '',
  email: '',
  size_of_the_car: '',
  contact_no: '',
  time_of_booking: '',
  date_of_booking: '',
  slot_number: 1
})

const shopError = ref<string>('')
const overlapError = ref<string>('')

const hasOverlap = computed(() => {
  if (!form.date_of_booking || !form.time_of_booking || !form.slot_number) return false
  const newStart = new Date(`${form.date_of_booking}T${form.time_of_booking}:00`)
  const newEnd = new Date(newStart.getTime() + 3 * 60 * 60 * 1000)

  return props.bookings.some(booking => {
    const start = new Date(`${form.date_of_booking}T${booking.time_of_booking}:00`)
    const end = new Date(start.getTime() + 3 * 60 * 60 * 1000)
    return booking.slot_number === form.slot_number && start < newEnd && newStart < end
  })
})

const handleImageError = (event: Event, fallbackSrc: string) => {
  const target = event.target as HTMLImageElement | null
  if (target) {
    target.src = fallbackSrc
  }
}

const submit = () => {
  shopError.value = ''
  overlapError.value = ''
  if (!props.shop?.id) {
    shopError.value = 'Invalid shop ID. Please select a valid shop.'
    return
  }
  if (hasOverlap.value) {
    overlapError.value = 'This slot is already booked within the next 3 hours. Please choose a different time or slot.'
    return
  }
  console.log('Submitting booking for shop ID:', props.shop.id, 'at', form.date_of_booking, form.time_of_booking, 'slot', form.slot_number)
  form.post(`/customer/book/${props.shop.id}/payment`, {
    onSuccess: () => console.log('Booking submitted successfully, redirected to payment'),
    onError: (errors) => {
      console.error('Booking submission errors:', errors)
      if (Object.keys(errors).length > 0) {
        form.errors = { ...form.errors, ...errors }
      } else {
        overlapError.value = 'An unexpected error occurred. Please try again.'
      }
    }
  })
}

watch(() => props.shop, (newShop) => {
  console.log('Shop updated:', newShop)
})
</script>
