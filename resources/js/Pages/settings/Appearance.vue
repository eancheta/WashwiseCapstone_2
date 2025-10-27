<script setup lang="ts">

import { withDefaults } from 'vue';
import { Head, router } from '@inertiajs/vue3'

function goBack() {
  router.visit('/dashboard') // for customer dashboard
}

interface Shop {
  id: number
  name: string
  address?: string | null
}

interface Booking {
  id: number
  name: string
  size_of_the_car: string
  contact_no: string
  date_of_booking: string
  time_of_booking: string
  slot_number: number
  payment_amount?: number | null
  payment_status?: string | null
  shop: Shop
  created_at?: string | null
}

const props = withDefaults(defineProps<{
  bookings?: Booking[]
  message?: string | null
  pageTitle?: string
}>(), {
  bookings: () => [],
  message: null,
  pageTitle: 'Transaction History'
});
</script>

<template>
  <Head :title="props.pageTitle" />

  <div class="min-h-screen bg-[#F8FAFC] flex flex-col items-center  px-2 pt-16 ">
    <div class="w-full max-w-3xl bg-white rounded-xl shadow border border-gray-100 p-8">
      <h1 class="text-2xl font-bold text-[#182235] mb-6 text-center tracking-tight">
        {{ props.pageTitle }}
      </h1>
<div class="absolute top-4 left-4">
  <button
    @click="goBack"
    type="button"
     class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-black rounded-lg text-sm font-medium shadow-md hover:bg-[#FF2D2D] transition">

    ⬅ Return
  </button>
</div>


      <div v-if="props.bookings.length > 0" class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
          <thead>
            <tr class="bg-gray-50 border-b">
              <th class="px-4 py-3 font-semibold text-[#182235] text-left">Shop</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-left">Car Size</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-left">Date</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-left">Time</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-center">Slot</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-right">Amount</th>
              <th class="px-4 py-3 font-semibold text-[#182235] text-center">Status</th>
            </tr>
          </thead>
    <tbody class="text-[#182235] font-medium">
    <tr v-for="b in props.bookings" :key="`${b.shop.id}-${b.id}`" class="border-b hover:bg-gray-50 transition">
    <td class="px-4 py-3 font-medium">{{ b.shop.name }}</td>
    <td class="px-4 py-3">{{ b.size_of_the_car }}</td>
    <td class="px-4 py-3">{{ b.date_of_booking }}</td>
    <td class="px-4 py-3">{{ b.time_of_booking }}</td>
    <td class="px-4 py-3 text-center">#{{ b.slot_number }}</td>
    <td class="px-4 py-3 text-right text-[#FF2D2D] font-semibold">₱{{ b.payment_amount ?? 'N/A' }}</td>
    <td class="px-4 py-3 text-center">
      <span
        :class="b.payment_status === 'paid'
          ? 'inline-block bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-semibold'
          : 'inline-block bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold'"
      >
        {{ b.payment_status === 'paid' ? 'Paid' : (b.payment_status ?? 'Unpaid') }}
      </span>
    </td>
  </tr>
    </tbody>
        </table>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-10">
        <svg class="w-14 h-14 text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-base text-gray-400 font-medium">{{ props.message || 'You don’t have any bookings yet.' }}</span>
      </div>
    </div>
  </div>
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6">

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
