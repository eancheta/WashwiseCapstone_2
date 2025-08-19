<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4 flex justify-center">
    <div v-if="shop" class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-8">
      <!-- Title -->
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

        <div class="mb-3 block text-sm font-semibold text-gray-700">
          <label>Size of the Car: </label>
          <select v-model="form.size_of_the_car" class="form-control" required>

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
          <input type="number" v-model.number="form.slot_number" min="1"
            class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
            required />
          <div v-if="form.errors.slot_number" class="text-red-600 text-sm mt-1">{{ form.errors.slot_number }}</div>
        </div>

        <!-- Button -->
        <button type="submit"
          class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-semibold hover:bg-red-700 transition disabled:opacity-50"
          :disabled="form.processing">
          <span v-if="form.processing">Booking...</span>
          <span v-else>Book Now</span>
        </button>
      </form>
    </div>

    <!-- If shop not found -->
    <div v-else class="text-red-600 font-medium">Shop not found. Please select a valid shop.</div>
  </div>
</template>


<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

interface Shop {
  id: number
  name: string
}

const props = defineProps<{
  shop: Shop | null
  errors: { [key: string]: string }
}>()

const form = useForm({
  name: '',
  size_of_the_car: '',
  contact_no: '',
  time_of_booking: '',
  date_of_booking: '',
  slot_number: 1
})

const submit = () => {
  console.log('Submitting booking for shop ID:', props.shop?.id)
  form.post(`/customer/book/${props.shop?.id}`, {
    onSuccess: () => console.log('Booking submitted successfully'),
    onError: (errors) => console.error('Booking submission error:', errors)
  })
}
</script>
