<template>
  <Head title="Owner Appointments" />

  <div class="flex min-h-screen bg-gradient-to-br from-white via-blue-50 to-[#002B5C] py-4 sm:py-8 px-3 sm:px-6 flex-col items-center overflow-x-hidden">
    <div class="w-full max-w-[95%] xl:max-w-[1600px] p-4 sm:p-8 bg-white rounded-2xl shadow-xl border border-gray-200">

        <div v-if="$page.props.flash.success" class="mb-6 w-full">
  <div
    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg font-semibold shadow-sm text-center"
  >
    {{ $page.props.flash.success }}
  </div>
</div>

<div v-if="$page.props.flash.error" class="mb-6 w-full">
  <div
    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg font-semibold shadow-sm text-center"
  >
    {{ $page.props.flash.error }}
  </div>
</div>

      <!-- Return Button + Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <button
          @click="goBack"
          class="px-5 py-2 bg-[#002B5C] text-white font-semibold rounded-lg shadow-md hover:bg-[#FF2D2D] transition"
        >
          ← Return to Dashboard
        </button>

        <div class="text-center sm:flex-1">
          <h1 class="text-2xl sm:text-4xl font-extrabold text-[#002B5C] mb-1 sm:mb-2">
            Customer Appointments:
          </h1>
          <p class="text-gray-500 text-sm sm:text-base">
            Manage and monitor all customer bookings efficiently
          </p>
        </div>

        <div class="hidden sm:block w-[150px]"></div>
      </div>

      <!-- Filter Card -->
      <div class="mb-8 p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner grid grid-cols-1 sm:grid-cols-3 gap-4 border border-gray-200">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Date Range</label>
          <select
            v-model="dateRange"
            class="w-full px-4 py-2 sm:py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
          >
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="custom">Custom</option>
          </select>
        </div>

        <div v-if="dateRange === 'custom'">
          <label class="block text-sm font-semibold text-gray-600 mb-1">From</label>
          <input
            type="date"
            v-model="fromDate"
            class="w-full px-4 py-2 sm:py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
          />
        </div>

        <div v-if="dateRange === 'custom'">
          <label class="block text-sm font-semibold text-gray-600 mb-1">To</label>
          <input
            type="date"
            v-model="toDate"
            class="w-full px-4 py-2 sm:py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
          />
        </div>

        <div>
  <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
  <select
    v-model="statusFilter"
    class="w-full px-4 py-2 sm:py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none"
  >
    <option value="All">All</option>
    <option value="Approved">Approved</option>
    <option value="Declined">Declined</option>
    <option value="Pending">Pending</option>
  </select>
</div>
      </div>

      <!-- Appointment Table -->
      <div v-if="filteredAppointments.length === 0" class="text-gray-500 text-lg text-center py-12">
        No appointments found.
      </div>

      <div v-else class="overflow-x-auto rounded-xl border border-gray-200 shadow-md w-full">
        <table class="min-w-full text-[13px] sm:text-sm text-left border-collapse">
          <thead class="bg-[#002B5C] text-white">
            <tr>
              <th class="px-3 sm:px-4 py-3">No.</th>
              <th class="px-3 sm:px-4 py-3">Date</th>
              <th class="px-3 sm:px-4 py-3">Time</th>
              <th class="px-3 sm:px-4 py-3">Status</th>
              <th class="px-3 sm:px-4 py-3 text-center">ID</th>
              <th class="px-3 sm:px-4 py-3">Name</th>
              <th class="px-3 sm:px-4 py-3 hidden md:table-cell">Email</th>
              <th class="px-3 sm:px-4 py-3 hidden md:table-cell">Car Size</th>
              <th class="px-3 sm:px-4 py-3 hidden md:table-cell">Services</th>
              <th class="px-3 sm:px-4 py-3 hidden md:table-cell">Contact</th>
              <th class="px-3 sm:px-4 py-3 hidden md:table-cell">Slot</th>
              <th class="px-3 sm:px-4 py-3 hidden lg:table-cell">Created</th>
              <th class="px-3 sm:px-4 py-3 text-center hidden md:table-cell">Payment Proof</th>
              <th class="px-3 sm:px-4 py-3 text-center hidden sm:table-cell">Amount</th>
              <th class="px-3 sm:px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>

          <tbody class="text-[#182235] bg-white">
            <tr
              v-for="(appt, idx) in filteredAppointments"
              :key="appt.id"
              :class="idx % 2 === 0 ? 'bg-gray-50 hover:bg-gray-100' : 'hover:bg-gray-50'"
              class="transition"
            >
              <td class="px-3 sm:px-4 py-3 text-center">{{ idx + 1 }}</td>
              <td class="px-3 sm:px-4 py-3">{{ appt.date_of_booking }}</td>
              <td class="px-3 sm:px-4 py-3">{{ appt.time_of_booking }}</td>
              <td class="px-3 sm:px-4 py-3">
                <span v-if="appt.status === 'Approved'" class="text-green-600 font-semibold">Approved</span>
                <span v-else-if="appt.status === 'Declined'" class="text-red-600 font-semibold">Declined</span>
                <span v-else class="text-gray-600 font-semibold">Pending</span>
              </td>
              <td class="px-3 sm:px-4 py-3 text-center">{{ appt.id }}</td>
              <td class="px-3 sm:px-4 py-3">{{ appt.name }}</td>
              <td class="px-3 sm:px-4 py-3 hidden md:table-cell">{{ appt.email || 'Walk_IN' }}</td>
              <td class="px-3 sm:px-4 py-3 hidden md:table-cell">{{ appt.size_of_the_car }}</td>
              <td class="px-3 sm:px-4 py-3 hidden md:table-cell">{{ appt.services_offered || 'N/A' }}</td>
              <td class="px-3 sm:px-4 py-3 hidden md:table-cell">{{ appt.contact_no }}</td>
              <td class="px-3 sm:px-4 py-3 hidden md:table-cell">{{ appt.slot_number }}</td>
              <td class="px-3 sm:px-4 py-3 hidden lg:table-cell">{{ appt.created_at }}</td>

              <td class="px-3 sm:px-4 py-3 text-center hidden md:table-cell">
                <img
                  :src="getPaymentProofSrc(appt)"
                  alt="Payment Proof"
                  class="h-12 sm:h-16 w-12 sm:w-16 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
                  @click="openImagePreview(getPaymentProofSrc(appt))"
                  @error="handleImageError"
                />
              </td>

              <td class="px-3 sm:px-4 py-3 text-center font-bold text-[#FF2D2D] hidden sm:table-cell">
                {{ appt.payment_amount ?? 'Walk_IN' }}
              </td>

              <td class="px-3 sm:px-4 py-3 text-center flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
  <!-- Approve Button -->
  <button
    @click="approve(appt.id)"
    :disabled="appt.status === 'Approved' || appt.status === 'Declined'"
    class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg font-semibold text-xs sm:text-sm transition text-white
           bg-green-600 hover:bg-green-700
           disabled:bg-gray-400 disabled:cursor-not-allowed"
  >
    Approve
  </button>

  <!-- Decline Button -->
  <button
    @click="openDeclineModal(appt.id)"
    :disabled="appt.status === 'Approved' || appt.status === 'Declined'"
    class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg font-semibold text-xs sm:text-sm transition text-white
           bg-red-600 hover:bg-red-700
           disabled:bg-gray-400 disabled:cursor-not-allowed"
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
  <div
    v-if="showDeclineModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 px-4"
  >
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative">
      <h2 class="text-xl font-bold text-[#002B5C] mb-4">Decline Appointment</h2>
      <p class="text-gray-600 mb-3">
        Please write the reason for declining this appointment:
      </p>
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
  <div
    v-if="showImagePreview"
    class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
  >
    <img
      :src="previewSrc"
      alt="Preview"
      class="max-h-[80vh] rounded-xl shadow-2xl border-4 border-white"
    />
    <button
      @click="showImagePreview = false"
      class="absolute top-6 right-8 text-white text-3xl font-bold"
    >
      ✕
    </button>
  </div>
