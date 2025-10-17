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
  form.post(route('owner.register'), { forceFormData: true })
}
</script>

<template>
  <Head title="Car Wash Owner Registration" />

  <!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-wrap items-center justify-between px-8 py-2 border-b border-gray-200 text-sm font-semibold">
    <div class="flex items-center gap-2">
      <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mx-auto block" draggable="false" />
    </div>
    <div class="flex gap-8 items-center text-[#002B5C]">
      <div class="flex items-center gap-2">
        <span>📞</span> <span class="font-bold">Call Us</span> <span class="font-normal">+012 345 6789</span>
      </div>
      <div class="flex items-center gap-2">
        <span>✉️</span> <span class="font-bold">Email Us</span> <span class="font-normal">washwise00@gmail.com</span>
      </div>
    </div>
  </div>

  <!-- Navigation Bar -->
  <nav class="w-full bg-[#182235] flex items-center px-8 py-2 text-white font-semibold shadow z-10">
    <ul class="flex gap-8 items-center flex-1">
      <li><Link :href="'/'" class="text-[#FF2D2D] font-bold text-base">Home</Link></li>
    </ul>
    <div class="flex items-center gap-4 ml-8">
      <button @click="openLoginModal" class="text-white font-semibold hover:text-[#FF2D2D] transition text-base">Log in</button>
      <button @click="openRegisterModal" class="px-6 py-2 rounded-full border-2 font-semibold transition" style="border-color:#FF2D2D; color:#FF2D2D;">
        Register
      </button>
    </div>
  </nav>

  <!-- Login Modal -->
  <div v-if="showLoginModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 w-full max-w-xs shadow-lg relative">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Login as:</h3>
      <div class="flex flex-col gap-4">
        <Link :href="route('login')" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Customer</Link>
        <Link href="/owner/login" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div v-if="showRegisterModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 w-full max-w-xs shadow-lg relative">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Register as:</h3>
      <div class="flex flex-col gap-4">
        <Link :href="route('register')" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Customer</Link>
        <Link href="/owner/register" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Owner Register Form -->
  <div class="flex-grow flex items-center justify-center bg-[#F8FAFC] min-h-screen px-4">
    <form
      @submit.prevent="submit"
      enctype="multipart/form-data"
      class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white p-8 rounded-lg shadow-md overflow-y-auto"
      style="max-height: 80vh;"
    >
      <h2 class="text-2xl font-bold text-center text-[#182235]">Register as Owner</h2>
      <p class="text-center text-gray-500 mb-4">Enter your details below to create your account</p>

      <!-- Name -->
      <div>
        <Label for="name" class="text-gray-700 font-medium">Name</Label>
        <input
          id="name"
          type="text"
          required
          autofocus
          autocomplete="name"
          v-model="form.name"
          placeholder="Full name"
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        />
        <InputError :message="form.errors.name" />
      </div>

      <!-- Email -->
      <div>
        <Label for="email" class="text-gray-700 font-medium">Email</Label>
        <input
          id="email"
          type="email"
          required
          autocomplete="email"
          v-model="form.email"
          placeholder="email@example.com"
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        />
        <InputError :message="form.errors.email" />
      </div>

      <!-- Password -->
      <div class="grid gap-1 w-full">
        <Label for="password" class="text-gray-700 font-medium">Password</Label>
        <input
          id="password"
          type="password"
          required
          autocomplete="new-password"
          v-model.trim="form.password"
          name="password"
          placeholder="Password"
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        />
        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
      </div>

      <!-- Confirm Password -->
      <div class="grid gap-1 w-full">
        <Label for="password_confirmation" class="text-gray-700 font-medium">Confirm password</Label>
        <input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          required
          autocomplete="new-password"
          v-model="form.password_confirmation"
          placeholder="Confirm password"
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        />
        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
      </div>
      <!-- District -->
      <div>
        <Label for="district" class="text-gray-700 font-medium">District</Label>
        <select
          v-model="form.district"
          id="district"
          required
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        >
          <option value="">Select District</option>
          <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
        </select>
        <InputError :message="form.errors.district" />
      </div>

      <!-- Address -->
      <div>
        <Label for="address" class="text-gray-700 font-medium">Address</Label>
        <input
          placeholder="Enter address"
          v-model="form.address"
          id="address"
          type="text"
          class="w-full text-black border border-gray-200 px-4 py-2 rounded-lg bg-gray-300"
        />
        <InputError :message="form.errors.address" />
      </div>

      <!-- Photo Uploads -->
      <div>
        <Label for="photo1" class="text-gray-700 font-medium">Business permit picture 1</Label>
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
          class="mt-2 w-32 h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo1" />
      </div>

      <div>
        <Label for="photo2" class="text-gray-700 font-medium">Business permit picture 2</Label>
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
          class="mt-2 w-32 h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo2" />
      </div>

      <div>
        <Label for="photo3" class="text-gray-700 font-medium">Business permit picture 3</Label>
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
          class="mt-2 w-32 h-32 object-cover border rounded"
        />
        <InputError :message="form.errors.photo3" />
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="mt-2 w-full"
        style="background:#FF2D2D; color:#fff; font-weight:600; padding:0.5rem; border-radius:0.5rem;"
        :disabled="form.processing"
      >
        Register
      </button>
    </form>
  </div>
</template>
