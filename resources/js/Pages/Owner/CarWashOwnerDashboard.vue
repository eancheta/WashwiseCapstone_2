<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route } from 'ziggy-js'
import type { AppPageProps, Shop, User } from '@/types'

// Sidebar toggle
const sidebarOpen = ref(false)
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

// ✅ Strong typing for Inertia props
const page = usePage<AppPageProps>()
const user = computed<User | null>(() => page.props.auth?.user ?? null)
const shop = computed<Shop | null>(() => page.props.shop ?? null)

// Shop actions (call Laravel routes)
function closeShop(id: number) {
  router.post(route('owner.shop.close', { id }))
}
function openShop(id: number) {
  router.post(route('owner.shop.open', { id }))
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
      >
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
        <span class="block h-0.5 bg-gray-800 rounded"></span>
      </button>
      <img
        src="/images/washwiselogo2.png"
        alt="WashWise Logo"
        class="h-10 w-auto select-none"
        draggable="false"
      />
    </div>

    <div class="flex items-center space-x-2 px-3 py-2 rounded-lg">
      <span class="text-gray-700 font-medium">
        {{ user ? user.name : 'Owner' }}
      </span>
    </div>
  </div>

  <!-- Sidebar -->
  <aside
    :class="[
      'fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <div class="flex justify-between items-center p-4 border-b border-gray-700">
      <h2 class="text-lg font-bold">Menu</h2>
      <button
        @click="toggleSidebar"
        class="text-gray-400 hover:text-red-500 text-2xl"
      >&times;</button>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-2">
      <!-- Appointments -->
      <Link
        :href="route('owner.appointments')"
        class="block px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.appointments')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-[#1F3A5F] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Appointments
      </Link>

      <!-- Reviews -->
      <Link
        :href="route('owner.reviews')"
        class="block px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.reviews')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-[#1F3A5F] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Reviews
      </Link>

      <!-- Walk-in -->
      <Link
        :href="route('owner.walkin')"
        class="block px-4 py-3 rounded-xl font-medium transition-all duration-300"
        :class="route().current('owner.walkin')
          ? 'bg-white text-[#182235] shadow-lg'
          : 'bg-[#1F3A5F] text-white hover:bg-white hover:text-[#182235] hover:shadow-md'"
      >
        Walk-in
      </Link>

      <!-- ✅ Plain Shop Open/Close Buttons -->
      <div v-if="shop" class="mt-6 px-4">
        <p class="text-sm mb-2">
          Shop Status:
          <span :class="shop.status === 'open' ? 'text-green-400' : 'text-red-400'">
            {{ shop.status }}
          </span>
        </p>

        <button
          v-if="shop.status === 'open'"
          @click="closeShop(shop.id)"
          class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
        >
          Close Shop
        </button>

        <button
          v-else
          @click="openShop(shop.id)"
          class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
        >
          Open Shop
        </button>
      </div>

      <!-- Log Out -->
      <Link
        href="/logout"
        method="post"
        as="button"
        class="block w-full text-left px-4 py-3 rounded-xl font-medium transition-all duration-300 bg-[#1F3A5F] text-white hover:bg-white hover:text-[#182235] hover:shadow-md"
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
    <div
      class="relative z-10 flex flex-col items-center justify-center w-full px-4 py-20"
    >
      <div
        class="uppercase tracking-widest text-sm text-[#FF2D2D] font-bold mb-4"
      >
        Book Online
      </div>
      <h1
        class="text-4xl md:text-6xl font-extrabold text-white text-center mb-4 leading-tight drop-shadow"
      >
        Welcome,
        <span
          class="text-4xl md:text-6xl font-extrabold text-white leading-tight drop-shadow"
        >
          {{ user ? user.name : 'Owner' }}
        </span>!
      </h1>
      <div
        class="text-base md:text-lg text-gray-100 text-center mb-8 max-w-2xl"
      >
        Your dashboard is ready and waiting. Let’s make today productive—review
        appointments, check feedback, and keep your business running smoothly.
      </div>
      <Link
        :href="route('owner.walkin')"
        class="px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] transition"
      >
        Walk-in
      </Link>
    </div>
  </section>
</template>

<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out;
}
</style>
