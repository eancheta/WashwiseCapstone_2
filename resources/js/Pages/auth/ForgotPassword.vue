<template>
  <div class="flex min-h-screen items-center justify-center bg-[#F8FAFC]">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold mb-4 text-center">
        {{ step === 1 ? 'Forgot Password' : 'Reset Password' }}
      </h2>

      <div v-if="$page.props.flash.status" class="bg-green-50 text-green-700 p-3 rounded mb-4">
        {{ $page.props.flash.status }}
      </div>
      <div v-if="$page.props.flash.error" class="bg-red-50 text-red-700 p-3 rounded mb-4">
        {{ $page.props.flash.error }}
      </div>

      <!-- Step 1: email -->
      <form v-if="step === 1" @submit.prevent="sendCode" class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input v-model="form.email" type="email" required class="w-full border p-2 rounded" />
        <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>

        <button :disabled="form.processing" class="w-full py-2 rounded text-white" style="background:#FF2D2D;">
          Send Verification Code
        </button>
        <p v-if="status" class="text-green-600 text-sm mt-2 text-center">{{ status }}</p>
      </form>

      <!-- Step 2: code + new password -->
      <form v-else @submit.prevent="resetPassword" class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Verification Code</label>
        <input v-model="form.code" type="text" required class="w-full border p-2 rounded" />
        <div v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</div>

        <label class="block text-sm font-medium text-gray-700">New Password</label>
        <input v-model="form.password" type="password" required class="w-full border p-2 rounded" />
        <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</div>

        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required class="w-full border p-2 rounded" />

        <button :disabled="form.processing" class="w-full py-2 rounded text-white" style="background:#002B5C;">
          Reset Password
        </button>

        <p v-if="status" class="text-green-600 text-sm mt-2 text-center">{{ status }}</p>
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
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const step = ref(1)
const status = ref('')
const type = ref('user') // default

onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  type.value = (params.get('type') || 'user')
})

// Inertia form — we won't include `type` inside initial object to avoid prop issues
const form = useForm({
  email: '',
  code: '',
  password: '',
  password_confirmation: '',
  type: '',
})

const sendCode = () => {
  form.type = type.value
  form.post(route('password.sendCode'), {
    onSuccess: () => {
      status.value = 'Verification code sent!'
      step.value = 2
    },
  })
}

const resetPassword = () => {
  form.type = type.value
  form.post(route('password.reset'), {
    onSuccess: () => {
      status.value = 'Password reset successful! Redirecting...'
      setTimeout(() => (window.location.href = route('login')), 1300)
    },
  })
}
</script>
