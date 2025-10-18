<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, Ref } from 'vue'
import InputError from '@/components/InputError.vue'
import { Label } from '@/components/ui/label'

// Modal states
const showLoginModal = ref(false)
const showRegisterModal = ref(false)

function openLoginModal() {
  showLoginModal.value = true
}
function openRegisterModal() {
  showRegisterModal.value = true
}
function closeModals() {
  showLoginModal.value = false
  showRegisterModal.value = false
}

// Form setup
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  district: '',
  address: '',
  photo1: null as File | null,
  photo2: null as File | null,
  photo3: null as File | null,
})

const preview1 = ref<string | null>(null)
const preview2 = ref<string | null>(null)
const preview3 = ref<string | null>(null)

function handleFileChange(e: Event, key: 'photo1' | 'photo2' | 'photo3', preview: Ref<string | null>) {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form[key] = target.files[0]
    preview.value = URL.createObjectURL(target.files[0])
  }
}

function handlePhoto1Change(e: Event) {
  handleFileChange(e, 'photo1', preview1)
}

function handlePhoto2Change(e: Event) {
  handleFileChange(e, 'photo2', preview2)
}

function handlePhoto3Change(e: Event) {
  handleFileChange(e, 'photo3', preview3)
}

function submit() {
  if (!agreedToTerms.value) {
    alert('You must agree to the Terms and Conditions to register.')
    return
  }

  form.post(route('owner.register'), { forceFormData: true })
}
const showTermsModal = ref(false)
const agreedToTerms = ref(false)

function openTermsModal() {
  showTermsModal.value = true
}

function acceptTerms() {
  agreedToTerms.value = true
  showTermsModal.value = false
}
</script>

<template>
  <Head title="Car Wash Owner Registration" />

  <!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-wrap items-center justify-between px-4 sm:px-8 py-2 border-b border-gray-200 text-sm font-semibold">
    <div class="flex items-center gap-2">
      <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-12 sm:h-14 w-auto mx-auto block" draggable="false" />
    </div>
    <div class="flex gap-6 items-center text-[#002B5C] text-xs sm:text-sm">
      <div class="flex items-center gap-2">
        <span>📞</span> <span class="font-bold">Call Us</span> <span class="font-normal">+012 345 6789</span>
      </div>
      <div class="flex items-center gap-2">
        <span>✉️</span> <span class="font-bold">Email Us</span> <span class="font-normal">washwise00@gmail.com</span>
      </div>
    </div>
  </div>

  <!-- Navigation Bar -->
  <nav class="w-full bg-[#182235] flex items-center px-4 sm:px-8 py-2 text-white font-semibold shadow z-10">
    <ul class="flex gap-4 items-center flex-1">
      <li><Link :href="'/'" class="text-[#FF2D2D] font-bold text-base">Home</Link></li>
    </ul>
    <div class="flex items-center gap-4 ml-4">
      <button @click="openLoginModal" class="text-white font-semibold hover:text-[#FF2D2D] transition text-base">Log in</button>
      <button @click="openRegisterModal" class="px-4 py-2 rounded-full border-2 font-semibold transition" style="border-color:#FF2D2D; color:#FF2D2D;">
        Register
      </button>
    </div>
  </nav>

  <!-- Login Modal -->
  <div v-if="showLoginModal" class="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50">
    <div class="bg-white rounded-t-lg sm:rounded-lg p-4 sm:p-8 w-full sm:max-w-xs shadow-lg relative overflow-auto">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Login as:</h3>
      <div class="flex flex-col gap-4">
        <Link :href="route('login')" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Customer</Link>
        <Link href="/owner/login" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div v-if="showRegisterModal" class="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50">
    <div class="bg-white rounded-t-lg sm:rounded-lg p-4 sm:p-8 w-full sm:max-w-xs shadow-lg relative overflow-auto">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Register as:</h3>
      <div class="flex flex-col gap-4">
        <Link :href="route('register')" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Customer</Link>
        <Link href="/owner/register" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Owner Register Form -->
  <div class="flex-grow flex items-center justify-center bg-[#F8FAFC] min-h-screen px-4">
    <form
      @submit.prevent="submit"
      enctype="multipart/form-data"
      class="flex flex-col gap-4 w-full max-w-lg mx-auto bg-white p-4 sm:p-8 rounded-lg shadow-md overflow-y-auto"
      style="max-height: 90vh;"
    >
      <h2 class="text-2xl font-bold text-center text-[#182235]">Register as Owner</h2>
      <p class="text-center text-gray-500 mb-4">Enter your details below to create your account</p>

      <!-- Name -->
      <div>
        <Label for="name" class="text-gray-700 font-medium text-sm">Name</Label>
        <input
          id="name"
          type="text"
          required
          autofocus
          autocomplete="name"
          v-model="form.name"
          placeholder="Full name"
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        />
        <InputError :message="form.errors.name" />
      </div>

      <!-- Email -->
      <div>
        <Label for="email" class="text-gray-700 font-medium text-sm">Email</Label>
        <input
          id="email"
          type="email"
          required
          autocomplete="email"
          v-model="form.email"
          placeholder="email@example.com"
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        />
        <InputError :message="form.errors.email" />
      </div>

      <!-- Password -->
      <div class="grid gap-1 w-full">
        <Label for="password" class="text-gray-700 font-medium text-sm">Password</Label>
        <input
          id="password"
          type="password"
          required
          autocomplete="new-password"
          v-model.trim="form.password"
          name="password"
          placeholder="Password"
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        />
        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
      </div>

      <!-- Confirm Password -->
      <div class="grid gap-1 w-full">
        <Label for="password_confirmation" class="text-gray-700 font-medium text-sm">Confirm password</Label>
        <input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          required
          autocomplete="new-password"
          v-model="form.password_confirmation"
          placeholder="Confirm password"
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        />
        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
      </div>
      <!-- District -->
      <div>
        <Label for="district" class="text-gray-700 font-medium text-sm">District</Label>
        <select
          v-model="form.district"
          id="district"
          required
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        >
          <option value="">Select District</option>
          <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
        </select>
        <InputError :message="form.errors.district" />
      </div>

      <!-- Address -->
      <div>
        <Label for="address" class="text-gray-700 font-medium text-sm">Address</Label>
        <input
          placeholder="Enter address"
          v-model="form.address"
          id="address"
          type="text"
          class="w-full text-black border border-gray-200 px-4 py-3 rounded-lg bg-gray-100"
        />
        <InputError :message="form.errors.address" />
      </div>

      <!-- Photo Uploads -->
      <div>
        <Label for="photo1" class="text-gray-700 font-medium text-sm">Business permit picture 1</Label>
        <input
          type="file"
          id="photo1"
          @change="handlePhoto1Change"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4"
        />
        <img
          v-if="preview1"
          :src="preview1"
          alt="Photo 1 preview"
          class="mt-2 w-28 h-28 sm:w-32 sm:h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo1" />
      </div>

      <div>
        <Label for="photo2" class="text-gray-700 font-medium text-sm">Business permit picture 2</Label>
        <input
          type="file"
          id="photo2"
          @change="handlePhoto2Change"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4"
        />
        <img
          v-if="preview2"
          :src="preview2"
          alt="Photo 2 preview"
          class="mt-2 w-28 h-28 sm:w-32 sm:h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo2" />
      </div>

      <div>
        <Label for="photo3" class="text-gray-700 font-medium text-sm">Business permit picture 3</Label>
        <input
          type="file"
          id="photo3"
          @change="handlePhoto3Change"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4"
        />
        <img
          v-if="preview3"
          :src="preview3"
          alt="Photo 3 preview"
          class="mt-2 w-28 h-28 sm:w-32 sm:h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo3" />
      </div>
