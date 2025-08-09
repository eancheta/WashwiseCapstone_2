welcome
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

  <Head title="Welcome" />

  <!-- Top Info Bar -->
<!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-wrap items-center justify-between px-8 py-2 border-b border-gray-200 text-sm font-semibold">
    <div class="flex items-center gap-2">
      <!-- Logo Image -->
      <img
        src="/images/washwiselogo2.png"
        alt="WashWise Logo"
        class="h-14 w-auto mx-auto block"
        draggable="false"
      />
    </div>
    <div class="flex gap-8 items-center text-[#002B5C]">
      <div class="flex items-center gap-2">
        <span>📞</span> Call Us <span class="font-normal">+012 345 6789</span>
      </div>
      <div class="flex items-center gap-2">
        <span>✉️</span> Email Us <span class="font-normal">info@example.com</span>
      </div>
    </div>
  </div>

  <!-- Navigation Bar -->
  <nav class="w-full bg-[#182235] flex items-center px-8 py-2 text-white font-semibold shadow z-10">
    <ul class="flex gap-8 items-center flex-1">
      <li><Link :href="'#'" class="text-[#FF2D2D]">Home</Link></li>
      <li><Link href="/about-us">About</Link></li>
      <li><Link :href="'#'">Service</Link></li>
    </ul>

    <div class="flex items-center gap-4 ml-8">
      <Link
        :href="route('login')"
        class="text-white font-semibold hover:text-[#FF2D2D] transition [text-decoration:none]"
        style="font-size: 1.0rem; line-height: 1;"
      >
        Log in
      </Link>
      <Link
        :href="route('register')"
        class="px-6 py-2 rounded-full border-2 font-semibold transition [text-decoration:none]"
        style="border-color:#FF2D2D; color:#FF2D2D; font-size: 1.0rem; line-height: 1;"
      >
        Register
      </Link>
    </div>
  </nav>

  <!-- Hero Section -->
  <section
    class="relative flex items-center justify-center min-h-screen bg-cover bg-center"
    style="background-image: url('/images/hero-carwash.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;"
  >
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 flex flex-col items-center justify-center w-full px-4 py-20">
      <div class="uppercase tracking-widest text-sm text-[#FF2D2D] font-bold mb-4">Book Online</div>
      <h1 class="text-4xl md:text-6xl font-extrabold text-white text-center mb-4 leading-tight drop-shadow">
         Effortless Booking. Exceptional Service
      </h1>
      <div class="text-base md:text-lg text-gray-100 text-center mb-8 max-w-2xl">
        “Effortlessly book appointments with trusted car wash providers. Fast. Reliable. Hassle-free.”<br>

      </div>
      <Link
        :href="'#'"
        class="px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] transition"
      >
        Explore More
      </Link>
    </div>
  </section>
</template>
