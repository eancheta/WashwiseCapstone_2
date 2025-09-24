<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { watch, ref, computed } from 'vue';
import axios from 'axios';

// Interfaces (ensure Props is defined before use)
interface Shop {
    id: number;
    name: string;
    address: string;
    district?: string;
    logo?: string;
    description?: string;
    services_offered?: string;
    qr_code?: string;
}

interface Booking {
    id?: number;
    date_of_booking: string;
    time_of_booking: string;
    slot_number: number;
    name?: string;
}

interface GroupedBooking {
    id?: number; // Optional ID for uniqueness
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
        if (!groups[key]) {
            groups[key] = {
                id: booking.id,
                time: booking.time_of_booking,
                slot: booking.slot_number,
                takenBy: '',
            };
        }
        if (booking.name) {
            if (groups[key].takenBy === '') {
                groups[key].takenBy = booking.name;
            } else if (groups[key].takenBy !== booking.name) {
                groups[key].takenBy = 'Multiple';
            }
        } else if (groups[key].takenBy === '') {
            groups[key].takenBy = 'Unknown';
        }
    });

    const groupArray = Object.values(groups);
    groupArray.sort((a, b) => {
        const timeA = new Date(`2000-01-01T${a.time}:00`).getTime();
        const timeB = new Date(`2000-01-01T${b.time}:00`).getTime();
        return timeA - timeB;
    });

    return groupArray;
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

    const dates: string[] = [];
    const d = new Date(date);
    dates.push(d.toISOString().split('T')[0]);
    dates.push(new Date(d.getTime() - 24 * 60 * 60 * 1000).toISOString().split('T')[0]);
    dates.push(new Date(d.getTime() + 24 * 60 * 60 * 1000).toISOString().split('T')[0]);

    const allFetched: Booking[] = [];

    for (const fetchDate of [...new Set(dates)]) {
        try {
            const response = await axios.get(`/shops/${props.shop.id}/availability?date=${fetchDate}`);
            allFetched.push(...response.data.bookings);
        } catch (error) {
            console.warn(`No bookings found for ${fetchDate}:`, error);
        }
    }

    const uniqueBookings = allFetched.filter((booking, index, self) =>
        index === self.findIndex(b =>
            b.date_of_booking === booking.date_of_booking &&
            b.time_of_booking === booking.time_of_booking &&
            b.slot_number === booking.slot_number
        )
    );

    bookings.value = uniqueBookings;
};

const returnToDashboard = () => {
    if (form.isDirty) {
        if (!confirm('You have unsaved changes. Are you sure you want to leave?')) {
            return;
        }
    }
    router.visit(route('dashboard'));
};

const submit = () => {
    shopError.value = '';
    overlapError.value = '';

    if (!props.shop?.id) {
        shopError.value = 'Invalid shop ID. Please select a valid shop.';
        return;
    }

    if (hasOverlap.value) {
        overlapError.value = 'This slot is already booked within the next 3 hours. Please choose a different time or slot.';
        return;
    }

    form.post(`/customer/book/${props.shop.id}/payment`, {
        onSuccess: () => console.log('Booking submitted successfully, redirected to payment'),
        onError: (errors) => {
            console.error('Booking submission errors:', errors);
            if (Object.keys(errors).length > 0) {
                form.errors = { ...form.errors, ...errors };
                if (errors.time_of_booking) {
                    overlapError.value = errors.time_of_booking;
                }
            } else {
                overlapError.value = 'An unexpected error occurred. Please try again.';
            }
        },
    });
};

// --- START: New Image Functions from Dashboard.vue ---

// Checks if the URL is a full URL. If not, it uses the default image.
function getLogoSrc(shop: Shop) {
    if (!shop.logo) {
        return '/images/default-carwash.png';
    }
    // Check if the URL is absolute (starts with http or https)
    if (shop.logo.startsWith('http')) {
        return shop.logo;
    }
    // Fallback for local storage (assumes a symbolic link)
    return `/storage/${shop.logo}`;
}

// Gets the QR code URL.
function getQrCodeSrc(shop: Shop) {
    if (!shop.qr_code) {
        return '';
    }
    // Check if the URL is absolute (starts with http or https)
    if (shop.qr_code.startsWith('http')) {
        return shop.qr_code;
    }
    // Fallback for local storage (assumes a symbolic link)
    return `/storage/${shop.qr_code}`;
}

// Handles broken logos by showing a default image.
function handleImgError(e: Event) {
    const target = e.target as HTMLImageElement | null
    if (target) target.src = '/logos/default-carwash.png'
}

// --- END: New Image Functions from Dashboard.vue ---


// Watchers (place after fetchBookings definition)
watch(() => form.date_of_booking, (newDate) => {
    if (newDate) {
        fetchBookings(newDate);
    }
});

