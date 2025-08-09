<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const email = (page.props as any).email ?? (page.props.flash as any)?.email ?? ''

const form = useForm({
  email: email,
  code: ''
})

function submit() {
  form.post(route('owner.verify.submit'))
}

function resend() {

  form.post(route('owner.verify.resend'), { preserveScroll: true })
}
</script>

<template>
  <div class="max-w-md mx-auto p-6">
    <h2>Verify Your Email</h2>
    <p>Code sent to <strong>{{ form.email }}</strong></p>
    <form @submit.prevent="submit">
      <input v-model="form.code" placeholder="6-digit code" />
      <div v-if="form.errors.code" class="text-red-500">{{ form.errors.code }}</div>
      <button type="submit">Verify</button>
    </form>

    <form @submit.prevent="resend">
      <button type="submit">Resend Code</button>
    </form>
  </div>
</template>
