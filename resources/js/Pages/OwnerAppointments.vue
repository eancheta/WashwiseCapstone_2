<template>
  <Head title="Owner Appointments" />

  <div class="h-screen w-screen bg-gradient-to-br from-[#001F3F] via-[#003366] to-[#00509E] text-white overflow-hidden flex flex-col">
    <!-- Top Section -->
    <div class="flex justify-between items-center px-8 py-4 bg-white/10 backdrop-blur-md border-b border-white/20 shadow-md">
      <button
        @click="goBack"
        class="px-5 py-2 bg-white/20 text-white font-semibold rounded-xl shadow-lg hover:bg-white/30 border border-white/30 transition-all hover:scale-105 backdrop-blur-md"
      >
        ← Back
      </button>

      <div class="text-center">
        <h1 class="text-4xl font-extrabold drop-shadow-lg tracking-wide">Customer Appointments ✨</h1>
        <p class="text-gray-200 text-sm">Track and manage all bookings with clarity and style</p>
      </div>

      <div></div>
    </div>

    <!-- Filter Bar -->
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 bg-white/10 border-b border-white/20 backdrop-blur-sm">
      <div>
        <label class="block text-sm font-semibold text-gray-200 mb-1">Date Range</label>
        <select v-model="dateRange" class="w-full px-4 py-3 rounded-lg text-[#001F3F] font-semibold focus:ring-4 focus:ring-[#FF2D2D]/60 outline-none">
          <option value="all">All</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="custom">Custom</option>
        </select>
      </div>

      <div v-if="dateRange==='custom'">
        <label class="block text-sm font-semibold text-gray-200 mb-1">From</label>
        <input type="date" v-model="fromDate" class="w-full px-4 py-3 rounded-lg text-[#001F3F] font-semibold focus:ring-4 focus:ring-[#FF2D2D]/60 outline-none" />
      </div>

      <div v-if="dateRange==='custom'">
        <label class="block text-sm font-semibold text-gray-200 mb-1">To</label>
        <input type="date" v-model="toDate" class="w-full px-4 py-3 rounded-lg text-[#001F3F] font-semibold focus:ring-4 focus:ring-[#FF2D2D]/60 outline-none" />
      </div>
    </div>

    <!-- Main Table Container -->
    <div class="flex-1 overflow-auto">
      <div v-if="filteredAppointments.length === 0" class="text-gray-100 text-xl text-center py-20 font-medium">
        🚫 No appointments found for this range.
      </div>

      <div v-else class="w-full h-full overflow-x-auto overflow-y-auto">
        <table class="w-full text-sm md:text-base border-collapse">
          <thead class="bg-gradient-to-r from-[#FF2D2D] to-[#FF6B6B] text-white uppercase sticky top-0 z-10">
            <tr>
              <th v-for="header in headers" :key="header.value" class="px-5 py-4 text-left font-semibold tracking-wider">
                {{ header.text }}
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(appt, idx) in filteredAppointments"
              :key="appt.id"
              :class="idx % 2 === 0 ? 'bg-white/10 hover:bg-white/20' : 'bg-white/5 hover:bg-white/15'"
              class="transition-all text-white"
            >
              <td class="px-5 py-4">{{ appt.date_of_booking }}</td>
              <td class="px-5 py-4">{{ appt.time_of_booking }}</td>
              <td class="px-5 py-4">
                <span
                  :class="{
                    'bg-green-600/80 text-white px-3 py-1 rounded-full text-sm font-semibold': appt.status === 'approved',
                    'bg-red-600/80 text-white px-3 py-1 rounded-full text-sm font-semibold': appt.status === 'declined',
                    'bg-gray-400/70 text-white px-3 py-1 rounded-full text-sm font-semibold': !appt.status || appt.status === 'pending'
                  }"
                >
                  {{ appt.status ?? 'Pending' }}
                </span>
              </td>
              <td class="px-5 py-4 text-center">{{ appt.id }}</td>
              <td class="px-5 py-4">{{ appt.name }}</td>
              <td class="px-5 py-4">{{ appt.email || 'Walk-IN' }}</td>
              <td class="px-5 py-4">{{ appt.size_of_the_car }}</td>
              <td class="px-5 py-4">{{ appt.contact_no }}</td>
              <td class="px-5 py-4">{{ appt.slot_number }}</td>
              <td class="px-5 py-4">{{ appt.created_at }}</td>
              <td class="px-5 py-4 text-center">
                <img
                  :src="getPaymentProofSrc(appt)"
                  alt="Payment Proof"
                  class="h-16 w-16 object-cover rounded-lg border border-white/30 mx-auto cursor-pointer hover:scale-105 transition-all"
                  @click="openImagePreview(getPaymentProofSrc(appt))"
                  @error="handleImageError"
                />
              </td>
              <td class="px-5 py-4 text-center font-bold text-[#FFD700]">{{ appt.payment_amount ?? 'Walk-IN' }}</td>
              <td class="px-5 py-4 text-center flex justify-center gap-3">
                <button @click="approve(appt.id)" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white font-semibold shadow-md transition hover:scale-105">
                  ✅ Approve
                </button>
                <button @click="openDeclineModal(appt.id)" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-semibold shadow-md transition hover:scale-105">
                  ❌ Decline
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Decline Modal -->
    <div v-if="showDeclineModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex justify-center items-center z-50">
      <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 relative">
        <h2 class="text-2xl font-bold text-[#002B5C] mb-4">Decline Appointment</h2>
        <p class="text-gray-600 mb-3">Please provide a reason for declining:</p>
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
      <button @click="showImagePreview=false" class="absolute top-6 right-8 text-white text-3xl font-bold hover:text-red-400">✕</button>
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