</template>

<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

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
  services_offered?: string | null;
}

const props = defineProps<{ appointments: Appointment[] }>();

// Approve Appointment
function approve(id: number) {
  router.post(`/owner/appointments/${id}/approve`);
}

// Decline Appointment Logic
const showDeclineModal = ref(false);
const declineReason = ref("");
const selectedId = ref<number | null>(null);

function openDeclineModal(id: number) {
  selectedId.value = id;
  showDeclineModal.value = true;
}

function submitDecline() {
  if (!selectedId.value) return;
  router.post(`/owner/appointments/${selectedId.value}/decline`, {
    reason: declineReason.value,
  });
  showDeclineModal.value = false;
  declineReason.value = "";
}

// Payment Proof Logic
function getPaymentProofSrc(appt: Appointment): string {
  if (!appt.payment_proof) return "/images/hero-carwash.jpg";
  if (appt.payment_proof.startsWith("http")) return appt.payment_proof;
  if (appt.payment_proof.startsWith("/storage/")) return appt.payment_proof;
  return `/storage/${appt.payment_proof}`;
}

function handleImageError(event: Event) {
  const target = event.target as HTMLImageElement;
  target.src = "/images/hero-carwash.jpg";
}

// Image Preview Modal
const showImagePreview = ref(false);
const previewSrc = ref("");

function openImagePreview(src: string) {
  previewSrc.value = src;
  showImagePreview.value = true;
}

// Filters
const dateRange = ref<string>("all");
const fromDate = ref<string>("");
const toDate = ref<string>("");
const statusFilter = ref<string>("all"); // ✅ new status filter

// Computed: Filter Appointments by Date + Status
const filteredAppointments = computed(() => {
  let data = [...props.appointments];
  const today = new Date();
  let start: Date | null = null;
  let end: Date | null = null;

  // --- DATE FILTER ---
  if (dateRange.value === "today") {
    start = new Date(today.toDateString());
    end = new Date(start);
    end.setDate(end.getDate() + 1);
  } else if (dateRange.value === "week") {
    const firstDay = today.getDate() - today.getDay();
    start = new Date(today.setDate(firstDay));
    end = new Date(today.setDate(firstDay + 7));
  } else if (dateRange.value === "month") {
    start = new Date(today.getFullYear(), today.getMonth(), 1);
    end = new Date(today.getFullYear(), today.getMonth() + 1, 1);
  } else if (dateRange.value === "custom" && fromDate.value && toDate.value) {
    start = new Date(fromDate.value);
    end = new Date(toDate.value);
    end.setDate(end.getDate() + 1);
  }

  if (start && end) {
    data = data.filter((a) => {
      const apptDate = new Date(a.date_of_booking);
      return apptDate >= start! && apptDate < end!;
    });
  }

  // --- STATUS FILTER ---
if (statusFilter.value !== "All") {
  data = data.filter((a) => {
    const status = a.status || "Pending";
    return status === statusFilter.value;
  });
}

  return data;
});

// Go Back to Dashboard
function goBack() {
  router.visit("/owner/dashboard");
}
</script>
