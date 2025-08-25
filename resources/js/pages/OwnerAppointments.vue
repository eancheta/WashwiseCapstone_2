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

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Customer Appointments</h1>

    <div v-if="props.appointments.length === 0" class="text-gray-500">
      No appointments found.
    </div>

    <table v-else class="w-full border border-gray-200 shadow rounded-lg overflow-hidden">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Car Size</th>
          <th class="px-4 py-2">Contact</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Time</th>
          <th class="px-4 py-2">Date</th>
          <th class="px-4 py-2">Slot</th>
          <th class="px-4 py-2">Created</th>
          <th class="px-4 py-2">Payment Proof</th>
          <th class="px-4 py-2">Payment Amount</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="appt in props.appointments" :key="appt.id" class="border-t">
          <td class="px-4 py-2">{{ appt.id }}</td>
          <td class="px-4 py-2">{{ appt.name }}</td>
          <td class="px-4 py-2">{{ appt.size_of_the_car }}</td>
          <td class="px-4 py-2">{{ appt.contact_no }}</td>
          <td class="px-4 py-2">{{ appt.email || 'N/A' }}</td>
          <td class="px-4 py-2">{{ appt.time_of_booking }}</td>
          <td class="px-4 py-2">{{ appt.date_of_booking }}</td>
          <td class="px-4 py-2">{{ appt.slot_number }}</td>
          <td class="px-4 py-2">{{ appt.created_at }}</td>
          <td class="px-4 py-2">
            <img
              v-if="appt.payment_proof"
              :src="appt.payment_proof"
              alt="Payment Proof"
              class="h-16 w-16 object-cover rounded"
              @error="handleImageError"
            />
            <span v-else class="text-gray-600">No proof</span>
          </td>
          <td class="px-4 py-2">{{ appt.payment_amount ?? 'N/A' }}</td>
          <td class="px-4 py-2">
            <span v-if="appt.status === 'approved'" class="text-green-600 font-semibold">Approved</span>
            <span v-else-if="appt.status === 'declined'" class="text-red-600 font-semibold">Declined</span>
            <span v-else class="text-gray-600">Pending</span>
          </td>
          <td class="px-4 py-2 space-x-2">
            <button @click="approve(appt.id)" class="px-3 py-1 bg-green-600 text-white rounded">Approve</button>
            <button @click="decline(appt.id)" class="px-3 py-1 bg-red-600 text-white rounded">Decline</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
