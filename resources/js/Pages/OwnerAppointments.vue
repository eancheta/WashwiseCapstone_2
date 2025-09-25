<template>
  <Head title="Appointments" />

  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
        <button
          @click="goBack"
          class="px-4 py-2 bg-gray-700 text-white rounded-lg shadow hover:bg-gray-800"
        >
          Back to Dashboard
        </button>
      </div>
    </div>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Filters -->
        <div class="mb-4 flex items-center space-x-4">
          <select v-model="dateRange" class="border rounded p-2">
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom</option>
          </select>
          <div v-if="dateRange === 'custom'" class="flex items-center space-x-2">
            <input type="date" v-model="fromDate" class="border rounded p-2" />
            <span>to</span>
            <input type="date" v-model="toDate" class="border rounded p-2" />
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white border rounded-lg shadow">
            <thead>
              <tr class="bg-gray-50 border-b">
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Name</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Car Size</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Contact</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Booking Time</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Booking Date</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Slot</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Payment Proof</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Amount</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="appt in filteredAppointments"
                :key="appt.id"
                class="border-b hover:bg-gray-50"
              >
                <td class="px-4 py-3">{{ appt.name }}</td>
                <td class="px-4 py-3">{{ appt.size_of_the_car }}</td>
                <td class="px-4 py-3">{{ appt.contact_no }}</td>
                <td class="px-4 py-3">{{ appt.email }}</td>
                <td class="px-4 py-3">{{ appt.time_of_booking }}</td>
                <td class="px-4 py-3">{{ appt.date_of_booking }}</td>
                <td class="px-4 py-3">{{ appt.slot_number }}</td>

                <!-- Payment Proof -->
                <td class="px-4 py-3 text-center">
                  <img
  v-if="appt.payment_proof"
  :src="getPaymentProofSrc(appt)!"
  alt="Payment Proof"
  class="h-16 w-16 object-cover rounded border mx-auto"
  @error="handleImageError"
/>

                  <span v-else class="text-gray-400 italic">Walk_IN</span>
                </td>

                <td class="px-4 py-3">{{ appt.payment_amount ?? '-' }}</td>

                <!-- Status Badge -->
                <td class="px-4 py-3">
                  <span
                    v-if="appt.status === 'pending'"
                    class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full"
                  >
                    Pending
                  </span>
                  <span
                    v-else-if="appt.status === 'approved'"
                    class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full"
                  >
                    Approved
                  </span>
                  <span
                    v-else-if="appt.status === 'declined'"
                    class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full"
                  >
                    Declined
                  </span>
                  <span
                    v-else-if="appt.status === 'paid'"
                    class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full"
                  >
                    Paid
                  </span>
                  <span v-else class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                    {{ appt.status }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-4 py-3 text-center space-x-2">
                  <button
                    v-if="appt.status === 'pending'"
                    @click="approve(appt.id)"
                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                  >
                    Approve
                  </button>
                  <button
                    v-if="appt.status === 'pending'"
                    @click="decline(appt.id)"
                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                  >
                    Decline
                  </button>
                </td>
              </tr>
              <tr v-if="filteredAppointments.length === 0">
                <td colspan="11" class="px-4 py-3 text-center text-gray-500 italic">
                  No appointments found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

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

const props = defineProps<{ appointments: Appointment[] }>()

// ✅ Cloudinary base path
const cloudBase = 'https://res.cloudinary.com/dqfyjxaw2/image/upload/v1758645874/'

// --- Status actions ---
function approve(id: number) {
  router.post(`/owner/appointments/${id}/approve`)
}
function decline(id: number) {
  router.post(`/owner/appointments/${id}/decline`)
}

// ✅ Payment proof source
function getPaymentProofSrc(appt: Appointment) {
  if (!appt.payment_proof) return null
  if (appt.payment_proof.startsWith('http')) {
    return appt.payment_proof
  }
  return cloudBase + appt.payment_proof
}

// ✅ On broken image
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
    end = new Date(start); end.setDate(end.getDate() + 1)
  } else if (dateRange.value === 'week') {
    const firstDay = today.getDate() - today.getDay()
    start = new Date(today.setDate(firstDay))
    end = new Date(today.setDate(firstDay + 7))
  } else if (dateRange.value === 'month') {
    start = new Date(today.getFullYear(), today.getMonth(), 1)
    end = new Date(today.getFullYear(), today.getMonth() + 1, 1)
  } else if (dateRange.value === 'custom' && fromDate.value && toDate.value) {
    start = new Date(fromDate.value)
    end = new Date(toDate.value); end.setDate(end.getDate() + 1)
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
