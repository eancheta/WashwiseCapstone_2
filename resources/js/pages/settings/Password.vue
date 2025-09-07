<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put('/settings/password')
}
</script>

<template>
  <Head title="Change Password" />

  <div class="max-w-lg mx-auto mt-10 bg-black shadow rounded-xl p-6">
    <h1 class="text-xl font-bold mb-4">Change Password</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Current Password</label>
        <input
          v-model="form.current_password"
          type="password"
          class="mt-1 block w-full border rounded-lg p-2"
        />
        <div v-if="form.errors.current_password" class="text-red-500 text-sm">
          {{ form.errors.current_password }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium">New Password</label>
        <input
          v-model="form.password"
          type="password"
          class="mt-1 block w-full border rounded-lg p-2"
        />
        <div v-if="form.errors.password" class="text-red-500 text-sm">
          {{ form.errors.password }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium">Confirm New Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full border rounded-lg p-2"
        />
      </div>

      <button
        type="submit"
        class="px-4 py-2 bg-[#002B5C] text-white rounded-lg hover:bg-[#FF2D2D]"
        :disabled="form.processing"
      >
        Save Changes
      </button>
    </form>
  </div>
</template>
