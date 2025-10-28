<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/loginAdmin')
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#e0e7ff] to-[#f8fafc]">
    <form
      @submit.prevent="submit"
      class="flex flex-col gap-6 w-full max-w-md mx-auto bg-white p-10 rounded-2xl shadow-xl border border-gray-100 relative"
    >
      <div class="flex flex-col items-center mb-2">
        <img
          src="/images/washwiselogo2.png"
          alt="WashWise Logo"
          class="h-16 w-auto mb-2 drop-shadow"
          draggable="false"
        />


        <h2 class="text-2xl font-extrabold text-[#182235] mb-1">Admin Login</h2>
        <p class="text-gray-500 text-sm"></p>
      </div>

      <div class="flex flex-col gap-4">
<!-- Email -->
<div>
  <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
  <input
    id="email"
    v-model="form.email"
    type="email"
    required
    autofocus
    placeholder="Admin email"
    class="w-full border border-gray-300 px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-[#FF2D2D] transition"
  />
  <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
</div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            placeholder="Password"
            class="w-full border border-gray-300 px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-[#FF2D2D] transition"
          />
          <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
        </div>
      </div>

      <button
        type="submit"
        class="w-full py-2 rounded-lg bg-[#FF2D2D] hover:bg-[#d72626] text-white font-bold text-lg shadow transition disabled:opacity-60"
        :disabled="form.processing"
      >
        Login
      </button>
    </form>
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
