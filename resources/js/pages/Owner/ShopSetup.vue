<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 px-4 flex justify-center items-center">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">

      <!-- Title -->
      <h1 class="text-2xl font-bold text-center text-[#002B5C] mb-6">
        {{ pageTitle }}
      </h1>

      <!-- Success & Error Alerts -->
      <div v-if="$page.props.flash.success" class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm shadow">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm shadow">
        {{ $page.props.flash.error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">
        <!-- Name -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Name</label>
          <input type="text" v-model="form.name"
            class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
            placeholder="Enter shop name"
            required />
          <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <!-- Address -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Address</label>
          <input type="text" v-model="form.address"
            class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
            placeholder="Enter shop address"
            required />
          <div v-if="form.errors.address" class="text-red-600 text-sm mt-1">{{ form.errors.address }}</div>
        </div>

        <!-- District -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">District</label>
          <input type="number" v-model.number="form.district" min="1" max="6"
            class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
            placeholder="1-6"
            required />
          <div v-if="form.errors.district" class="text-red-600 text-sm mt-1">{{ form.errors.district }}</div>
        </div>

        <!-- Logo -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Logo</label>
          <input type="file" @change="handleLogoChange" accept="image/*"
            class="w-full text-gray-700 mt-1" />
          <div v-if="form.errors.logo" class="text-red-600 text-sm mt-1">{{ form.errors.logo }}</div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Description</label>
          <textarea v-model="form.description"
            class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
            placeholder="Write shop description"></textarea>
          <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
        </div>

        <!-- Services Offered -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Services Offered</label>
          <textarea v-model="form.services_offered"
            class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
            placeholder="List services"></textarea>
          <div v-if="form.errors.services_offered" class="text-red-600 text-sm mt-1">{{ form.errors.services_offered }}</div>
        </div>

        <!-- QR Code -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">QR Code</label>
          <input type="file" @change="handleQrCodeChange" accept="image/*"
            class="w-full text-gray-700 mt-1" />
          <div v-if="form.errors.qr_code" class="text-red-600 text-sm mt-1">{{ form.errors.qr_code }}</div>
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full bg-[#FF2D2D] text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition shadow-lg disabled:opacity-50"
          :disabled="form.processing">
          <span v-if="form.processing">Creating...</span>
          <span v-else>Create Shop</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{ pageTitle: string }>()

const form = useForm({
  name: '',
  address: null as string | null,
  district: null as number | null,
  logo: null as File | null,
  description: '',
  services_offered: '',
  qr_code: null as File | null,
})

const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.logo = target.files[0]
  }
}

const handleQrCodeChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.qr_code = target.files[0]
  }
}

const submit = () => {
  form.post(route('owner.shop.store'), {
    onSuccess: () => console.log('Shop created successfully'),
    onError: (errors) => console.error('Shop creation error:', errors),
  })
}
</script>

<style>
/* Alerts */
.alert-success {
  background-color: #d4edda;
  color: #155724;
  padding: 1rem;
  border-radius: 0.25rem;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 0.25rem;
}

/* Error text */
.text-red-600 {
  color: #721c24;
  font-size: 0.875rem;
}

/* Input/textarea shadow for better design */
input, textarea {
  transition: all 0.2s;
}
input:focus, textarea:focus {
  box-shadow: 0 0 0 3px rgba(0, 43, 92, 0.2);
}

/* Button */
.btn-primary {
  background-color: #FF2D2D;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
}
</style>
