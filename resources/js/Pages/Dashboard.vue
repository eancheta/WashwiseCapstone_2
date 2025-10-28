<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
const selectedStatus = ref<'all' | 'open' | 'closed'>('all')


const sidebarOpen = ref(false)
function toggleSidebar() { sidebarOpen.value = !sidebarOpen.value }

interface Shop {
  id: number
  name: string
  address: string
  district: string | number
  logo?: string | null
  qr_code?: string | null
  status: string
}
interface AuthUser { id: number; name: string; email: string; email_verified_at?: string | null }
interface Props { shops: Shop[]; districts: (string|number)[]; auth: { user: AuthUser | null } }

const props = defineProps<Props>()
const selectedDistrict = ref<string | number>('all')

const logout = () => {
  Inertia.post('/logout', {}, {
    onSuccess: () => Inertia.get('/login'),
    onError: (errors) => { console.error('Logout error:', errors); alert('Failed to log out.'); }
  })
}

// Modal state
const reminderOpen = ref(false)
const selectedShop = ref<Shop | null>(null)
const agreeChecked = ref(false) // ✅ Checkbox state

const openReminder = (shop: Shop) => {
  if (shop.status === 'closed') {
    alert('🚫 Sorry, this shop is currently closed.')
    return
  }
  if (!props.auth.user) { Inertia.get('/login'); return }
  selectedShop.value = shop
  reminderOpen.value = true
  agreeChecked.value = false // reset checkbox each time modal opens
}

const confirmBooking = () => {
  if (!agreeChecked.value) {
    alert('⚠️ Please check the agreement box before confirming your booking.')
    return
  }
  if (selectedShop.value) {
    Inertia.get(`/customer/book/${selectedShop.value.id}`)
    reminderOpen.value = false
  }
}

const cancelReminder = () => {
  reminderOpen.value = false
  selectedShop.value = null
  agreeChecked.value = false
}

const filteredShops = computed(() => {
  let shops = props.shops

  // Filter by district
  if (selectedDistrict.value !== 'all') {
    shops = shops.filter((shop) => shop.district == selectedDistrict.value)
  }

  // Filter by status (open/closed)
  if (selectedStatus.value !== 'all') {
    shops = shops.filter((shop) => shop.status === selectedStatus.value)
  }

  return shops
})


function getLogoSrc(shop: Shop) {
  if (!shop.logo) return '/images/default-carwash.png'
  if (shop.logo.startsWith('http')) return shop.logo
  return `/storage/${shop.logo}`
}

function handleImgError(e: Event) {
  const target = e.target as HTMLImageElement | null
  if (target) target.src = '/logos/default-carwash.png'
}
</script>

<template>
  <Head title="Customer Dashboard" />

  <!-- 🌟 Top bar -->
  <header class="w-full bg-white flex items-center justify-between px-4 py-3 shadow-md sticky top-0 z-40">
    <button @click="toggleSidebar" class="flex flex-col justify-between w-6 h-5 focus:outline-none hover:opacity-80 transition">
      <span class="block h-0.5 bg-gray-800 rounded"></span>
      <span class="block h-0.5 bg-gray-800 rounded"></span>
      <span class="block h-0.5 bg-gray-800 rounded"></span>
    </button>

    <div class="flex items-center gap-2">
      <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-10 w-auto object-contain" />
    </div>

    <span class="hidden sm:block text-sm font-semibold text-gray-800">{{ props.auth.user?.name || 'Guest' }}</span>
  </header>

  <div class="flex min-h-screen bg-gradient-to-br from-white via-blue-50 to-[#002B5C]">

    <!-- Overlay for mobile sidebar -->
    <div v-if="sidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black bg-opacity-40 z-40 sm:hidden"></div>

    <!-- 🧭 Sidebar -->
    <aside :class="['fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300', sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
      <div class="flex justify-between items-center p-4 border-b border-gray-700">
        <h2 class="text-lg font-bold">Menu</h2>
        <button @click="toggleSidebar" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
      </div>
      <nav class="space-y-3 p-4">
        <button @click.prevent="Inertia.get('/settings/profile')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/10 transition font-bold">⚙️ Edit Profile</button>
        <button @click.prevent="Inertia.get('/settings/password')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/10 transition font-bold">🔒 Password</button>
        <button @click.prevent="Inertia.get('/settings/appearance')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/10 transition font-bold">💳 Transaction History</button>
        <button @click="logout" class="w-full text-left px-3 py-2 rounded-lg text-red-500 hover:bg-red-500/10 transition font-bold">🚪 Log Out</button>
      </nav>
    </aside>

    <!-- 🌊 Main content -->
    <main class="flex-1 p-4 sm:p-8">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 text-center">Available Car Wash Services</h1>

