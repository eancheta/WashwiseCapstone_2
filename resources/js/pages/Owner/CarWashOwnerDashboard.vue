<!-- resources/js/Pages/OwnerDashboard.vue -->
<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col py-8 px-4">
      <div class="flex flex-col items-center mb-10">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mb-2" draggable="false" />

      </div>

      <!-- Menu -->
      <nav class="flex flex-col gap-2">
        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'dashboard' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'dashboard'"
        >
          📊 Dashboard
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'appointments' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'appointments'"
        >
          📅 Appointments
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'services' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'services'"
        >
          🛠 Service Listing
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'customers' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'customers'"
        >
          👥 Customers
        </button>
      </nav>

      <!-- Footer -->
      <form @submit.prevent="logout" class="mt-auto pt-10">
        <button class="w-full bg-[#FF2D2D] hover:bg-[#d72626] text-white px-4 py-2 rounded-lg font-semibold shadow transition">
          Logout
        </button>
      </form>
    </aside>

    <!-- Main content -->
    <main class="flex-1 bg-gray-50 p-6">
      <h1 class="text-2xl font-bold mb-1">
        {{
          activeTab === 'dashboard' ? 'Dashboard' :
          activeTab === 'appointments' ? 'Appointments' :
          activeTab === 'services' ? 'Services' :
          'Customers'
        }}
      </h1>

      <p class="text-gray-500 mb-6">Welcome back!</p>

      <div v-if="activeTab === 'dashboard'">
        <!-- Dashboard Content -->
        <p>This is the dashboard overview.</p>
      </div>

      <div v-if="activeTab === 'appointments'">
        <!-- Appointments Content -->
        <p>List of appointments here.</p>
      </div>

      <div v-if="activeTab === 'services'">
        <!-- Services Content -->
        <p>Manage services here.</p>
      </div>

      <div v-if="activeTab === 'customers'">
        <!-- Customers Content -->
        <p>Customer list here.</p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const activeTab = ref('dashboard')

const logout = () => {
  router.post('/logout')
}
</script>
