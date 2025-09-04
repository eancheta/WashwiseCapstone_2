<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

interface Appointment {
  id: number;
  name: string;
  size_of_the_car: string;
  contact_no: string;
  email: string;
  time_of_booking: string;
  date_of_booking: string;
  slot_number: string;
  created_at: string;
  status?: string | null;
  payment_proof?: string | null;
  payment_amount?: number | null;
}

const props = defineProps<{
  appointments: Appointment[];
}>();

function approve(id: number) {
  router.post(`/owner/appointments/${id}/approve`);
}

function decline(id: number) {
  router.post(`/owner/appointments/${id}/decline`);
}

function handleImageError(event: Event) {
  const target = event.target as HTMLImageElement;
  target.outerHTML = '<span class="text-gray-600">No proof</span>';
}
</script>

<template>
  <Head title="Owner Appointments" />

  <div class="min-h-screen bg-[#F8FAFC] py-6 px-2 flex flex-col items-center relative overflow-hidden">
    <!-- Decorative background shapes -->
    <div class="absolute top-0 left-0 w-40 h-40 bg-[#FF2D2D] opacity-10 rounded-full blur-2xl z-0"></div>
    <div class="absolute bottom-0 right-0 w-56 h-56 bg-[#002B5C] opacity-10 rounded-full blur-2xl z-0"></div>
    <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-[#182235] opacity-10 rounded-full blur-xl z-0" style="transform: translate(-50%, -50%);"></div>

    <div class="w-full max-w-6xl bg-white rounded-2xl shadow-lg p-4 sm:p-8 relative z-10">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-center text-[#002B5C] mb-6 sm:mb-8 tracking-tight">Customer Appointments</h1>

      <div v-if="props.appointments.length === 0" class="text-gray-500 text-base sm:text-lg text-center py-8">
        No appointments found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-[700px] w-full border border-gray-200 rounded-lg overflow-hidden text-xs sm:text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">ID</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Name</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Car Size</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Contact</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Email</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Time</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Date</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Slot</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Created</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Payment Proof</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Amount</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Status</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="text-[#182235] font-medium">
            <tr v-for="appt in props.appointments" :key="appt.id" class="border-t hover:bg-[#F8FAFC] transition">
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ appt.id }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.name }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.size_of_the_car }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.contact_no }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.email || 'N/A' }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.time_of_booking }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.date_of_booking }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.slot_number }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ appt.created_at }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                <img
                  v-if="appt.payment_proof"
                  :src="appt.payment_proof"
                  alt="Payment Proof"
                  class="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded border border-gray-300 mx-auto"
                  @error="handleImageError"
                />
                <span v-else class="text-gray-400 italic">No proof</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-[#FF2D2D]">{{ appt.payment_amount ?? 'N/A' }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                <span v-if="appt.status === 'approved'" class="bg-green-100 text-green-700 px-2 py-1 rounded font-semibold">Approved</span>
                <span v-else-if="appt.status === 'declined'" class="bg-red-100 text-red-700 px-2 py-1 rounded font-semibold">Declined</span>
                <span v-else class="bg-gray-100 text-gray-600 px-2 py-1 rounded font-semibold">Pending</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center space-x-2">
                <button @click="approve(appt.id)" class="px-3 py-1 sm:px-4 sm:py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition text-xs sm:text-sm">Approve</button>
                <button @click="decline(appt.id)" class="px-3 py-1 sm:px-4 sm:py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition text-xs sm:text-sm">Decline</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
