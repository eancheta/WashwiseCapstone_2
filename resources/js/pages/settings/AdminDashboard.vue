<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <h1 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h1>

    <form @submit.prevent="logout">
      <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Logout
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const logout = () => {
  router.post('/logout')
}
</script>
