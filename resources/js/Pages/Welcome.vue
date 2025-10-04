<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showLoginModal = ref(false);
const showRegisterModal = ref(false);
const showMobileMenu = ref(false);

function openLoginModal() {
  showLoginModal.value = true;
}
function openRegisterModal() {
  showRegisterModal.value = true;
}
function closeModals() {
  showLoginModal.value = false;
  showRegisterModal.value = false;
}
</script>

<template>
  <Head title="Welcome" />

  <!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-col sm:flex-row items-center justify-between px-4 sm:px-8 py-2 border-b border-gray-200 text-sm font-semibold gap-2 sm:gap-8">
    <div class="flex items-center gap-2">
      <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mx-auto block" draggable="false" />
    </div>
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-8 text-[#002B5C] items-center">
      <div class="flex items-center gap-2">
        <span>📞</span> Call Us <span class="font-normal">+012 345 6789</span>
      </div>
      <div class="flex items-center gap-2">
        <span>✉️</span> Email Us <span class="font-normal">info@example.com</span>
      </div>
    </div>
  </div>

  <!-- Navigation Bar -->
  <nav class="w-full bg-[#182235] flex items-center px-4 sm:px-8 py-2 text-white font-semibold shadow z-10">
    <!-- Desktop Links -->
    <ul class="hidden sm:flex gap-8 items-center flex-1">
      <li><Link href="/" class="text-[#FF2D2D]">Home</Link></li>
      <li><Link href="/about-us">About</Link></li>
      <li><Link href="/services">Service</Link></li>
    </ul>

    <!-- Mobile Hamburger -->
    <div class="sm:hidden flex-1 flex justify-between items-center">
      <span class="font-bold text-lg text-[#FF2D2D]">Menu</span>
      <button @click="showMobileMenu = !showMobileMenu" class="text-white text-2xl">☰</button>
    </div>

    <div class="flex items-center gap-4 ml-0 sm:ml-8">
      <button @click="openLoginModal" class="text-white font-semibold hover:text-[#FF2D2D] transition text-sm sm:text-base">
        Log in
      </button>
      <button @click="openRegisterModal" class="px-4 sm:px-6 py-2 rounded-full border-2 font-semibold transition border-[#FF2D2D] text-[#FF2D2D] text-sm sm:text-base">
        Register
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div v-if="showMobileMenu" class="sm:hidden bg-[#182235] text-white flex flex-col gap-4 px-4 py-4">
    <Link href="/" class="hover:text-[#FF2D2D]">Home</Link>
    <Link href="/about-us" class="hover:text-[#FF2D2D]">About</Link>
    <Link href="/services" class="hover:text-[#FF2D2D]">Service</Link>
  </div>

  <!-- Login Modal -->
  <div v-if="showLoginModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 sm:p-8 w-full sm:max-w-xs md:max-w-sm shadow-lg relative">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Login as:</h3>
      <div class="flex flex-col gap-4">
        <Link href="/login" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">User</Link>
        <Link href="/owner/login" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div v-if="showRegisterModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 sm:p-8 w-full sm:max-w-xs md:max-w-sm shadow-lg relative">
      <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 hover:text-[#FF2D2D] text-xl">&times;</button>
      <h3 class="text-lg font-bold mb-4 text-[#182235] text-center">Register as:</h3>
      <div class="flex flex-col gap-4">
        <Link href="/register" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">User</Link>
        <Link href="/owner/register" class="px-4 py-3 rounded bg-[#182235] text-white font-semibold text-center hover:bg-[#FF2D2D] transition">Business Owner</Link>
      </div>
    </div>
  </div>

  <!-- Hero Section -->
  <section
    class="relative flex items-center justify-center min-h-screen bg-cover bg-center"
    style="background-image: url('/images/hero-carwash.jpg');"
  >
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 flex flex-col items-center justify-center w-full px-4 py-16 sm:py-20">
      <div class="uppercase tracking-widest text-sm text-[#FF2D2D] font-bold mb-4">Book Online</div>
      <h1 class="text-3xl sm:text-4xl md:text-6xl font-extrabold text-white text-center mb-4 leading-tight drop-shadow">
        Effortless Booking. Exceptional Service
      </h1>
      <div class="text-base sm:text-lg text-gray-100 text-center mb-8 max-w-2xl">
        “Effortlessly book appointments with trusted car wash providers. Fast. Reliable. Hassle-free.”
      </div>
      <Link href="#"
            class="w-full sm:w-auto px-6 sm:px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] transition text-center">
        Explore More
      </Link>
    </div>
  </section>
</template>
