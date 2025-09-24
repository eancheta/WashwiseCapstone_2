<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// Sidebar state
const sidebarOpen = ref(false)
function toggleSidebar() { sidebarOpen.value = !sidebarOpen.value }

// Types
interface Shop {
  id: number
  name: string
  address: string
  district: string | number
  logo?: string | null
  qr_code?: string | null
}
interface AuthUser { id: number; name: string; email: string; email_verified_at?: string | null }
interface Props { shops: Shop[]; districts: (string|number)[]; auth: { user: AuthUser | null } }

const props = defineProps<Props>()
const selectedDistrict = ref<string | number>('all')

// Logout
const logout = () => {
  router.post('/logout', {}, {
    onSuccess: () => router.get('/login'),
    onError: (errors) => { console.error('Logout error:', errors); alert('Failed to log out.'); }
  })
}

// Booking
const goToBooking = (shopId: number) => {
  if (!props.auth.user) { router.get('/login'); return }
  router.get(`/customer/book/${shopId}`)
}

// Filter by district
const filteredShops = computed(() => {
  if (selectedDistrict.value === 'all') return props.shops
  return props.shops.filter((shop) => shop.district == selectedDistrict.value)
})

// ✅ FIX: Use the logo URL directly from the database, as it is already a full, secure URL from Cloudinary.
function getLogoSrc(shop: Shop) {
  if (shop?.logo) {
    return shop.logo.trim()
  }
  return '/images/default-carwash.png'
}

// ✅ FIX: Use the QR code URL directly.
function getQrCodeSrc(shop: Shop) {
  if (shop?.qr_code) {
    return shop.qr_code.trim()
  }
  return ''
}

// ✅ Handle broken logos
function handleImgError(e: Event) {
  const target = e.target as HTMLImageElement | null
  if (target) target.src = '/logos/default-carwash.png'
}
</script>

<template>
  <Head title="Customer Dashboard" />

  <!-- Top navigation -->
  <div class="w-full bg-white flex items-center justify-between px-6 py-3 border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="flex items-center gap-4">
      <button @click="toggleSidebar" class="flex flex-col justify-between w-6 h-5 focus:outline-none hover:opacity-80 transition">
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
      </button>
      <!-- ✅ App logo -->
      <img src="/logos/default-carwash.png" alt="App Logo" class="h-8 w-8 object-contain" />
    </div>

    <div class="text-center">
      <h2 class="text-lg font-semibold text-gray-800">
        {{ props.auth.user?.name || 'Guest' }}
      </h2>
    </div>
  </div>

  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside :class="['fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300', sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
      <div class="flex justify-between items-center p-4 border-b border-gray-700">
        <h2 class="text-lg font-bold">Menu</h2>
        <button @click="toggleSidebar" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
      </div>
      <nav class="space-y-3 p-4">
        <button @click.prevent="router.get('/settings/profile')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold">⚙️ Edit Profile</button>
        <button @click.prevent="router.get('/settings/password')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold">🔒 Password</button>
        <button @click.prevent="router.get('/settings/appearance')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition text-gray-900 font-bold">💳 Transaction History</button>
        <button @click="logout" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 transition font-bold">🚪 Log Out</button>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8 bg-gradient-to-br from-white via-blue-50 to-[#002B5C]">
      <h1 class="text-3xl font-extrabold text-gray-900 text-center">Available Car Wash Services</h1>

      <!-- District filter -->
      <div class="mt-6">
        <label for="district" class="block text-sm font-medium text-gray-900">Nearby</label>
        <select id="district" v-model="selectedDistrict" class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-900">
          <option value="all">All Districts</option>
          <option v-for="d in props.districts" :key="d" :value="d">District {{ d }}</option>
        </select>
      </div>

      <!-- Shops -->
      <div v-if="filteredShops.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-6">
        <div
          v-for="shop in filteredShops"
          :key="shop.id"
          class="bg-white shadow-md rounded-xl p-6 flex flex-col items-center text-center border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition"
        >
          <!-- ✅ Logo -->
          <img
            :src="getLogoSrc(shop)"
            alt="Car Wash Logo"
            class="h-20 w-20 object-contain mb-4"
            @error="handleImgError"
          />


          <h3 class="text-lg font-semibold text-gray-800">{{ shop.name }}</h3>
          <p class="text-sm text-gray-500 mb-5">{{ shop.address }}</p>

          <button @click.prevent="goToBooking(shop.id)" class="px-5 py-2 rounded-full bg-[#002B5C] text-white font-medium shadow hover:bg-[#FF2D2D] hover:scale-105 transition">
            Book Now
          </button>
          <a :href="`/customer/feedback/${shop.id}`" class="mt-2 px-5 py-2 rounded-full bg-[#FF2D2D] text-white font-medium shadow hover:bg-[#002B5C] hover:scale-105 transition">
            Feedback
          </a>

          <!-- ✅ QR Code -->
          <div v-if="shop.qr_code" class="mt-4">
            <img :src="getQrCodeSrc(shop)" alt="QR Code" class="w-24 h-24 object-contain" />
          </div>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center mt-10">No approved shops available.</p>
    </main>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap');
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out; }
</style>