<div class="mt-6 bg-white/80 backdrop-blur-lg rounded-xl shadow-md p-4 border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
  <!-- District Filter -->
  <div>
    <label for="district" class="block text-sm font-medium text-gray-900 mb-1">Nearby</label>
    <select id="district" v-model="selectedDistrict" class="block w-full appearance-none rounded-xl border border-gray-300 px-4 py-2 pr-10 text-gray-900 shadow focus:border-[#FF2D2D] focus:ring-2 focus:ring-[#FF2D2D] text-sm transition">
      <option value="all">All Districts</option>
      <option v-for="d in props.districts" :key="d" :value="d">District {{ d }}</option>
    </select>
  </div>

  <!-- Status Filter -->
  <div>
    <label for="status" class="block text-sm font-medium text-gray-900 mb-1">Shop Status</label>
    <select id="status" v-model="selectedStatus" class="block w-full appearance-none rounded-xl border border-gray-300 px-4 py-2 pr-10 text-gray-900 shadow focus:border-[#FF2D2D] focus:ring-2 focus:ring-[#FF2D2D] text-sm transition">
      <option value="all">All Shops</option>
      <option value="open">🟢 Open Shops</option>
      <option value="closed">🔴 Closed Shops</option>
    </select>
  </div>
</div>

      <!-- 🧽 Shops List -->
      <div v-if="filteredShops.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <div v-for="shop in filteredShops" :key="shop.id" class="bg-white shadow-md rounded-xl p-4 flex flex-col items-center text-center border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition">
          <img :src="getLogoSrc(shop)" alt="Car Wash Logo" class="h-20 w-20 object-contain mb-4" @error="handleImgError" />
          <h3 class="text-lg font-semibold text-gray-800">{{ shop.name }}</h3>
          <p class="text-sm text-gray-500 mb-2">{{ shop.address }}</p>
          <p class="text-xs font-semibold mb-4" :class="shop.status === 'open' ? 'text-green-600' : 'text-red-600'">
            {{ shop.status === 'open' ? '🟢 Open' : '🔴 Closed' }}
          </p>

          <button v-if="shop.status === 'open'" @click.prevent="openReminder(shop)" class="w-full sm:w-auto px-5 py-2 rounded-full bg-[#002B5C] text-white font-medium shadow hover:bg-[#FF2D2D] hover:scale-105 transition">
            Book Now
          </button>
          <button v-else disabled class="w-full sm:w-auto px-5 py-2 rounded-full bg-gray-400 text-white font-medium shadow cursor-not-allowed">
            🚫 Closed
          </button>

          <a :href="`/customer/feedback/${shop.id}`" class="mt-2 w-full sm:w-auto px-5 py-2 rounded-full bg-[#FF2D2D] text-white font-medium shadow hover:bg-[#002B5C] hover:scale-105 transition">
            Feedback
          </a>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center mt-8">No approved shops available.</p>
    </main>
  </div>

  <!-- 💬 Booking Reminder Modal -->
  <div v-if="reminderOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
      <h2 class="text-lg font-bold text-gray-800 mb-4">Booking Reminder Notice</h2>
      <ul class="list-disc list-inside text-sm text-gray-700 space-y-2 mb-4">
        <li>Please make sure to arrive on time for your selected schedule to secure your slot.</li>
        <li>If you are unable to come, kindly note that the reservation/down payment is non-refundable.</li>
        <li>If you arrive late, accommodation will depend on staff availability.</li>
        <li>In case of unforeseen events (e.g., heavy rain, technical issues, or emergencies), your appointment may be rescheduled to the next available slot. You may also rebook your preferred schedule once the system reopens for booking.</li>
      </ul>

      <!-- ✅ Checkbox Agreement -->
      <div class="flex items-start mb-4">
        <input
          id="agree"
          type="checkbox"
          v-model="agreeChecked"
          class="mt-1 w-4 h-4 text-[#002B5C] border-gray-300 rounded focus:ring-[#FF2D2D]"
        />
        <label for="agree" class="ml-2 text-sm text-gray-700 font-semibold">
          By clicking “Confirm Booking”, you agree to these conditions.
        </label>
      </div>

      <div class="flex flex-col sm:flex-row justify-end gap-3">
        <button @click="cancelReminder" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 w-full sm:w-auto">Cancel</button>
        <button @click="confirmBooking" class="px-4 py-2 bg-[#002B5C] text-white rounded-lg hover:bg-[#FF2D2D] w-full sm:w-auto">Confirm Booking</button>
      </div>
    </div>
  </div>
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-6">

    <!-- About -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">About WashWise</h2>
      <p class="text-sm leading-relaxed">
        WashWise is your trusted platform for booking car wash services — connecting customers and business owners for a smoother, cleaner experience every day.
      </p>
    </div>
    <!-- Contact Info -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">Contact Us</h2>
      <ul class="text-sm space-y-1">
        <li>📞 +63 992 759 4673</li>
        <li>✉️ washwise00@gmail.com</li>
      </ul>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="border-t border-gray-700 mt-8 pt-4 text-center text-xs text-gray-400">
    © {{ new Date().getFullYear() }} WashWise. All rights reserved.
    <br class="sm:hidden" /> Developed by <span class="text-[#FF2D2D] font-semibold">Washwise Team.</span>
  </div>
</footer>
</template>
