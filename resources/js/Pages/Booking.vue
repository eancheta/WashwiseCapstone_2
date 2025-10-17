<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { watch, ref, computed } from 'vue';
import axios from 'axios';
const showQrPreview = ref(false);
const qrPreviewSrc = ref('');

function openQr(src: string) {
  if (!src) return;
  qrPreviewSrc.value = src;
  showQrPreview.value = true;
}

interface Shop {
  id: number;
  name: string;
  address: string;
  district?: string;
  description?: string;
  services_offered?: string;
  logo?: string;
  status?: string;
  qr_code?: string;
  qr_code2?: string;
  qr_code3?: string;
  qr_code4?: string;
  qr_code5?: string;
}

interface Booking {
    id?: number;
    date_of_booking: string;
    time_of_booking: string;
    slot_number: number;
    name?: string;
}

interface GroupedBooking {
    id?: number;
    time: string;
    slot: number;
    takenBy: string;
}

interface Props {
    shop: Shop | null;
    errors: Record<string, string>;
    bookings: Booking[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    size_of_the_car: '',
    contact_no: '',
    time_of_booking: '',
    date_of_booking: '',
    slot_number: 1,
    services_offered: '',
});

const shopError = ref<string>('');
const overlapError = ref<string>('');
const bookings = ref<Booking[]>(props.bookings || []);

const formattedDate = computed(() => {
    if (!form.date_of_booking) return '';
    return new Date(form.date_of_booking).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
});

const takenBookings = computed((): GroupedBooking[] => {
    if (!form.date_of_booking) return [];
    const selectedDate = form.date_of_booking;
    const filtered = bookings.value.filter(booking => booking.date_of_booking === selectedDate);
    const groups: { [key: string]: GroupedBooking } = {};
    filtered.forEach(booking => {
        const key = `${booking.time_of_booking}-${booking.slot_number}`;
        if (!groups[key]) groups[key] = { id: booking.id, time: booking.time_of_booking, slot: booking.slot_number, takenBy: '' };
        if (booking.name) {
            groups[key].takenBy = groups[key].takenBy && groups[key].takenBy !== booking.name ? 'Multiple' : booking.name;
        } else if (!groups[key].takenBy) groups[key].takenBy = 'Unknown';
    });
    return Object.values(groups).sort((a, b) => new Date(`2000-01-01T${a.time}:00`).getTime() - new Date(`2000-01-01T${b.time}:00`).getTime());
});

const hasOverlap = computed(() => {
    if (!form.date_of_booking || !form.time_of_booking || !form.slot_number) return false;
    const newStart = new Date(`${form.date_of_booking}T${form.time_of_booking}:00`);
    const newEnd = new Date(newStart.getTime() + 3 * 60 * 60 * 1000);
    return bookings.value.some(booking => {
        const start = new Date(`${booking.date_of_booking}T${booking.time_of_booking}:00`);
        const end = new Date(start.getTime() + 3 * 60 * 60 * 1000);
        return booking.slot_number === form.slot_number && start < newEnd && newStart < end;
    });
});

const fetchBookings = async (date: string) => {
    if (!date || !props.shop?.id) return;
    const dates = [
        new Date(date),
        new Date(date).setDate(new Date(date).getDate() - 1),
        new Date(date).setDate(new Date(date).getDate() + 1)
    ].map(d => new Date(d).toISOString().split('T')[0]);
    const allFetched: Booking[] = [];
    for (const fetchDate of [...new Set(dates)]) {
        try {
            const response = await axios.get(`/shops/${props.shop.id}/availability?date=${fetchDate}`);
            allFetched.push(...response.data.bookings);
        } catch {}
    }
    bookings.value = allFetched.filter((b, i, self) => i === self.findIndex(x => x.date_of_booking === b.date_of_booking && x.time_of_booking === b.time_of_booking && x.slot_number === b.slot_number));
};

const returnToDashboard = () => router.visit(route('dashboard'));

const submit = () => {
    shopError.value = '';
    overlapError.value = '';
    if (!props.shop?.id) { shopError.value = 'Invalid shop ID'; return; }
    if (hasOverlap.value) { overlapError.value = 'This slot is already booked'; return; }
    form.post(`/customer/book/${props.shop.id}/payment`);
};

