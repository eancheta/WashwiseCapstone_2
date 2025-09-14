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
    end.setDate(end.getDate() + 1) // include end date
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

  <div class="min-h-screen bg-[#F8FAFC] py-6 px-2 flex flex-col items-center">
    <div class="w-full max-w-6xl bg-white rounded-2xl shadow-lg p-4 sm:p-8">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-center text-[#002B5C] mb-6">
        Customer Appointments
      </h1>
<div class="mb-4">
  <button
    @click="goBack"
    class="px-4 py-2 bg-[#002B5C] text-white font-semibold rounded-lg shadow hover:bg-[#003366] transition"
  >
    ← Return to Dashboard
  </button>
</div>
      <!-- 🔹 Date Range Filter -->
      <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Date Range</label>
          <select v-model="dateRange" class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom</option>
          </select>
        </div>

        <div v-if="dateRange==='custom'">
          <label class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">From</label>
          <input type="date" v-model="fromDate" class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap2" />
        </div>

        <div v-if="dateRange==='custom'">
          <label class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">To</label>
          <input type="date" v-model="toDate" class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap" />
        </div>
      </div>

      <!-- 🔹 Appointment Table -->
      <div v-if="filteredAppointments.length === 0" class="text-gray-500 text-lg text-center py-8">
        No appointments found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-[700px] w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Date</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Time</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Status</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">ID</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Name</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Email</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Car Size</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Contact</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Slot</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Created</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Payment Proof</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Amount</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Actions</th>
            </tr>
          </thead>

          <tbody class="text-[#182235] font-medium">
            <tr v-for="appt in filteredAppointments" :key="appt.id" class="border-t hover:bg-gray-50">
              <td>{{ appt.date_of_booking }}</td>
              <td>{{ appt.time_of_booking }}</td>
              <td>
                <span v-if="appt.status === 'approved'" class="text-green-600 font-semibold">Approved</span>
                <span v-else-if="appt.status === 'declined'" class="text-red-600 font-semibold">Declined</span>
                <span v-else class="text-gray-600 font-semibold">Pending</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ appt.id }}</td>
              <td>{{ appt.name }}</td>
              <td>{{ appt.email || 'Walk_IN' }} </td>
              <td>{{ appt.size_of_the_car }}</td>
              <td>{{ appt.contact_no }}</td>
              <td> {{ appt.slot_number }}</td>
              <td>{{ appt.created_at }}</td>
              <td class="text-center">
                <img
                  v-if="appt.payment_proof"
                  :src="appt.payment_proof"
                  alt="Payment Proof"
                  class="h-12 w-12 object-cover rounded border mx-auto"
                  @error="handleImageError"
                />
                <span v-else class="text-gray-400 italic">Walk_IN</span>
              </td>
              <td class="text-center font-bold text-[#FF2D2D]">{{ appt.payment_amount ?? 'Walk_IN' }}</td>
              <td class="text-center space-x-2">
                <button @click="approve(appt.id)" class="px-3 py-1 bg-green-600 text-white rounded">Approve</button>
                <button @click="decline(appt.id)" class="px-3 py-1 bg-red-600 text-white rounded">Decline</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
