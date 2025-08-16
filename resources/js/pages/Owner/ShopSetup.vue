<template>
  <div class="container py-5">
    <h1 class="text-2xl font-bold mb-4">{{ pageTitle }}</h1>

    <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
      {{ $page.props.flash.error }}
    </div>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="flex flex-col gap-4">
      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">Name</label>
        <input type="text" v-model="form.name" class="form-control" required />
        <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">Address</label>
        <input type="text" v-model="form.address" class="form-control" required />
        <div v-if="form.errors.address" class="text-danger">{{ form.errors.address }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">District</label>
        <input type="number" v-model.number="form.district" min="1" max="6" class="form-control" required />
        <div v-if="form.errors.district" class="text-danger">{{ form.errors.district }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">Logo</label>
        <input type="file" @change="handleLogoChange" accept="image/*" class="form-control" />
        <div v-if="form.errors.logo" class="text-danger">{{ form.errors.logo }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">Description</label>
        <textarea v-model="form.description" class="form-control"></textarea>
        <div v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">Services Offered</label>
        <textarea v-model="form.services_offered" class="form-control"></textarea>
        <div v-if="form.errors.services_offered" class="text-danger">{{ form.errors.services_offered }}</div>
      </div>

      <div class="grid gap-1">
        <label class="text-gray-700 font-medium">QR Code</label>
        <input type="file" @change="handleQrCodeChange" accept="image/*" class="form-control" />
        <div v-if="form.errors.qr_code" class="text-danger">{{ form.errors.qr_code }}</div>
      </div>

      <button type="submit" class="btn btn-primary mt-4" :disabled="form.processing">
        <span v-if="form.processing">Creating...</span>
        <span v-else>Create Shop</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
  pageTitle: string
}>()

const form = useForm({
  name: '',
  address: '',
  district: null as number | null,
  logo: null as File | null,
  description: '',
  services_offered: '',
  qr_code: null as File | null,
})

const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.logo = target.files[0]
  }
}

const handleQrCodeChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.qr_code = target.files[0]
  }
}

const submit = () => {
  form.post(route('owner.shop.store'), {
    onSuccess: () => console.log('Shop created successfully'),
    onError: (errors) => console.error('Shop creation error:', errors),
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
  font-size: 0.875rem;
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
