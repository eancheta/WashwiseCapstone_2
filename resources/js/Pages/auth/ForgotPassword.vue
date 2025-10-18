<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-sm">
      <h2 class="text-xl font-bold mb-4 text-center">
        {{ step === 1 ? 'Forgot Password' : 'Reset Password' }}
      </h2>

      <!-- Step 1: Email -->
      <form v-if="step === 1" @submit.prevent="sendCode">
        <label>Email</label>
        <input
          v-model="form.email"
          type="email"
          class="border p-2 w-full rounded mb-3"
          required
        />
        <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700">
          Send Verification Code
        </button>
        <p v-if="status" class="text-green-600 text-sm mt-2 text-center">{{ status }}</p>
      </form>

      <!-- Step 2: Code + New Password -->
      <form v-else @submit.prevent="resetPassword">
        <label>Verification Code</label>
        <input v-model="form.code" type="text" class="border p-2 w-full rounded mb-3" required />

        <label>New Password</label>
        <input v-model="form.password" type="password" class="border p-2 w-full rounded mb-3" required />

        <label>Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" class="border p-2 w-full rounded mb-3" required />

        <button type="submit" class="bg-green-600 text-white w-full py-2 rounded hover:bg-green-700">
          Reset Password
        </button>
        <p v-if="status" class="text-green-600 text-sm mt-2 text-center">{{ status }}</p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">

import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ type: String })

const step = ref(1)
const status = ref('')

const form = useForm({
  email: '',
  code: '',
  password: '',
  password_confirmation: '',
  type: props.type || 'user',
})

const sendCode = () => {
  form.post(route('password.sendCode'), {
    onSuccess: () => {
      status.value = 'Verification code sent!'
      step.value = 2
    },
  })
}

const resetPassword = () => {
  form.post(route('password.reset'), {
    onSuccess: () => {
      status.value = 'Password reset successful! Redirecting...'
      setTimeout(() => window.location.href = route('login'), 2000)
    },
  })
}
</script>
