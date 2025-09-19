<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

type Booking = {
  id: number
  name: string
  size_of_the_car: string
  contact_no: string
  time_of_booking: string // HH:mm
  date_of_booking: string // YYYY-MM-DD
  slot_number: number // 1..4
}

interface Shop {
  id: number
  name: string
  address: string
  district: number
}

const props = defineProps<{ shop: Shop }>()

const form = useForm({
  name: '',
  size_of_the_car: '',
  contact_no: '',
  date_of_booking: '',
  time_of_booking: '',
})

const bookings = ref<Booking[]>([])

async function loadAvailability() {
  if (!form.date_of_booking) {
    bookings.value = []
    return
  }
  const { data } = await axios.get(route('shops.bookings.availability', props.shop.id), {
    params: { date: form.date_of_booking },
  })
  bookings.value = (data?.bookings ?? []) as Booking[]
}

// update when date changes
watch(() => form.date_of_booking, () => { loadAvailability() })

const carSizes = [
  'HatchBack', 'Sedan', 'MPV', 'SUV', 'Pickup', 'Van', 'Motorcycle'
]

// compute existing ranges (start .. start+3h) per slot
const existingRanges = computed(() => {
  return bookings.value.map(b => {
    const start = new Date(`${b.date_of_booking}T${b.time_of_booking}`)
    const end = new Date(start.getTime() + 3 * 60 * 60 * 1000)
    return { slot: b.slot_number, start, end }
  })
})

// a time is bookable if at least one slot (1..4) has no overlap
function canBookAt(time: string): boolean {
  if (!form.date_of_booking || !time) return true
  const chosen = new Date(`${form.date_of_booking}T${time}`)
  for (let slot = 1; slot <= 4; slot++) {
    const overlaps = existingRanges.value.some(r =>
      r.slot === slot && chosen >= r.start && chosen < r.end
    )
    if (!overlaps) return true
  }
  return false
}

function submit() {
  if (!canBookAt(form.time_of_booking)) {
    alert('All 4 slots are occupied within 3 hours of that time. Please choose another time.')
    return
  }
  form.post(route('shops.bookings.store', props.shop.id), {
    onSuccess: () => {
      alert('Booking successful!')
      const keepDate = form.date_of_booking
      form.reset()
      form.date_of_booking = keepDate
      loadAvailability()
    }
  })
}

onMounted(() => {
  const today = new Date().toISOString().slice(0, 10)
  form.date_of_booking = today
})
</script>

<template>
  <div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold mb-1">Book at {{ shop.name }}</h1>
    <p class="text-gray-600 mb-6">{{ shop.address }} • District {{ shop.district }}</p>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-1">Your Name</label>
        <input v-model="form.name" class="border rounded w-full p-2" placeholder="Your name" />
        <div class="text-red-600 text-sm" v-if="form.errors.name">{{ form.errors.name }}</div>
      </div>

      <div>
        <label class="block mb-1">Car Size</label>
        <select v-model="form.size_of_the_car" class="border rounded w-full p-2">
          <option value="">Select</option>
          <option v-for="s in carSizes" :key="s" :value="s">{{ s }}</option>
        </select>
        <div class="text-red-600 text-sm" v-if="form.errors.size_of_the_car">{{ form.errors.size_of_the_car }}</div>
      </div>

      <div>
        <label class="block mb-1">Contact No.</label>
        <input v-model="form.contact_no" class="border rounded w-full p-2" placeholder="09xxxxxxxxx" />
        <div class="text-red-600 text-sm" v-if="form.errors.contact_no">{{ form.errors.contact_no }}</div>
      </div>

      <div>
        <label class="block mb-1">Date</label>
        <input type="date" v-model="form.date_of_booking" class="border rounded w-full p-2" />
        <div class="text-red-600 text-sm" v-if="form.errors.date_of_booking">{{ form.errors.date_of_booking }}</div>
      </div>

      <div>
        <label class="block mb-1">Time</label>
        <input type="time" v-model="form.time_of_booking" class="border rounded w-full p-2" step="1800" />
        <small v-if="form.date_of_booking && form.time_of_booking && !canBookAt(form.time_of_booking)" class="text-red-600">
          All slots are unavailable within 3 hours of that time. Pick another.
        </small>
        <div class="text-red-600 text-sm" v-if="form.errors.time_of_booking">{{ form.errors.time_of_booking }}</div>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Book</button>
    </form>

    <div class="mt-8">
      <h2 class="text-lg font-semibold mb-2">Existing bookings ({{ form.date_of_booking || '—' }})</h2>
      <ul class="space-y-2">
        <li v-for="b in bookings" :key="b.id" class="border rounded p-3">
          <div><strong>Slot #{{ b.slot_number }}</strong> — {{ b.time_of_booking }} ({{ b.size_of_the_car }})</div>
          <div class="text-sm text-gray-600">By {{ b.name }} • {{ b.contact_no }}</div>
        </li>
      </ul>
    </div>
  </div>
</template>