const backendBaseUrl = window.location.origin;
function getLogoSrc(shop: Shop) { return shop.logo?.startsWith('http') ? shop.logo : `/storage/${shop.logo}` || '/images/default-carwash.png'; }
function handleImgError(e: Event) { const target = e.target as HTMLImageElement | null; if (target) target.src = `${backendBaseUrl}/images/default-carwash.png`; }

watch(() => form.date_of_booking, fetchBookings);
watch(() => props.shop, (s) => { if (s?.id && form.date_of_booking) fetchBookings(form.date_of_booking); });

function limitContactLength(event: Event) {
    const input = event.target as HTMLInputElement;
    input.value = input.value.replace(/\D/g, '').slice(0, 11);
    form.contact_no = input.value;
}

// Frontend restrictions for past date and time
const minDate = computed(() => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
});

const minTime = ref('00:00');

watch(
  () => form.date_of_booking,
  (newDate) => {
    if (!newDate) return;

    const today = new Date();
    const selectedDate = new Date(newDate);

    if (selectedDate.toDateString() === today.toDateString()) {
      const hours = today.getHours().toString().padStart(2, '0');
      const minutes = today.getMinutes().toString().padStart(2, '0');
      minTime.value = `${hours}:${minutes}`;

      // Auto-adjust if user already picked a past time
      if (form.time_of_booking < minTime.value) {
        form.time_of_booking = minTime.value;
      }
    } else {
      minTime.value = '00:00';
    }
  },
  { immediate: true }
);
</script>

<template>
    <!-- QR Image Preview Modal -->
<div
  v-if="showQrPreview"
  class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
>
  <img
    :src="qrPreviewSrc"
    alt="QR Code Preview"
    class="max-h-[80vh] rounded-xl shadow-2xl border-4 border-white"
  />
  <button
    @click="showQrPreview = false"
    class="absolute top-6 right-8 text-white text-3xl font-bold"
  >
    ✕
  </button>
</div>
  <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 flex justify-center">
    <div v-if="shop" class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Form Section -->
      <div class="space-y-4">
        <h1 class="text-xl sm:text-2xl font-bold text-center text-[#002B5C] mb-4">Book at {{ shop.name }}</h1>
        <div v-if="shopError" class="bg-red-100 text-red-800 p-2 rounded-lg text-sm">{{ shopError }}</div>
        <div v-if="overlapError" class="bg-red-100 text-red-800 p-2 rounded-lg text-sm">{{ overlapError }}</div>

        <form @submit.prevent="submit" class="space-y-4">
          <input type="text" v-model="form.name" placeholder="Your Name" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required/>
          <input type="email" v-model="form.email" placeholder="Email Address" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required/>
          <select v-model="form.size_of_the_car" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required>
            <option value="">Select Car Types</option>
            <option>HatchBack</option>
            <option>Sedan</option>
            <option>MPV</option>
            <option>SUV</option>
            <option>Pickup</option>
            <option>Van</option>
            <option>Motorcycle</option>
          </select>

          <input type="text" v-model="form.contact_no" placeholder="09XXXXXXXXX" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required maxlength="11" @input="limitContactLength"/>

          <!-- Date & Time with restrictions -->
          <input type="date" v-model="form.date_of_booking" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required :min="minDate"/>
          <input type="time" v-model="form.time_of_booking" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required :min="minTime"/>

          <input type="number" v-model="form.slot_number" min="1" max="4" class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]" required/>

<!-- ✅ Services Offered Section (Clickable Image Support) -->
<div>
  <label class="block text-sm font-semibold text-gray-700 mb-1">Services Offered</label>

  <div v-if="shop.services_offered">
    <!-- Check if it's an image URL -->
    <div v-if="shop.services_offered.match(/\.(jpg|jpeg|png|gif|webp)$/i) || shop.services_offered.startsWith('http')">
      <img
        :src="shop.services_offered.startsWith('http') ? shop.services_offered : `/storage/${shop.services_offered}`"
        alt="Services Offered"
        class="h-24 w-24 object-contain rounded border cursor-pointer hover:scale-105 transition"
        @click="openQr(shop.services_offered.startsWith('http') ? shop.services_offered : `/storage/${shop.services_offered}`)"
      />
    </div>

    <!-- Otherwise show text -->
    <p v-else class="text-gray-700 text-sm">{{ shop.services_offered }}</p>
  </div>

  <div v-else class="text-gray-500 italic text-sm">No services listed</div>

  <!-- User’s request input -->
  <label class="block text-sm font-semibold text-gray-700 mt-3">Services Requested</label>
  <textarea
    v-model="form.services_offered"
    class="w-full p-2 border rounded focus:ring-2 focus:ring-[#002B5C]"
    placeholder="Specify services you want"
    rows="3"
  ></textarea>

  <div v-if="form.errors.services_offered" class="text-red-600 text-sm mt-1">
    {{ form.errors.services_offered }}
  </div>
