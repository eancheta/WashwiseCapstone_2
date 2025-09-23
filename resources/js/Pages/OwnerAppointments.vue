<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

interface Appointment {
  id: number
  name: string
  size_of_the_car: string
  contact_no: string
  email: string
  time_of_booking: string
  date_of_booking: string
  slot_number: string
  created_at: string
  status?: string | null
  payment_proof?: string | null
  payment_amount?: number | null
}

const props = defineProps<{
  appointments: Appointment[]
}>()

function approve(id: number) {
  router.post(`/owner/appointments/${id}/approve`)
}

function decline(id: number) {
  router.post(`/owner/appointments/${id}/decline`)
}

function handleImageError(event: Event) {
  const target = event.target as HTMLImageElement
  target.outerHTML = '<span class="text-gray-600">No proof</span>'
}

// --- Date filtering ---
const dateRange = ref<string>('all')
const fromDate = ref<string>('')
const toDate = ref<string>('')

const filteredAppointments = computed(() => {
  let data = [...props.appointments]

  const today = new Date()
  let start: Date | null = null
  let end: Date | null = null

  if (dateRange.value === 'today') {
    start = new Date(today.toDateString())
    end = new Date(start)
    end.setDate(end.getDate() + 1)
  } else if (dateRange.value === 'week') {
    const firstDay = today.getDate() - today.getDay()
    start = new Date(today.setDate(firstDay))
    end = new Date(today.setDate(firstDay + 7))
  } else if (dateRange.value === 'month') {
    start = new Date(today.getFullYear(), today.getMonth(), 1)
    end = new Date(today.getFullYear(), today.getMonth() + 1, 1)
  } else if (dateRange.value === 'custom' && fromDate.value && toDate.value) {
    start = new Date(fromDate.value)
    end = new Date(toDate.value)
    end.setDate(end.getDate() + 1)
  }

  if (start && end) {
    data = data.filter(a => {
      const apptDate = new Date(a.date_of_booking)
      return apptDate >= start! && apptDate < end!
    })
  }

  return data
})

function goBack() {
  router.visit('/owner/dashboard')
}
</script>

<template>
  <Head title="Owner Appointments" />

  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-100 py-6 px-4 flex flex-col items-center">
    <div class="w-full max-w-7xl p-6 sm:p-10">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#002B5C] mb-2">
          Customer Appointments
        </h1>
        <p class="text-gray-500">Manage and monitor all customer bookings efficiently</p>
      </div>

      <!-- Return Button -->
      <div class="mb-6 flex justify-start">
        <button
          @click="goBack"
          class="px-6 py-3 bg-[#002B5C] text-white font-semibold rounded-xl shadow-md hover:bg-[#FF2D2D] transition transform hover:-translate-y-0.5"
        >
          ← Return to Dashboard
        </button>
      </div>

      <!-- Filter Card -->
      <div class="mb-8 p-6 bg-white shadow-lg rounded-2xl grid grid-cols-1 sm:grid-cols-3 gap-4 border border-gray-200">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Date Range</label>
          <select v-model="dateRange" class="w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none">
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom</option>
          </select>
        </div>

        <div v-if="dateRange==='custom'">
          <label class="block text-sm font-semibold text-gray-600 mb-1">From</label>
          <input type="date" v-model="fromDate" class="w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none" />
        </div>

        <div v-if="dateRange==='custom'">
          <label class="block text-sm font-semibold text-gray-600 mb-1">To</label>
          <input type="date" v-model="toDate" class="w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none" />
        </div>
      </div>

      <!-- Appointment Table -->
      <div v-if="filteredAppointments.length === 0" class="text-gray-500 text-lg text-center py-12">
        No appointments found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-xl overflow-hidden shadow-sm">
          <thead class="bg-gradient-to-r from-[#002B5C] to-[#00509E] text-white">
            <tr>
              <th class="px-4 py-3 text-left">Date</th>
              <th class="px-4 py-3 text-left">Time</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-center">ID</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-left">Car Size</th>
              <th class="px-4 py-3 text-left">Contact</th>
              <th class="px-4 py-3 text-left">Slot</th>
              <th class="px-4 py-3 text-left">Created</th>
              <th class="px-4 py-3 text-center">Payment Proof</th>
              <th class="px-4 py-3 text-center">Amount</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="text-[#182235] font-medium">
            <tr v-for="(appt, idx) in filteredAppointments" :key="appt.id"
                :class="idx % 2 === 0 ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 hover:bg-gray-100 transition'">
              <td class="px-4 py-3">{{ appt.date_of_booking }}</td>
              <td class="px-4 py-3">{{ appt.time_of_booking }}</td>
              <td class="px-4 py-3">
                <span v-if="appt.status === 'approved'" class="text-green-600 font-semibold">Approved</span>
                <span v-else-if="appt.status === 'declined'" class="text-red-600 font-semibold">Declined</span>
                <span v-else class="text-gray-600 font-semibold">Pending</span>
              </td>
              <td class="px-4 py-3 text-center">{{ appt.id }}</td>
              <td class="px-4 py-3">{{ appt.name }}</td>
              <td class="px-4 py-3">{{ appt.email || 'Walk_IN' }}</td>
              <td class="px-4 py-3">{{ appt.size_of_the_car }}</td>
              <td class="px-4 py-3">{{ appt.contact_no }}</td>
              <td class="px-4 py-3">{{ appt.slot_number }}</td>
              <td class="px-4 py-3">{{ appt.created_at }}</td>
              <td class="px-4 py-3 text-center">
                <img
                  v-if="appt.payment_proof"
                  :src="appt.payment_proof"
                  alt="Payment Proof"
                  class="h-16 w-16 object-cover rounded border mx-auto"
                  @error="handleImageError"
                />
                <span v-else class="text-gray-400 italic">Walk_IN</span>
              </td>
              <td class="px-4 py-3 text-center font-bold text-[#FF2D2D]">{{ appt.payment_amount ?? 'Walk_IN' }}</td>
              <td class="px-4 py-3 text-center flex justify-center gap-3">
                <button @click="approve(appt.id)" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5">
                  Approve
                </button>
                <button @click="decline(appt.id)" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5">
                  Decline
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

