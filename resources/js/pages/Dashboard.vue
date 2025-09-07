```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

// Interfaces
interface Shop {
  id: number
  name: string
  address: string
  logo?: string
}

interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at?: string | null
}

interface Props {
  shops: Shop[]
  auth: {
    user: AuthUser | null
  }
}

const props = defineProps<Props>()

// Define backend base URL from env or fallback
const backendBaseUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost/Washwise'

// Profile menu toggle
const profileMenuOpen = ref(false)
function toggleProfileMenu() {
  profileMenuOpen.value = !profileMenuOpen.value
}

// Function to handle image error and set fallback
const handleImageError = (event: Event, fallbackSrc: string) => {
  const target = event.target as HTMLImageElement | null
  if (target) {
    target.src = fallbackSrc
  }
}

// Navigate to Booking
const goToBooking = (shopId: number) => {
  if (!props.auth.user) {
    Inertia.get('/login')
    return
  }
  if (!props.shops.find((shop) => shop.id === shopId)) {
    alert('Invalid shop ID. Please select a valid shop.')
    return
  }
  Inertia.get(`/customer/book/${shopId}`)
}

// Logout function
const logout = () => {
  Inertia.post('/logout', {}, {
    onSuccess: () => {
      Inertia.get('/login')
    },
    onError: (errors) => {
      console.error('Logout error:', errors)
      alert('Failed to log out. Please try again.')
    }
  })
}

// Debug shop logos
props.shops.forEach((shop) => {
  console.log(`Shop ${shop.id} Logo:`, shop.logo)
})
</script>

<template>
  <Head title="Customer Dashboard" />

  <!-- Top Bar -->
  <div
    class="w-full bg-white flex items-center justify-between px-4 py-2 border-b border-gray-200 text-sm font-semibold relative"
  >
    <!-- Left: Logo -->
    <div class="flex items-center">
      <img
        :src="`${backendBaseUrl}/images/washwiselogo2.png`"
        alt="WashWise Logo"
        class="h-10 w-auto"
        @error="handleImageError($event, `${backendBaseUrl}/images/washwiselogo2.png`)"
        draggable="false"
      />
    </div>

    <!-- Right: Profile Dropdown -->
    <div class="relative">
      <button
        @click="toggleProfileMenu"
        class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition"
      >
        <img
          :src="`${backendBaseUrl}/images/default-avatar.png`"
          alt="Profile"
          class="w-8 h-8 rounded-full border border-gray-300"
          @error="handleImageError($event, `${backendBaseUrl}/images/default-avatar.png`)"
        />
        <span class="text-gray-700 font-medium">
          {{ props.auth.user?.email || 'Guest' }}
        </span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 text-gray-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 9l-7 7-7-7"
          />
        </svg>
      </button>

      <!-- Dropdown -->
      <div
        v-if="profileMenuOpen"
        class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 animate-fadeIn"
      >
        <div class="py-2 space-y-1">
          <button
            @click.prevent="Inertia.get('/settings/profile')"
            class="flex items-center gap-2 px-4 py-2 w-full text-left text-gray-700 hover:bg-gray-100 hover:text-black transition rounded-lg"
          >
            <span>⚙️</span>
            Edit Profile
          </button>
          <button
            @click.prevent="Inertia.get('/settings/password')"
            class="flex items-center gap-2 px-4 py-2 w-full text-left text-gray-700 hover:bg-gray-100 hover:text-black transition rounded-lg"
          >
            <span>⚙️</span>
            Password
          </button>
          <button
            @click.prevent="Inertia.get('/settings/appearance')"
            class="flex items-center gap-2 px-4 py-2 w-full text-left text-gray-700 hover:bg-gray-100 hover:text-black transition rounded-lg"
          >
            <span>💳</span>
            Transaction History
          </button>
          <button
            @click="logout"
            class="flex items-center gap-2 px-4 py-2 w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-lg"
          >
            <span>🚪</span>
            Log Out
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Flash Messages (Backend-driven) -->
  <transition name="fade">
    <div
      v-if="$page.props.flash.success"
      class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium z-50"
    >
      ✅ {{ $page.props.flash.success }}
    </div>
  </transition>

  <transition name="fade">
    <div
      v-if="$page.props.flash.error"
      class="fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium z-50"
    >
      ❌ {{ $page.props.flash.error }}
    </div>
  </transition>

  <!-- Car Wash Shops Section -->
  <section class="min-h-screen p-6 bg-gradient-to-br from-white via-blue-50 to-[#002B5C]">
    <h1
      class="text-3xl font-extrabold text-gray-900 tracking-wide drop-shadow-sm font-poppins text-center"
    >
      Available Car Wash Shops
    </h1>

    <div
      v-if="props.shops.length > 0"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-6"
    >
      <div
        v-for="shop in props.shops"
        :key="shop.id"
        class="bg-white shadow-md rounded-xl p-6 flex flex-col items-center text-center border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition"
      >
        <img
          :src="shop.logo ? `${backendBaseUrl}/storage/${shop.logo}` : `${backendBaseUrl}/images/default-carwash.png`"
          alt="Car Wash Logo"
          class="h-20 w-20 object-contain mb-4"
          @error="handleImageError($event, `${backendBaseUrl}/images/default-carwash.png`)"
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
  class="px-5 py-2 rounded-full bg-green-600 text-white font-medium shadow hover:bg-green-700 hover:scale-105 transition"
>
  Feedback
</a>
      </div>
    </div>

    <p v-else class="text-gray-500 text-center mt-10">
      No approved shops available at the moment.
    </p>
  </section>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap');

.custom-heading {
  font-family: 'Montserrat', sans-serif;
  letter-spacing: 0.5px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

/* Smooth fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 1.0s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
```
