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
          <div class="mt-1 flex items-center">
            <input type="file" @change="handleLogoChange" accept="image/*"
              class="w-full p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#002B5C] file:text-white hover:file:bg-[#003b7a] cursor-pointer" />
          </div>
          <!-- Logo Preview -->
          <div v-if="logoPreview" class="mt-3">
            <img :src="logoPreview" alt="Logo Preview" class="h-24 w-24 object-contain rounded-lg border border-gray-300 shadow-sm" />
          </div>
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
  <label class="block text-sm font-semibold text-gray-700">Services Offered Image</label>
  <input
    type="file"
    accept="image/*"
    @change="handleServicesOfferedChange"
    class="w-full mt-1 p-3 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#002B5C] file:text-white hover:file:bg-[#003b7a] cursor-pointer"
  />
  <div v-if="servicesOfferedPreview" class="mt-3">
    <img :src="servicesOfferedPreview" alt="Services Offered Preview" class="h-24 w-24 object-contain rounded-lg border border-gray-300 shadow-sm" />
  </div>
  <div v-if="form.errors.services_offered" class="text-red-600 text-sm mt-1">{{ form.errors.services_offered }}</div>
</div>

<!-- QR Codes -->
<!-- ✅ QR Code 1 -->
<div class="mt-4">
  <label class="block text-sm font-semibold text-gray-700">QR Code 1</label>
  <input
    type="file"
    id="qr_code"
    name="qr_code"
    accept="image/*"
    @change="(e) => handleQrCodeChange(e, 1)"
    class="mt-1 w-full text-gray-700 "
    required
  />
  <img
    v-if="qrPreviews[0]"
    :src="qrPreviews[0]"
    alt="QR 1 Preview"
    class="h-16 w-16 object-contain border border-gray-300 rounded-lg mt-2"
  />
</div>

<!-- ✅ QR Code 2 -->
<div class="mt-4">
  <label class="block text-sm font-semibold text-gray-700">QR Code 2</label>
  <input
    type="file"
    id="qr_code2"
    name="qr_code2"
    accept="image/*"
    @change="(e) => handleQrCodeChange(e, 2)"
    class="mt-1 w-full text-gray-700"
  />
  <img
    v-if="qrPreviews[1]"
    :src="qrPreviews[1]"
    alt="QR 2 Preview"
    class="h-16 w-16 object-contain border border-gray-300 rounded-lg mt-2"
  />
</div>

<!-- ✅ QR Code 3 -->
<div class="mt-4">
  <label class="block text-sm font-semibold text-gray-700">QR Code 3</label>
  <input
    type="file"
    id="qr_code3"
    name="qr_code3"
    accept="image/*"
    @change="(e) => handleQrCodeChange(e, 3)"
    class="mt-1 w-full text-gray-700"
  />
  <img
    v-if="qrPreviews[2]"
    :src="qrPreviews[2]"
    alt="QR 3 Preview"
    class="h-16 w-16 object-contain border border-gray-300 rounded-lg mt-2"
  />
</div>

<!-- ✅ QR Code 4 -->
<div class="mt-4">
  <label class="block text-sm font-semibold text-gray-700">QR Code 4</label>
  <input
    type="file"
    id="qr_code4"
    name="qr_code4"
    accept="image/*"
    @change="(e) => handleQrCodeChange(e, 4)"
    class="mt-1 w-full text-gray-700"
  />
  <img
    v-if="qrPreviews[3]"
    :src="qrPreviews[3]"
    alt="QR 4 Preview"
    class="h-16 w-16 object-contain border border-gray-300 rounded-lg mt-2"
  />
</div>

<!-- ✅ QR Code 5 -->
<div class="mt-4">
  <label class="block text-sm font-semibold text-gray-700">QR Code 5</label>
  <input
    type="file"
    id="qr_code5"
    name="qr_code5"
    accept="image/*"
    @change="(e) => handleQrCodeChange(e, 5)"
    class="mt-1 w-full text-gray-700"
  />
  <img
    v-if="qrPreviews[4]"
    :src="qrPreviews[4]"
    alt="QR 5 Preview"
    class="h-16 w-16 object-contain border border-gray-300 rounded-lg mt-2"
  />
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
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6">

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

const qrPreviews = ref<(string | null)[]>([null, null, null, null, null])

const servicesOfferedPreview = ref<string | null>(null)

const handleServicesOfferedChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files?.length) {
    form.services_offered = target.files[0] as any
    servicesOfferedPreview.value = URL.createObjectURL(target.files[0])
  } else {
    form.services_offered = '' as any
    servicesOfferedPreview.value = null
  }
}
defineProps<{ pageTitle: string }>()

type ShopForm = {
  name: string
  address: string | null
  district: number | null
  logo: File | null
  description: string
  services_offered: File | null
  qr_code: File | null
  qr_code2: File | null
  qr_code3: File | null
  qr_code4: File | null
  qr_code5: File | null
}

const form = useForm<ShopForm>({
  name: '',
  address: null,
  district: null,
  logo: null,
  description: '',
  services_offered: null,
  qr_code: null,
  qr_code2: null,
  qr_code3: null,
  qr_code4: null,
  qr_code5: null,
})

// Logo Preview
const logoPreview = ref<string | null>(null)

const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files?.length) {
    form.logo = target.files[0]
    logoPreview.value = URL.createObjectURL(target.files[0])
  } else {
    form.logo = null
    logoPreview.value = null
  }
}

// ✅ Fix here
const handleQrCodeChange = (event: Event, index: number) => {
  const target = event.target as HTMLInputElement
  const key = index === 1 ? 'qr_code' : `qr_code${index}` as keyof ShopForm

  if (target.files?.length) {
    const file = target.files[0]
    ;(form as unknown as Record<string, File | null>)[key] = file
    qrPreviews.value[index - 1] = URL.createObjectURL(file)
  } else {
    ;(form as unknown as Record<string, File | null>)[key] = null
    qrPreviews.value[index - 1] = null
  }
}

const submit = () => {
  form.post(route('owner.shop.store'), {
    onSuccess: () => {
      console.log('Shop created successfully')
      logoPreview.value = null
    },
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
