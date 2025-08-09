<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, Ref } from 'vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  district: '',
  address: '',
  photo1: null as File | null,
  photo2: null as File | null,
  photo3: null as File | null,
})

const preview1 = ref<string | null>(null)
const preview2 = ref<string | null>(null)
const preview3 = ref<string | null>(null)

function handleFileChange(e: Event, key: 'photo1' | 'photo2' | 'photo3', preview: Ref<string | null>) {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form[key] = target.files[0]
    preview.value = URL.createObjectURL(target.files[0])
  }
}

function handlePhoto1Change(e: Event) {
  handleFileChange(e, 'photo1', preview1)
}

function handlePhoto2Change(e: Event) {
  handleFileChange(e, 'photo2', preview2)
}

function handlePhoto3Change(e: Event) {
  handleFileChange(e, 'photo3', preview3)
}

function submit() {
  form.post(route('owner.register'), { forceFormData: true })
}
</script>

<template>
  <div class="p-6 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-4">Car Wash Owner Registration</h2>

    <form @submit.prevent="submit" enctype="multipart/form-data">

      <div class="mb-4">
        <label class="block mb-1 font-medium">Name</label>
        <input v-model="form.name" type="text" class="border rounded w-full p-2" />
        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Email</label>
        <input v-model="form.email" type="email" class="border rounded w-full p-2" />
        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Password</label>
        <input v-model="form.password" type="password" class="border rounded w-full p-2" />
        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" class="border rounded w-full p-2" />
        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">District</label>
        <select v-model="form.district" class="border rounded w-full p-2">
          <option value="">Select District</option>
          <option v-for="n in 6" :key="n" :value="n">{{ n }}</option>
        </select>
        <div v-if="form.errors.district" class="text-red-600 text-sm mt-1">{{ form.errors.district }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Address</label>
        <input v-model="form.address" type="text" class="border rounded w-full p-2" />
        <div v-if="form.errors.address" class="text-red-600 text-sm mt-1">{{ form.errors.address }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Photo 1</label>
        <input type="file" @change="handlePhoto1Change" class="border rounded w-full p-2" />
        <img v-if="preview1" :src="preview1" alt="Photo 1 preview" class="mt-2 w-32 h-32 object-cover border" />
        <div v-if="form.errors.photo1" class="text-red-600 text-sm mt-1">{{ form.errors.photo1 }}</div>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Photo 2</label>
        <input type="file" @change="handlePhoto2Change" class="border rounded w-full p-2" />
        <img v-if="preview2" :src="preview2" alt="Photo 2 preview" class="mt-2 w-32 h-32 object-cover border" />
        <div v-if="form.errors.photo2" class="text-red-600 text-sm mt-1">{{ form.errors.photo2 }}</div>
      </div>


      <div class="mb-4">
        <label class="block mb-1 font-medium">Photo 3</label>
        <input type="file" @change="handlePhoto3Change" class="border rounded w-full p-2" />
        <img v-if="preview3" :src="preview3" alt="Photo 3 preview" class="mt-2 w-32 h-32 object-cover border" />
        <div v-if="form.errors.photo3" class="text-red-600 text-sm mt-1">{{ form.errors.photo3 }}</div>
      </div>

      <button
        type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        :disabled="form.processing"
      >
        Register
      </button>
    </form>
  </div>
</template>