watch(() => props.shop, (newShop) => {
    console.log('Shop updated:', newShop);
    if (newShop?.id && form.date_of_booking) {
        fetchBookings(form.date_of_booking);
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-10 px-4 flex justify-center">
        <div v-if="shop" class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Form Section -->
            <div class="space-y-5">
                <h1 class="text-2xl font-bold text-center text-[#002B5C] mb-6"> Book at {{ shop.name }} </h1>
                <!-- Success & Error Alerts -->
                <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm"> {{ $page.props.flash.success }} </div>
                <div v-if="$page.props.flash?.error" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm"> {{ $page.props.flash.error }} </div>
                <div v-if="shopError" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm"> {{ shopError }} </div>
                <div v-if="overlapError" class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm"> {{ overlapError }} </div>
                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Your Name</label>
                        <input type="text" v-model="form.name" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Email Address</label>
                        <input type="email" v-model="form.email" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                    <!-- Size of the Car -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Size of the Car</label>
                        <select v-model="form.size_of_the_car" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required>
                            <option value="">Select</option>
                            <option value="HatchBack">HatchBack</option>
                            <option value="Sedan">Sedan</option>
                            <option value="MPV">MPV</option>
                            <option value="SUV">SUV</option>
                            <option value="Pickup">Pickup</option>
                            <option value="Van">Van</option>
                            <option value="Motorcycle">Motorcycle</option>
                        </select>
                        <div v-if="form.errors.size_of_the_car" class="text-red-600 text-sm mt-1">{{ form.errors.size_of_the_car }}</div>
                    </div>
                    <!-- Contact -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Contact Number</label>
                        <input type="text" v-model="form.contact_no" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.contact_no" class="text-red-600 text-sm mt-1">{{ form.errors.contact_no }}</div>
                    </div>
                    <!-- Time -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Time of Booking</label>
                        <input type="time" v-model="form.time_of_booking" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.time_of_booking" class="text-red-600 text-sm mt-1">{{ form.errors.time_of_booking }}</div>
                    </div>
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Date of Booking</label>
                        <input type="date" v-model="form.date_of_booking" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.date_of_booking" class="text-red-600 text-sm mt-1">{{ form.errors.date_of_booking }}</div>
                    </div>
                    <!-- Slot -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Slot Number</label>
                        <input type="number" v-model="form.slot_number" min="1" max="4" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#002B5C] focus:outline-none" required />
                        <div v-if="form.errors.slot_number" class="text-red-600 text-sm mt-1">{{ form.errors.slot_number }}</div>
                    </div>
                    <!-- Taken Bookings Table (View Only) -->
                    <div v-if="form.date_of_booking" class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Taken Slots on {{ formattedDate }} (View Only)</h3>
                        <div class="overflow-x-auto">
                            <table v-if="takenBookings.length > 0" class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Slot</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="group in takenBookings" :key="`${group.time}-${group.slot}-${group.id || 0}`" class="bg-red-50 border-l-4 border-red-400 hover:bg-red-100">
                                        <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ group.time }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">#{{ group.slot }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="text-red-600 font-semibold">Taken</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p v-else class="text-sm text-gray-500 italic">No taken slots on this date yet.</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Bookings are sorted by time. Each lasts 3 hours; avoid overlaps.</p>
                    </div>
                    <!-- Return to Dashboard Button -->
                    <button type="button" @click="returnToDashboard" class="w-full bg-gray-500 text-white py-2 rounded-lg font-semibold hover:bg-gray-600 transition">
                        Return to Dashboard
                    </button>
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#FF2D2D] text-white py-2 rounded-lg font-semibold hover:bg-red-700 transition disabled:opacity-50" :disabled="form.processing || hasOverlap">
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Pay Now</span>
                    </button>
                </form>
            </div>
            <!-- Shop Details Section -->
            <div class="space-y-5">
                <h2 class="text-xl font-semibold text-[#002B5C] mb-4">Shop Details</h2>
                <div class="flex justify-center">
                    <img :src="getLogoSrc(shop)" @error="handleImgError" alt="Shop Logo" class="h-24 w-24 object-contain rounded-lg border border-gray-200" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Name</h3>
                    <p class="text-gray-900">{{ shop.name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Address</h3>
                    <p class="text-gray-900">{{ shop.address }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">District</h3>
                    <p class="text-gray-900">{{ shop.district || 'Not specified' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Description</h3>
                    <p class="text-gray-900">{{ shop.description || 'No description available' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Services Offered</h3>
                    <p class="text-gray-900">{{ shop.services_offered || 'No services listed' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">QR Code</h3>
                    <img :src="getQrCodeSrc(shop)" @error="handleImgError" alt="Shop QR Code" class="h-32 w-32 object-contain rounded-lg border border-gray-200" />
                </div>
            </div>
        </div>
        <div v-else class="text-red-600 font-medium">Shop not found. Please select a valid shop.</div>
    </div>
</template>