</div>

          <div v-if="form.date_of_booking" class="mt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Taken Slots on {{ formattedDate }} (View Only)</h3>
            <div class="overflow-x-auto">
              <table v-if="takenBookings.length" class="min-w-full bg-white border rounded">
                <thead class="bg-gray-50"><tr><th>Time</th><th>Slot</th><th>Status</th></tr></thead>
                <tbody>
                  <tr v-for="group in takenBookings" :key="`${group.time}-${group.slot}-${group.id || 0}`" class="bg-red-50 border-l-4 border-red-400">
                    <td class="px-2 py-1 text-sm">{{ group.time }}</td>
                    <td class="px-2 py-1 text-sm">#{{ group.slot }}</td>
                    <td class="px-2 py-1 text-sm text-red-600 font-semibold">Taken</td>
                  </tr>
                </tbody>
              </table>
              <p v-else class="text-xs text-gray-500 italic">No taken slots yet.</p>
            </div>
          </div>

          <button type="button" @click="returnToDashboard" class="w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600 transition">Return</button>
          <button type="submit" class="w-full bg-[#FF2D2D] text-white py-2 rounded hover:bg-red-700 transition" :disabled="form.processing || hasOverlap">Pay Now</button>
        </form>
      </div>

      <!-- Shop Details Section -->
      <div class="space-y-4">
        <h2 class="text-lg font-semibold text-[#002B5C mb-2">Shop Details</h2>
        <div class="flex justify-center">
          <img :src="getLogoSrc(shop)" alt="Car Wash Logo" class="h-20 w-20 object-contain mb-2" @error="handleImgError"/>
        </div>
        <p><span class="font-semibold">Name:</span> {{ shop.name }}</p>
        <p><span class="font-semibold">Address:</span> {{ shop.address }}</p>
        <p><span class="font-semibold">District:</span> {{ shop.district || 'Not specified' }}</p>
        <p><span class="font-semibold">Description:</span> {{ shop.description || 'No description' }}</p>

<div v-if="shop && (shop.qr_code || shop.qr_code2 || shop.qr_code3 || shop.qr_code4 || shop.qr_code5)" class="mt-4">
  <h3 class="text-sm font-semibold text-gray-700 mb-2">QR Codes</h3>
  <div class="overflow-x-auto">
    <table class="min-w-full border rounded">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-center">QR Code 1</th>
          <th class="px-3 py-2 text-center">QR Code 2</th>
          <th class="px-3 py-2 text-center">QR Code 3</th>
          <th class="px-3 py-2 text-center">QR Code 4</th>
          <th class="px-3 py-2 text-center">QR Code 5</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="px-3 py-3 text-center">
            <img
              v-if="shop.qr_code"
              :src="shop.qr_code"
              alt="QR Code 1"
              class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
              @click="openQr(shop.qr_code)"
            />
          </td>
          <td class="px-3 py-3 text-center">
            <img
              v-if="shop.qr_code2"
              :src="shop.qr_code2"
              alt="QR Code 2"
              class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
              @click="openQr(shop.qr_code2)"
            />
          </td>
          <td class="px-3 py-3 text-center">
            <img
              v-if="shop.qr_code3"
              :src="shop.qr_code3"
              alt="QR Code 3"
              class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
              @click="openQr(shop.qr_code3)"
            />
          </td>
          <td class="px-3 py-3 text-center">
            <img
              v-if="shop.qr_code4"
              :src="shop.qr_code4"
              alt="QR Code 4"
              class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
              @click="openQr(shop.qr_code4)"
            />
          </td>
          <td class="px-3 py-3 text-center">
            <img
              v-if="shop.qr_code5"
              :src="shop.qr_code5"
              alt="QR Code 5"
              class="h-12 w-12 object-cover rounded border mx-auto cursor-pointer hover:scale-105 transition"
              @click="openQr(shop.qr_code5)"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

      </div>
    </div>
    <div v-else class="text-red-600 font-medium text-center mt-10">Shop not found.</div>
  </div>
</template>
