<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const sidebarOpen = ref(false)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

// Interfaces
interface Shop {
  id: number
  name: string
  address: string
  district: string | number
  logo?: string // This will now hold Cloudinary URL
}

interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at?: string | null
}

interface Props {
  shops: Shop[]
  districts: (string | number)[]
  auth: {
    user: AuthUser | null
  }
}

const props = defineProps<Props>()

// State
const selectedDistrict = ref<string | number>('all')

// Backend base URL (still used for fallback default image)
const backendBaseUrl = import.meta.env.VITE_BACKEND_URL

// Logout
const logout = () => {
  Inertia.post('/logout', {}, {
    onSuccess: () => Inertia.get('/login'),
    onError: (errors) => {
      console.error('Logout error:', errors)
      alert('Failed to log out. Please try again.')
    }
  })
}

// Booking navigation
const goToBooking = (shopId: number) => {
  if (!props.auth.user) {
    Inertia.get('/login')
    return
  }
  Inertia.get(`/customer/book/${shopId}`)
}

// Filtered shops
const filteredShops = computed(() => {
  if (selectedDistrict.value === 'all') return props.shops
  return props.shops.filter((shop) => shop.district == selectedDistrict.value)
})
</script>

<template>
  <Head title="Customer Dashboard" />
  <!-- Top Navigation -->
  <div class="w-full bg-white flex items-center justify-between px-6 py-3 border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <!-- Left: Hamburger + Logo -->
    <div class="flex items-center gap-4">
      <button
        @click="toggleSidebar"
        class="flex flex-col justify-between w-6 h-5 focus:outline-none hover:opacity-80 transition"
      >
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
      </button>

      <!-- Logo -->
      <img
        src="/images/washwiselogo2.png"
        alt="WashWise Logo"
        class="h-10 w-auto select-none"
        draggable="false"
      />
    </div>

    <!-- Right: Owner Name -->
    <div class="text-center">
      <h2 class="text-lg font-semibold text-gray-800">
        {{ props.auth.user?.name || 'Guest' }}
      </h2>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="flex min-h-screen">
    <aside
      :class="['fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300', sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
    >
      <div class="flex justify-between items-center p-4 border-b border-gray-700">
        <h2 class="text-lg font-bold">Menu</h2>
        <button @click="toggleSidebar" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
      </div>

      <!-- Sidebar Links -->
      <nav class="space-y-3 p-4">
        <button
          @click.prevent="Inertia.get('/settings/profile')"
          class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold"
        >
          ⚙️ Edit Profile
        </button>
        <button
          @click.prevent="Inertia.get('/settings/password')"
          class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold"
        >
          🔒 Password
        </button>
        <button
          @click.prevent="Inertia.get('/settings/appearance')"
          class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold"
        >
          💳 Transaction History
        </button>
        <button
          @click="logout"
          class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 transition font-bold"
        >
          🚪 Log Out
        </button>
      </nav>
    </aside>

    <!-- Hero Section -->
    <main class="flex-1 p-8 bg-gradient-to-br from-white via-blue-50 to-[#002B5C]">
      <h1 class="text-3xl font-extrabold text-gray-900 text-center">Available Car Wash Services</h1>

      <div class="mt-6">
        <label for="district" class="block text-sm font-medium text-gray-900">Nearby</label>
        <select
          id="district"
          v-model="selectedDistrict"
          class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-900"
        >
          <option value="all">All Districts</option>
          <option
            v-for="district in props.districts"
            :key="district"
            :value="district"
          >
            District {{ district }}
          </option>
        </select>
      </div>

      <div
        v-if="filteredShops.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-6"
      >
        <div
          v-for="shop in filteredShops"
          :key="shop.id"
          class="bg-white shadow-md rounded-xl p-6 flex flex-col items-center text-center border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition"
        >
<img
  :src="(
    shop.logo
      ? (shop.logo.startsWith('http') ? shop.logo : `${backendBaseUrl}/storage/${shop.logo}`)
      : `${backendBaseUrl}/logos/default-carwash.png`)
  "
  alt="Car Wash Logo"
  class="h-20 w-20 object-contain mb-4"
  @error="(e) => { (e.target as HTMLImageElement).src = `${backendBaseUrl}/logos/default-carwash.png` }"
/>
          <h3 class="text-lg font-semibold text-gray-800">{{ shop.name }}</h3>
          <p class="text-sm text-gray-500 mb-5">{{ shop.address }}</p>
          <button
            @click.prevent="goToBooking(shop.id)"
            class="px-5 py-2 rounded-full bg-[#002B5C] text-white font-medium shadow hover:bg-[#FF2D2D] hover:scale-105 transition"
          >
            Book Now
          </button>
          <a
            :href="`/customer/feedback/${shop.id}`"
            class="mt-2 px-5 py-2 rounded-full bg-[#FF2D2D] text-white font-medium shadow hover:bg-[#002B5C] hover:scale-105 transition"
          >
            Feedback
          </a>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center mt-10">No approved shops available.</p>
    </main>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap');

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>
