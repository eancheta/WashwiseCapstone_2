<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({ email: '', password: '' })

function submit() {
  form.post(route('owner.login'))
}
</script>

<template>
  <div class="max-w-md mx-auto p-6">
    <h2>Owner Login</h2>
    <form @submit.prevent="submit">
      <input v-model="form.email" type="email" />
      <input v-model="form.password" type="password" />
      <button type="submit">Login</button>
    </form>
  </div>
</template>
