<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'

function goBack() {
  router.visit('/dashboard') // for customer dashboard
}

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put('/settings/password')
}
</script>

<template>
  <Head title="Change Password" />

  <div class="min-h-screen flex items-center justify-center bg-[#F8FAFC] py-8 px-2 relative">
    <!-- Return Button (outside the box, top-left) -->
    <div class="absolute top-4 left-4">
      <button
        @click="goBack"
        type="button"
        class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-black rounded-lg text-sm font-medium shadow hover:bg-[#FF2D2D] hover:text-white transition"
      >
        ⬅ Return
      </button>
    </div>

    <!-- Main Form Box -->
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-8 z-10">
      <!-- Decorative background shapes -->
      <div class="absolute -top-10 -left-10 w-24 h-24 bg-[#FF2D2D] opacity-10 rounded-full blur-2xl z-0"></div>
      <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-[#002B5C] opacity-10 rounded-full blur-2xl z-0"></div>

      <div class="relative z-10">
        <h1 class="text-2xl font-extrabold text-center text-[#002B5C] mb-6 tracking-tight">
          Change Password
        </h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-base font-bold text-[#182235] mb-1">Current Password</label>
            <input
              v-model="form.current_password"
              type="password"
              class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
              autocomplete="current-password"
              required
            />
            <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
              {{ form.errors.current_password }}
            </div>
          </div>

          <div>
            <label class="block text-base font-bold text-[#182235] mb-1">New Password</label>
            <input
              v-model="form.password"
              type="password"
              class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
              autocomplete="new-password"
              required
            />
            <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
              {{ form.errors.password }}
            </div>
          </div>

          <div>
            <label class="block text-base font-bold text-[#182235] mb-1">Confirm New Password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="mt-1 block w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
              autocomplete="new-password"
              required
            />
          </div>

          <button
            type="submit"
            class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-bold text-lg hover:bg-[#002B5C] transition"
            :disabled="form.processing"
          >
            Save Changes
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
