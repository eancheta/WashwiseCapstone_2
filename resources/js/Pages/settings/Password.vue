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

const submit = () => {
  form.put('/settings/password')
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
</template>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
</style>
