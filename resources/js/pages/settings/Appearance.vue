<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { withDefaults } from 'vue';

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
  pageTitle: 'Transactions'
});
</script>

<template>
  <Head :title="props.pageTitle" />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">{{ props.pageTitle }}</h1>

    <div v-if="props.bookings.length > 0" class="overflow-x-auto">
      <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
          <tr>
            <th class="px-4 py-2 border">Shop</th>
            <th class="px-4 py-2 border">Car Size</th>
            <th class="px-4 py-2 border">Date</th>
            <th class="px-4 py-2 border">Time</th>
            <th class="px-4 py-2 border">Slot</th>
            <th class="px-4 py-2 border">Amount</th>
            <th class="px-4 py-2 border">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in props.bookings" :key="`${b.shop.id}-${b.id}`" class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-2 border font-semibold text-gray-800 dark:text-gray-200">{{ b.shop.name }}</td>
            <td class="px-4 py-2 border text-gray-700 dark:text-gray-300">{{ b.size_of_the_car }}</td>
            <td class="px-4 py-2 border text-gray-700 dark:text-gray-300">{{ b.date_of_booking }}</td>
            <td class="px-4 py-2 border text-gray-700 dark:text-gray-300">{{ b.time_of_booking }}</td>
            <td class="px-4 py-2 border text-center text-gray-700 dark:text-gray-300">#{{ b.slot_number }}</td>
            <td class="px-4 py-2 border text-red-600 dark:text-red-400 font-bold">₱{{ b.payment_amount ?? 'N/A' }}</td>
            <td class="px-4 py-2 border">
              <span
                :class="b.payment_status === 'paid'
                  ? 'text-green-600 dark:text-green-400 font-semibold'
                  : 'text-yellow-600 dark:text-yellow-400 font-semibold'"
              >
                {{ b.payment_status ?? 'unpaid' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="text-gray-600 dark:text-gray-400">{{ props.message || 'You don’t have any bookings yet.' }}</p>
  </div>
</template>
