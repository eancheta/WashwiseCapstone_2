<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'


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

const form = useForm({ email: '', password: '', remember: false })

function submit() {
  form.post(route('owner.login'))
}
</script>

<template>
  <Head title="Business Owner Login" />

  <!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-wrap items-center justify-between px-8 py-2 border-b border-gray-200 text-sm font-semibold">
    <div class="flex items-center gap-2">
      <img
        src="/images/washwiselogo2.png"
        alt="WashWise Logo"
        class="h-14 w-auto mx-auto block"
        draggable="false"
      />
    </div>
    <div class="flex gap-8 items-center text-[#002B5C]">
      <div class="flex items-center gap-2">
        <span>📞</span> <span class="font-bold">Call Us</span> <span class="font-normal">+012 345 6789</span>
      </div>
      <div class="flex items-center gap-2">
        <span>✉️</span> <span class="font-bold">Email Us</span> <span class="font-normal">info@example.com</span>
      </div>
    </div>
  </div>

  <!-- Navigation Bar -->
  <nav class="w-full bg-[#182235] flex items-center px-8 py-2 text-white font-semibold shadow z-10">
    <ul class="flex gap-8 items-center flex-1">
      <li><Link :href="'/'" class="text-[#FF2D2D] font-bold text-base">Home</Link></li>
      <li><Link href="/about-us" class="text-base">About</Link></li>
      <li><Link :href="'#'" class="text-base">Service</Link></li>
    </ul>
    <div class="flex items-center gap-4 ml-8">
      <button
        @click="openLoginModal"
        class="text-white font-semibold hover:text-[#FF2D2D] transition text-base"
        style="background: none; border: none; cursor: pointer;"
      >
        Log in
      </button>
      <button
        @click="openRegisterModal"
        class="px-6 py-2 rounded-full border-2 font-semibold transition"
        style="border-color:#FF2D2D; color:#FF2D2D; font-size: 1rem; background: none; border-width: 2px; cursor: pointer;"
      >
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
        <Link :href="route('login')" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">User</Link>
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
        <Link :href="route('register')" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">User</Link>
        <Link href="/owner/register" class="px-4 py-2 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

<!-- Owner Login Form -->
<div class="flex-grow flex items-start justify-center bg-[#F8FAFC] min-h-screen pt-16">
  <form
    @submit.prevent="submit"
    class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white px-8 py-6 rounded-lg shadow-md"
  >
    <h2 class="text-2xl font-bold text-center text-[#182235]">Log in as Owner</h2>
    <p class="text-center text-gray-500 mb-4">Enter your credentials below to log in</p>

    <div class="grid gap-3 w-full">
      <!-- Email -->
      <div class="grid gap-1 w-full">
        <Label for="email" class="text-gray-700 font-medium">Email address</Label>
        <input
          id="email"
          type="email"
          required
          autofocus
          autocomplete="email"
          v-model="form.email"
          placeholder="owner@email.com"
          class="w-full text-black border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF2D2D] transition"
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
          autocomplete="current-password"
          v-model="form.password"
          placeholder="Password"
          class="w-full text-black border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF2D2D] transition"
        />
        <InputError :message="form.errors.password" />
      </div>

      <!-- Remember Me -->
      <div class="flex items-center gap-2">
        <input
          id="remember"
          type="checkbox"
          v-model="form.remember"
          class="rounded border-gray-300 text-[#FF2D2D] shadow-sm focus:ring focus:ring-[#FF2D2D]"
        />
        <Label for="remember" class="text-sm text-gray-600">Remember me</Label>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="mt-2 w-full"
       style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
        :disabled="form.processing"

      >
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Log in
      </button>
    </div>

    <!-- Link to Register -->
    <div class="text-center text-sm mt-2">
      <span class="text-gray-600">Don't have an account?</span>
      <Link
        href="/owner/register"
        class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D] [text-decoration:none]"
        style="color:#002B5C;"
      >
        Register
      </Link>
    </div>
    <!-- Forgot Password Link -->
    <div class="text-center text-sm mt-1">
      <Link
        href="/owner/forgot-password"
        class="text-sm font-medium transition-colors duration-200 [text-decoration:none] hover:text-[#004080]"
        style="color: #002B5C;"
      >
        Forgot your password?
      </Link>
    </div>
  </form>
</div>
</template>
