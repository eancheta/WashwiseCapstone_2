<template>
  <Head title="Owner Appointments" />

  <div class="min-h-screen bg-gradient-to-br from-[#e3f2fd] via-[#ffffff] to-[#e1f5fe] py-10 px-6 flex flex-col items-center">
    <div class="w-full max-w-7xl backdrop-blur-lg bg-white/90 border border-gray-200 shadow-2xl rounded-3xl p-8 sm:p-10 relative transition-all">

      <!-- Return Button -->
      <div class="absolute top-6 left-6">
        <button
          @click="goBack"
          class="px-5 py-2 bg-[#002B5C] text-white font-semibold rounded-xl shadow-md hover:bg-[#FF2D2D] transition-all duration-300 transform hover:-translate-y-0.5"
        >
          ← Return to Dashboard
        </button>
      </div>

      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-[#002B5C] mb-3 tracking-tight drop-shadow-sm">
          Customer Appointments
        </h1>
        <p class="text-gray-600 text-lg">Monitor and manage all customer bookings effortlessly</p>
      </div>

      <!-- Filter Card -->
      <div class="mb-10 p-8 bg-white border border-gray-200 rounded-2xl shadow-md grid grid-cols-1 sm:grid-cols-3 gap-6 transition-all duration-300">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">Date Range</label>
          <select
            v-model="dateRange"
            class="w-full px-4 py-3 font-medium text-[#182235] border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none bg-gray-50 hover:bg-white transition"
          >
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom</option>
          </select>
        </div>

        <div v-if="dateRange==='custom'">
          <label class="block text-sm font-bold text-gray-700 mb-2">From</label>
          <input
            type="date"
            v-model="fromDate"
            class="w-full px-4 py-3 font-medium text-[#182235] border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none bg-gray-50 hover:bg-white transition"
          />
        </div>

        <div v-if="dateRange==='custom'">
          <label class="block text-sm font-bold text-gray-700 mb-2">To</label>
          <input
            type="date"
            v-model="toDate"
            class="w-full px-4 py-3 font-medium text-[#182235] border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none bg-gray-50 hover:bg-white transition"
          />
        </div>
      </div>

      <!-- Appointment Table -->
      <div v-if="filteredAppointments.length === 0" class="text-gray-500 text-lg text-center py-16">
        No appointments found.
      </div>

      <div v-else class="overflow-x-auto rounded-2xl shadow-lg border border-gray-200 bg-white/90 backdrop-blur-sm">
        <table class="w-full border-collapse text-sm sm:text-base">
          <thead class="bg-gradient-to-r from-[#002B5C] to-[#00509E] text-white">
            <tr>
              <th v-for="header in headers" :key="header.value" class="px-4 py-3 text-left font-semibold uppercase tracking-wide">
                {{ header.text }}
              </th>
            </tr>
          </thead>

          <tbody class="text-[#182235] font-medium divide-y divide-gray-200">
            <tr
              v-for="(appt, idx) in filteredAppointments"
              :key="appt.id"
              :class="idx % 2 === 0 ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 hover:bg-gray-100'"
              class="transition-colors duration-200"
            >
              <td class="px-4 py-3">{{ appt.date_of_booking }}</td>
              <td class="px-4 py-3">{{ appt.time_of_booking }}</td>
              <td class="px-4 py-3">
                <span
                  v-if="appt.status === 'approved'"
                  class="px-2 py-1 bg-green-100 text-green-700 font-semibold rounded-lg text-xs sm:text-sm"
                >Approved</span>
                <span
                  v-else-if="appt.status === 'declined'"
                  class="px-2 py-1 bg-red-100 text-red-700 font-semibold rounded-lg text-xs sm:text-sm"
                >Declined</span>
                <span
                  v-else
                  class="px-2 py-1 bg-gray-200 text-gray-700 font-semibold rounded-lg text-xs sm:text-sm"
                >Pending</span>
              </td>
              <td class="px-4 py-3 text-center">{{ appt.id }}</td>
              <td class="px-4 py-3">{{ appt.name }}</td>
              <td class="px-4 py-3">{{ appt.email || 'Walk_IN' }}</td>
              <td class="px-4 py-3">{{ appt.size_of_the_car }}</td>
              <td class="px-4 py-3">{{ appt.contact_no }}</td>
              <td class="px-4 py-3">{{ appt.slot_number }}</td>
              <td class="px-4 py-3">{{ appt.created_at }}</td>

              <!-- Payment Proof -->
              <td class="px-4 py-3 text-center">
                <img
                  :src="getPaymentProofSrc(appt)"
                  alt="Payment Proof"
                  class="h-16 w-16 object-cover rounded-lg border mx-auto cursor-pointer hover:scale-110 transition-transform duration-200"
                  @click="openImagePreview(getPaymentProofSrc(appt))"
                  @error="handleImageError"
                />
              </td>

              <td class="px-4 py-3 text-center font-bold text-[#FF2D2D]">{{ appt.payment_amount ?? 'Walk_IN' }}</td>

              <td class="px-4 py-3 text-center flex justify-center gap-3">
                <button
                  @click="approve(appt.id)"
                  class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5"
                >
                  Approve
                </button>
                <button
                  @click="openDeclineModal(appt.id)"
                  class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5"
                >
                  Decline
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Decline Modal -->
  <div v-if="showDeclineModal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative">
      <h2 class="text-xl font-bold text-[#002B5C] mb-4">Decline Appointment</h2>
      <p class="text-gray-600 mb-3">Please write the reason for declining this appointment:</p>
      <textarea
        v-model="declineReason"
        rows="4"
        placeholder="Type reason here..."
        class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-[#FF2D2D] focus:outline-none"
      ></textarea>

      <div class="mt-6 flex justify-end gap-3">
        <button
          @click="showDeclineModal = false"
          class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          @click="submitDecline"
          class="px-5 py-2 bg-[#FF2D2D] text-white font-semibold rounded-lg hover:bg-[#E02626] transition"
        >
          Send & Decline
        </button>
      </div>
    </div>
  </div>

  <!-- Image Preview Modal -->
  <div v-if="showImagePreview" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
    <img :src="previewSrc" alt="Preview" class="max-h-[80vh] rounded-xl shadow-2xl border-4 border-white" />
    <button @click="showImagePreview=false" class="absolute top-6 right-8 text-white text-3xl font-bold">✕</button>
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

// --- Status actions ---
function approve(id: number) {
  router.post(`/owner/appointments/${id}/approve`)
}

// --- Decline logic ---
const showDeclineModal = ref(false)
const declineReason = ref('')
const selectedId = ref<number | null>(null)

function openDeclineModal(id: number) {
  selectedId.value = id
  showDeclineModal.value = true
}

function submitDecline() {
  if (!selectedId.value) return
  router.post(`/owner/appointments/${selectedId.value}/decline`, {
    reason: declineReason.value
  })
  showDeclineModal.value = false
  declineReason.value = ''
}

// --- Payment Proof Logic ---
function getPaymentProofSrc(appt: Appointment): string {
  if (!appt.payment_proof) return '/images/hero-carwash.jpg'
  if (appt.payment_proof.startsWith('http')) return appt.payment_proof
  if (appt.payment_proof.startsWith('/storage/')) return appt.payment_proof
  return `/storage/${appt.payment_proof}`
}

function handleImageError(event: Event) {
  const target = event.target as HTMLImageElement
  target.src = '/images/hero-carwash.jpg'
}

const showImagePreview = ref(false)
const previewSrc = ref('')

function openImagePreview(src: string) {
  previewSrc.value = src
  showImagePreview.value = true
}

// --- Filters ---
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
const headers = [
  { text: 'Date', value: 'date_of_booking' },
  { text: 'Time', value: 'time_of_booking' },
  { text: 'Status', value: 'status' },
  { text: 'ID', value: 'id' },
  { text: 'Name', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Car Size', value: 'size_of_the_car' },
  { text: 'Contact', value: 'contact_no' },
  { text: 'Slot', value: 'slot_number' },
  { text: 'Created', value: 'created_at' },
  { text: 'Payment Proof', value: 'payment_proof' },
  { text: 'Amount', value: 'payment_amount' },
  { text: 'Actions', value: 'actions' },
];
</script>
