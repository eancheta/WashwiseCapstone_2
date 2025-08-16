<template>
  <div class="container py-5">
    <div v-if="shop">
      <h1>Book at {{ shop.name }}</h1>

      <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
        {{ $page.props.flash.error }}
      </div>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label>Your Name</label>
          <input type="text" v-model="form.name" class="form-control" required />
          <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
        </div>

        <div class="mb-3">
          <label>Size of the Car</label>
          <select v-model="form.size_of_the_car" class="form-control" required>
            <option value="">Select</option>
            <option value="HatchBack">HatchBack</option>
            <option value="Sedan">Sedan</option>
            <option value="MPV">MPV</option>
            <option value="SUV">SUV</option>
            <option value="Pickup">Pickup</option>
            <option value="Van">Van</option>
            <option value="Motorcycle">Motorcycle</option>
          </select>
          <div v-if="form.errors.size_of_the_car" class="text-danger">{{ form.errors.size_of_the_car }}</div>
        </div>

        <div class="mb-3">
          <label>Contact Number</label>
          <input type="text" v-model="form.contact_no" class="form-control" required />
          <div v-if="form.errors.contact_no" class="text-danger">{{ form.errors.contact_no }}</div>
        </div>

        <div class="mb-3">
          <label>Time of Booking</label>
          <input type="time" v-model="form.time_of_booking" class="form-control" required />
          <div v-if="form.errors.time_of_booking" class="text-danger">{{ form.errors.time_of_booking }}</div>
        </div>

        <div class="mb-3">
          <label>Date of Booking</label>
          <input type="date" v-model="form.date_of_booking" class="form-control" required />
          <div v-if="form.errors.date_of_booking" class="text-danger">{{ form.errors.date_of_booking }}</div>
        </div>

        <div class="mb-3">
          <label>Slot Number</label>
          <input type="number" v-model.number="form.slot_number" min="1" class="form-control" required />
          <div v-if="form.errors.slot_number" class="text-danger">{{ form.errors.slot_number }}</div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="form.processing">
          <span v-if="form.processing">Booking...</span>
          <span v-else>Book Now</span>
        </button>
      </form>
    </div>
    <div v-else class="text-danger">
      Shop not found. Please select a valid shop.
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

interface Shop {
  id: number
  name: string
}

const props = defineProps<{
  shop: Shop | null
  errors: { [key: string]: string }
}>()

const form = useForm({
  name: '',
  size_of_the_car: '',
  contact_no: '',
  time_of_booking: '',
  date_of_booking: '',
  slot_number: 1
})

const submit = () => {
  console.log('Submitting booking for shop ID:', props.shop?.id)
  form.post(`/customer/book/${props.shop?.id}`, {
    onSuccess: () => console.log('Booking submitted successfully'),
    onError: (errors) => console.error('Booking submission error:', errors)
  })
}
</script>

<style>
.alert-success {
  background-color: #d4edda;
  color: #155724;
  padding: 1rem;
  border-radius: 0.25rem;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 0.25rem;
}
.text-danger {
  color: #721c24;
}
.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
}
.btn-primary {
  background-color: #FF2D2D;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
}
</style>
