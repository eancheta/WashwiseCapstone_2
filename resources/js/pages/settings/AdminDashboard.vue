<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <h1 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h1>

    <!-- Optional test -->
    <pre class="mb-4">{{ users }}</pre>

    <form @submit.prevent="logout">
      <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mb-6">
        Logout
      </button>
    </form>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="py-2 px-4 border-b">ID</th>
            <th class="py-2 px-4 border-b">Name</th>
            <th class="py-2 px-4 border-b">Email</th>
            <th class="py-2 px-4 border-b">Verified At</th>
            <th class="py-2 px-4 border-b">Verification Code</th>
            <th class="py-2 px-4 border-b">Created</th>
            <th class="py-2 px-4 border-b">Updated</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="py-2 px-4 border-b">{{ user.id }}</td>
            <td class="py-2 px-4 border-b">{{ user.name }}</td>
            <td class="py-2 px-4 border-b">{{ user.email }}</td>
            <td class="py-2 px-4 border-b">{{ user.email_verified_at || 'Not verified' }}</td>
            <td class="py-2 px-4 border-b">{{ user.verification_code || 'N/A' }}</td>
            <td class="py-2 px-4 border-b">{{ user.created_at }}</td>
            <td class="py-2 px-4 border-b">{{ user.updated_at }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const { users } = defineProps<{
  users: Array<{
    id: number
    name: string
    email: string
    email_verified_at: string | null
    verification_code: string | null
    created_at: string
    updated_at: string
  }>
}>()

const logout = () => {
  router.post('/logout')
}
</script>
