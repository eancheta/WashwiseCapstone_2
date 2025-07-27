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
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form @submit.prevent="submit" class="bg-white p-6 rounded shadow-md w-full max-w-sm">
      <h2 class="text-xl font-bold mb-4">Admin Login</h2>

      <div class="mb-4">
        <label class="block mb-1">Username</label>
        <input v-model="form.username" type="text" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.username" class="text-red-500 text-sm">{{ form.errors.username }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Password</label>
        <input v-model="form.password" type="password" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</div>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Login
      </button>
    </form>
  </div>
</template>


