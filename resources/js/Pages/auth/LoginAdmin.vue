<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  username: '',
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
        <!-- Username -->
        <div>
          <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            autofocus
            placeholder="Admin username"
            class="w-full border border-gray-300 px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-[#FF2D2D] transition"
          />
          <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
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
</template>
