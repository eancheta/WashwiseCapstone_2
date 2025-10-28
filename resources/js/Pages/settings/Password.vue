<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const showCurrent = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)

function goBack() {
  router.visit('/dashboard')
}

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

// alert messages
const successMessage = ref('')
const errorMessage = ref('')

const clearAlerts = (delay = 3000) => {
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, delay)
}

const submit = () => {
  form.put('/settings/password', {
    onSuccess: () => {
      successMessage.value = 'Password changed successfully! You can now log in.'
      form.reset('current_password', 'password', 'password_confirmation')
      clearAlerts(3500)
    },
    onError: (err: any) => {
      // normalize where the server may put validation messages
      const errorData = (err as any)?.response?.data ?? (err as any)
      const firstErr =
        errorData?.errors?.current_password?.[0] ||
        errorData?.errors?.password?.[0] ||
        errorData?.message ||
        'Failed to change password. Please check your inputs.'
      errorMessage.value = firstErr
      clearAlerts(5000)
      console.error('Change password error:', err)
    },
  })
}

function goTo(path: string) {
  router.visit(path)
}
</script>

<template>
  <Head title="Change Password" />

  <div class="min-h-screen flex flex-col justify-between bg-[#F8FAFC] relative pb-16 sm:pb-0">
    <!-- Return Button -->
    <div class="absolute top-4 left-4 z-20">
      <button
        @click="goBack"
        type="button"
        class="flex items-center gap-2 px-3 py-1.5 bg-gray-200 text-black rounded-lg text-sm font-medium shadow hover:bg-[#FF2D2D] hover:text-white transition"
      >
        ⬅ Return
      </button>
    </div>

    <!-- Main Form -->
    <div class="flex flex-1 items-center justify-center px-4">
      <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-8 z-10">
        <div class="absolute -top-10 -left-10 w-24 h-24 bg-[#FF2D2D] opacity-10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-[#002B5C] opacity-10 rounded-full blur-2xl"></div>

        <div class="relative z-10">
          <h1 class="text-2xl font-extrabold text-center text-[#002B5C] mb-6 tracking-tight">
            Change Password
          </h1>

          <!-- Success / Error Alerts -->
          <div v-if="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center font-semibold">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-center font-semibold">
            {{ errorMessage }}
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <!-- Current Password -->
            <div class="relative">
              <label class="block text-sm font-bold text-[#182235] mb-1">Current Password</label>
              <input
                v-model="form.current_password"
                :type="showCurrent ? 'text' : 'password'"
                class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2.5 pr-10 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
                autocomplete="current-password"
                required
              />
              <button
                type="button"
                class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]"
                @click="showCurrent = !showCurrent"
              >
                <i :class="showCurrent ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
              <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
                {{ form.errors.current_password }}
              </div>
            </div>

            <!-- New Password -->
            <div class="relative">
              <label class="block text-sm font-bold text-[#182235] mb-1">New Password</label>
              <input
                v-model="form.password"
                :type="showNew ? 'text' : 'password'"
                class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2.5 pr-10 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
                autocomplete="new-password"
                required
              />
              <button
                type="button"
                class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]"
                @click="showNew = !showNew"
              >
                <i :class="showNew ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
              <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="relative">
              <label class="block text-sm font-bold text-[#182235] mb-1">Confirm New Password</label>
              <input
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2.5 pr-10 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
                autocomplete="new-password"
                required
              />
              <button
                type="button"
                class="absolute right-3 top-9 text-gray-500 hover:text-[#FF2D2D]"
                @click="showConfirm = !showConfirm"
              >
                <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="w-full bg-[#FF2D2D] text-white py-3 rounded-xl font-semibold text-base hover:bg-[#002B5C] active:scale-95 transition"
              :disabled="form.processing"
            >
              Save Changes
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around py-2 shadow-lg sm:hidden">
      <button @click="goTo('/dashboard')" class="flex flex-col items-center text-[#002B5C]">
        <i class="fa-solid fa-house text-lg"></i>
        <span class="text-xs mt-0.5">Home</span>
      </button>
      <button @click="goTo('/bookings')" class="flex flex-col items-center text-[#002B5C]">
        <i class="fa-solid fa-calendar text-lg"></i>
        <span class="text-xs mt-0.5">Bookings</span>
      </button>
      <button @click="goTo('/profile')" class="flex flex-col items-center text-[#002B5C]">
        <i class="fa-solid fa-user text-lg"></i>
        <span class="text-xs mt-0.5">Profile</span>
      </button>
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

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
</style>
