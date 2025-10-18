<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route } from 'ziggy-js'
import Swal from 'sweetalert2'
import { onMounted } from 'vue'

onMounted(() => {
  if (page.props.flash?.success) {
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: page.props.flash.success,
      showConfirmButton: false,
      timer: 2000,
    })
  }

  if (page.props.flash?.error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: page.props.flash.error,
    })
  }
})

// Use any to avoid strict PageProps mismatch errors in TS dev environment
const page = usePage<any>()

// Sidebar toggle
const sidebarOpen = ref(false)
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

// Read user and shop from Inertia props (shop may be top-level or nested under auth.user)
const user = computed(() => page.props?.auth?.user ?? null)
const shop = computed(() => page.props?.shop ?? page.props?.auth?.user?.shop ?? null)

// Loading states for buttons
const closing = ref(false)
const opening = ref(false)

// Debug: prints page.props to browser console so you can confirm shape

// Shop actions with loading and basic error handling
function closeShop(id?: number | null) {
  if (!id) {
    console.warn('closeShop called without id')
    return
  }
  if (closing.value) return
  closing.value = true

  router.post(route('owner.shop.close', { id }), {}, {
    preserveScroll: true,
    onFinish: () => {
      closing.value = false
    },
    onError: (errors) => {
      console.error('Close shop error', errors)
      closing.value = false
    },
  })
}

function openShop(id?: number | null) {
  if (!id) {
    console.warn('openShop called without id')
    return
  }
  if (opening.value) return
  opening.value = true

  router.post(route('owner.shop.open', { id }), {}, {
    preserveScroll: true,
    onFinish: () => {
      opening.value = false
    },
    onError: (errors) => {
      console.error('Open shop error', errors)
      opening.value = false
    },
  })
}
</script>

<template>
  <Head title="Owner Dashboard" />

  <!-- Top Navigation -->
  <div
    class="w-full bg-white flex items-center justify-between px-6 py-3 border-b border-gray-200 shadow-sm sticky top-0 z-40"
  >
    <div class="flex items-center gap-4">
      <button
        @click="toggleSidebar"
        class="flex flex-col justify-between w-6 h-5 focus:outline-none hover:opacity-80 transition"
        aria-label="Toggle sidebar"
      >
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
      </button>

      <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-10 w-auto select-none" draggable="false" />
    </div>

    <div class="flex items-center space-x-2 px-3 py-2 rounded-lg">
      <span class="text-gray-700 font-medium">{{ user ? user.name : 'Owner' }}</span>
    </div>
  </div>

  <!-- Sidebar -->
  <aside
    :class="[
      'fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full',
    ]"
  >
    <div class="flex justify-between items-center p-4 border-b border-gray-700">
      <h2 class="text-lg font-bold">Menu</h2>
      <button @click="toggleSidebar" class="text-gray-400 hover:text-red-500 text-2xl" aria-label="Close sidebar">&times;</button>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-2">
      <Link
        :href="route('owner.appointments')"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.appointments')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Appointments
      </Link>

      <Link
  :href="route('owner.password.edit')"
  class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300"
  :class="route().current('owner.password.edit')
    ? 'bg-white text-[#182235] shadow-lg'
    : 'bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
>
  Change Password
</Link>

<Link
  :href="route('owner.shop.edit')"
  class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300"
  :class="route().current('owner.shop.edit')
    ? 'bg-white text-[#182235] shadow-lg'
    : 'bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
>
  Edit Shop
</Link>


      <Link
        :href="route('owner.reviews')"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.reviews')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Reviews
      </Link>

      <Link
        :href="route('owner.walkin')"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.walkin')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Walk-in
      </Link>

      <Link
        href="/logout"
        method="post"
        as="button"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white hover:text-[#182235] hover:shadow-md"
      >
        Log Out
      </Link>
    </nav>
  </aside>

  <!-- Hero Section -->
  <section
    class="relative flex items-center justify-center min-h-screen bg-cover bg-center"
    style="background-image: url('/images/hero-carwash.jpg');"
  >
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative z-10 flex flex-col items-center justify-center w-full px-4 py-20">
      <div class="uppercase tracking-widest text-sm text-[#FF2D2D] font-bold mb-4">Book Online</div>

      <h1 class="text-4xl md:text-6xl font-extrabold text-white text-center mb-4 leading-tight drop-shadow">
        Welcome, <span class="text-white">{{ user ? user.name : 'Owner' }}</span>!
      </h1>

      <div class="text-base md:text-lg text-gray-100 text-center mb-8 max-w-2xl">
        Your dashboard is ready and waiting. Let’s make today productive—review appointments, check feedback, and keep your business running smoothly.
      </div>

      <!-- Shop status + Open/Close -->
      <div v-if="shop" class="mt-4 flex flex-col items-center space-y-3">
        <p class="text-sm text-white">
          Shop Status:
          <span :class="shop.status === 'Open' ? 'text-green-400' : 'text-red-400'">
            {{ shop.status }}
          </span>
        </p>

        <button
          v-if="shop.status === 'Open'"
          @click="closeShop(shop.id)"
          :disabled="closing"
          class="px-8 py-3 rounded-full bg-red-600 text-white font-bold text-lg shadow hover:bg-red-700 transition disabled:opacity-60"
        >
          <span v-if="closing">Closing...</span>
          <span v-else>Close Shop</span>
        </button>

        <button
          v-else
          @click="openShop(shop.id)"
          :disabled="opening"
          class="px-8 py-3 rounded-full bg-green-600 text-white font-bold text-lg shadow hover:bg-green-700 transition disabled:opacity-60"
        >
          <span v-if="opening">Opening...</span>
          <span v-else>Open Shop</span>
        </button>
      </div>

<div class="mt-6">
  <!-- When shop is open -->
<Link
  v-if="shop.status === 'Open'"
  :href="route('owner.walkin', { id: shop.id })"
  as="button"
  class="px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] hover:scale-105 transition"
>
  Walk-in
</Link>

<button
  v-else
  disabled
  class="px-8 py-3 rounded-full bg-gray-400 text-white font-bold text-lg shadow cursor-not-allowed"
>
  🚫 Closed
</button>
</div>


    </div>
  </section>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.15s ease-out; }
</style>
