<<<<<<< HEAD
<!-- resources/js/Pages/UserDashboard.vue -->
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const sidebarOpen = ref(false);
const profileMenuOpen = ref(false);

const user = {
  name: 'John Doe',
  avatar: '/images/default-avatar.png'
};

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

function toggleProfileMenu() {
  profileMenuOpen.value = !profileMenuOpen.value;
}
</script>

<template>
  <Head title="Owner Dashboard" />

  <!-- Top Bar -->
  <div class="w-full bg-white flex items-center justify-between px-4 py-2 border-b border-gray-200 text-sm font-semibold relative">
    <!-- Left: Hamburger + Logo -->
    <div class="flex items-center">
      <button
        @click="toggleSidebar"
        class="flex flex-col justify-between w-6 h-5 mr-4 focus:outline-none"
      >
        <span class="block h-0.5 bg-black"></span>
        <span class="block h-0.5 bg-black"></span>
        <span class="block h-0.5 bg-black"></span>
      </button>

      <img
        src="/images/washwiselogo2.png"
        alt="WashWise Logo"
        class="h-10 w-auto"
        draggable="false"
      />
    </div>


    <!-- Right: Styled Profile Dropdown -->
    <div class="relative">
      <button @click="toggleProfileMenu" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
        <img :src="user.avatar" alt="Profile" class="w-8 h-8 rounded-full border border-gray-300" />
        <span class="text-gray-700 font-medium">{{ user.name }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Dropdown -->
      <div
        v-if="profileMenuOpen"
        class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 animate-fadeIn"
      >
        <div class="py-2">
          <Link href="/profile/edit" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-black transition rounded-lg">
            <span>👤</span> Edit Profile
          </Link>
          <Link href="/settings" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-black transition rounded-lg">
            <span>⚙️</span> Settings
          </Link>
          <Link href="/logout" method="post" as="button" class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-lg">
            <span>🚪</span> Log Out
          </Link>
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <aside
    :class="['fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300', sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
  >
    <div class="flex justify-between items-center p-4 border-b border-gray-700">
      <h2 class="text-lg font-bold">Menu</h2>
      <button @click="toggleSidebar" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 space-y-2 px-3 mt-4">
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] hover:from-[#E74C3C] hover:to-[#C0392B] text-white font-semibold shadow-md transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#E74C3C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
        </svg>
        Appointments
      </a>

      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] hover:from-[#E74C3C] hover:to-[#C0392B] text-white font-semibold shadow-md transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#E74C3C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
        </svg>
        Services
      </a>

      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] hover:from-[#E74C3C] hover:to-[#C0392B] text-white font-semibold shadow-md transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#E74C3C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-5m0 0l-5 5m5-5H3" />
        </svg>
        Customers
      </a>

      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] hover:from-[#E74C3C] hover:to-[#C0392B] text-white font-semibold shadow-md transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#E74C3C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
        Feedback
      </a>
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
        Effortless Booking. Exceptional Service
      </h1>
      <div class="text-base md:text-lg text-gray-100 text-center mb-8 max-w-2xl">
        “Effortlessly book appointments with trusted car wash providers. Fast. Reliable. Hassle-free.”
      </div>
      <Link
        href="#"
        class="px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] transition"
      >
        Explore More
      </Link>
    </div>
  </section>
</template>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out;
}
</style>
=======
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Car Wash Owner Dashboard" />

  <div class="min-h-screen p-6 bg-gray-100">
    <h1 class="text-3xl font-bold mb-4">Hello</h1>

    <p>
      hi
    </p>

  </div>
</template>
>>>>>>> parent of 1b912a2 (BusinessOwnerDashboard)
