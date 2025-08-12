<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col py-8 px-4">
      <div class="flex flex-col items-center mb-10">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mb-2" draggable="false" />
        <span class="text-xl font-bold text-[#182235]">Admin</span>
      </div>
      <nav class="flex flex-col gap-2">
        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'users' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'users'"
        >
          <span>👤</span> Users
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'owners' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'owners'"
        >
          <span>🏢</span> Owners    
        </button>
      </nav>
      <form @submit.prevent="logout" class="mt-auto pt-10">
        <button class="w-full bg-[#FF2D2D] hover:bg-[#d72626] text-white px-4 py-2 rounded-lg font-semibold shadow transition">
          Logout
        </button>
      </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <h1 class="text-2xl font-bold mb-6 text-[#182235]">
        {{
          activeTab === 'users'
            ? 'Users'
            : activeTab === 'bookings'
            ? 'Bookings'
            : 'Owners'
        }}
      </h1>

      <!-- Users Table -->
      <div v-if="activeTab === 'users'" class="overflow-x-auto">
        <table class="min-w-full bg-white border border-[#182235] shadow-sm rounded">
          <thead>
            <tr class="bg-[#f8fafc] text-left">
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">ID</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Name</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Email</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Verified At</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Verification Code</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Created</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Updated</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="hover:bg-[#e0e7ff] transition">
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.id }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.name }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.email }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.email_verified_at || 'Not verified' }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.verification_code || 'N/A' }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.created_at }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.updated_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
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

const activeTab = ref('users')

const logout = () => {
  router.post('/logout')
}
</script>