<!-- Terms and Conditions Checkbox -->
<div class="flex items-start gap-2 mt-2">
  <input
    id="terms"
    type="checkbox"
    v-model="agreedToTerms"
    class="mt-1 w-4 h-4 rounded border-gray-300 text-[#FF2D2D] focus:ring-[#FF2D2D]"
  />
  <label for="terms" class="text-gray-700 text-sm">
    I agree to the
    <button type="button" @click="openTermsModal" class="text-[#FF2D2D] underline">
      Terms and Conditions
    </button>
  </label>
</div>
      <!-- Submit Button -->
      <button
        type="submit"
        class="mt-2 w-full py-3"
        style="background:#FF2D2D; color:#fff; font-weight:600; border-radius:0.5rem;"
        :disabled="form.processing"
      >
        Register
      </button>

    </form>
    <!-- Terms Modal -->
<div
  v-if="showTermsModal"
  class="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50 px-4"
>
  <div class="bg-white w-full max-w-lg rounded-t-2xl sm:rounded-2xl shadow-xl p-4 sm:p-6 relative overflow-y-auto max-h-[80vh]">
    <h2 class="text-xl font-bold text-[#002B5C] mb-4">WashWise Terms and Conditions</h2>
    <div class="text-gray-700 space-y-2 text-sm">
      <p>By signing up for an account on WashWise, you agree to the following terms:</p>
      <ul class="list-disc ml-5 space-y-1">
        <li>You certify that the information you provide is accurate and truthful.</li>
        <li>You are responsible for keeping your account credentials confidential.</li>
        <li>You agree to use the system only for booking appointments with verified car wash establishments.</li>
        <li>Appointments must be managed responsibly; frequent cancellations or no-shows may result in limited access.</li>
        <li>WashWise serves as a booking platform and is not liable for service issues rendered by third-party car wash providers.</li>
        <li>Your personal data will be processed in accordance with our Privacy Policy and applicable data protection laws.</li>
        <li>Any misuse of the platform (e.g., fraud, impersonation, or system abuse) may lead to account suspension or termination.</li>
      </ul>
      <p class="mt-2">By clicking "I Agree", you confirm that you have read, understood, and accepted these terms.</p>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <button
        @click="showTermsModal = false"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400"
      >
        Cancel
      </button>
      <button
        @click="acceptTerms"
        class="px-5 py-2 bg-[#FF2D2D] text-white font-semibold rounded-lg hover:bg-[#E02626]"
      >
        I Agree
      </button>
    </div>
  </div>
</div>
  </div>
</template>

<style>
/* Keep the same alerts styles */
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

/* Small tweaks to improve tap targets on mobile */
input[type="file"] { touch-action: manipulation; }
button { -webkit-tap-highlight-color: transparent; }
</style>
