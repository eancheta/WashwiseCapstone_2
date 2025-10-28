<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { LoaderCircle } from 'lucide-vue-next'
import { ref } from 'vue'

const showTermsModal = ref(false)
const agreedToTerms = ref(false)

function openTermsModal() {
  showTermsModal.value = true
}

function acceptTerms() {
  agreedToTerms.value = true
  showTermsModal.value = false
}


// Initialize form fields
type RegisterForm = {
  name: string
  email: string
  password: string
  password_confirmation: string
  picture_id: File | null
}

const form = useForm<RegisterForm>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  picture_id: null,
})



// Form submission handler
const submit = () => {
  if (!agreedToTerms.value) {
    alert('You must agree to the Terms and Conditions to register.')
    return
  }

  form.post(route('register'), {
    preserveScroll: true,
    onFinish: () => form.reset('password', 'password_confirmation'),
    onSuccess: () => router.visit('/emailvcode'),
    onError: () => console.error('Registration failed:', form.errors)
  })
}

function validateNameInput(e: Event) {
  const input = e.target as HTMLInputElement
  const cleaned = input.value.replace(/[^A-Za-z\s]/g, '')
  if (input.value !== cleaned) {
    alert('⚠️ Only letters and spaces are allowed in the name field.')
  }
  input.value = cleaned
  form.name = cleaned
}
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">
    <!-- Top Bar -->
    <div class="w-full bg-white flex flex-col sm:flex-row items-center justify-between px-4 sm:px-8 py-2 border-b border-gray-200 text-sm font-semibold gap-2 sm:gap-8">
      <div class="flex items-center gap-2">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mx-auto block" draggable="false" />
      </div>
      <div class="flex flex-col sm:flex-row gap-2 sm:gap-8 text-[#002B5C] items-center">
        <div class="flex items-center gap-2">
          <span>📞</span> Call Us <span class="font-normal">+639927594673/09927594673</span>
        </div>
        <div class="flex items-center gap-2">
          <span>✉️</span> Email Us <span class="font-normal">washwise00@gmail.com</span>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="w-full bg-[#182235] flex flex-col sm:flex-row sm:items-center px-4 sm:px-8 py-2 text-white font-semibold shadow z-10">
      <ul class="flex flex-col sm:flex-row gap-4 sm:gap-8 items-center flex-1">
        <TextLink :href="route('home')" class="text-white">Home</TextLink>
      </ul>

      <div class="flex items-center gap-4 mt-2 sm:mt-0">
        <TextLink
          :href="route('login')"
          class="text-white font-semibold hover:text-[#FF2D2D] transition text-sm sm:text-base"
        >
          Log in
        </TextLink>
        <TextLink
          :href="route('register')"
          class="px-4 sm:px-6 py-2 rounded-full border-2 font-semibold transition text-sm sm:text-base border-[#FF2D2D] text-white"
        >
          Register
        </TextLink>
      </div>
    </nav>

    <!-- Register Form -->
    <div class="flex-grow flex items-center justify-center px-4 py-8 sm:py-16">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-md mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-[#182235]">Create an account</h2>
        <p class="text-center text-gray-500 mb-4 text-sm sm:text-base">Enter your details below to create your account</p>

        <div class="grid gap-3 w-full">
<!-- Name -->
<div class="grid gap-1 w-full">
  <Label for="name" class="text-gray-700 font-medium flex items-center gap-1">
    Name <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Required)</span>
  </Label>
  <Input
    id="name"
    type="text"
    required
    autofocus
    autocomplete="name"
    v-model="form.name"
    placeholder="Full name"
    class="w-full text-black"
    @input="validateNameInput"
  />
  <InputError :message="form.errors.name" />
</div>

<!-- Email -->
<div class="grid gap-1 w-full">
  <Label for="email" class="text-gray-700 font-medium flex items-center gap-1">
    Email Address <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Required)</span>
  </Label>
  <Input
    id="email"
    type="email"
    required
    autocomplete="email"
    v-model="form.email"
    placeholder="email@example.com"
    class="w-full text-black"
  />
  <InputError :message="form.errors.email" />
</div>

<!-- Password -->
<div class="grid gap-1 w-full">
  <Label for="password" class="text-gray-700 font-medium flex items-center gap-1">
    Password <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Required)</span>
  </Label>
  <Input
    id="password"
    type="password"
    required
    autocomplete="new-password"
    v-model="form.password"
    placeholder="Password"
    class="w-full text-black"
  />
  <InputError :message="form.errors.password" />
</div>

<!-- Confirm Password -->
<div class="grid gap-1 w-full">
  <Label for="password_confirmation" class="text-gray-700 font-medium flex items-center gap-1">
    Confirm Password <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Required)</span>
  </Label>
  <Input
    id="password_confirmation"
    type="password"
    required
    autocomplete="new-password"
    v-model="form.password_confirmation"
    placeholder="Confirm password"
    class="w-full text-black"
  />
  <InputError :message="form.errors.password_confirmation" />
</div>

<!-- Terms Checkbox -->
<div class="flex items-start gap-2 mt-2">
  <input
    id="terms"
    type="checkbox"
    v-model="agreedToTerms"
    required
    class="mt-1 w-4 h-4 rounded border-gray-300 text-[#FF2D2D] focus:ring-[#FF2D2D]"
  />
  <label for="terms" class="text-gray-700 text-sm flex flex-wrap gap-1">
    <span>Terms and Conditions <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(Required)</span></span>
    — I agree to the
    <button type="button" @click="openTermsModal" class="text-[#FF2D2D] underline">
      Terms and Conditions
    </button>
  </label>
</div>

          <!-- Submit Button -->
          <Button
            type="submit"
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2 inline-block" />
            <span v-else>Create account</span>
          </Button>
        </div>

        <div class="text-center text-sm mt-2">
          <span class="text-gray-600">Already have an account?</span>
          <TextLink
            :href="route('login')"
            class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D]"
            style="color:#002366;"
          >
            Log in
          </TextLink>
        </div>
      </form>
    </div>
    <!-- Terms Modal -->
<div
  v-if="showTermsModal"
  class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
>
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 relative overflow-y-auto max-h-[80vh]">
    <h2 class="text-xl font-bold text-[#002B5C] mb-4">WashWise Terms and Conditions</h2>
    <div class="text-gray-700 space-y-2 text-sm">
      <p>By signing up for an account on WashWise, you agree to the following terms:</p>
      <ul class="list-disc ml-5 space-y-1">
        <li>You certify that the information you provide is accurate and truthful.</li>
        <li>You are responsible for keeping your account credentials confidential.</li>
        <li>You agree to use the system only for booking appointments with verified car wash establishments.</li>
        <li>Appointments must be managed responsibly; frequent cancellations or no-shows may result in limited access.</li>
        <li>WashWise serves as a booking platform and is not liable for service issues rendered by third-party car wash providers.</li>
        <li>Your personal data will be processed in accordance with our Privacy Policy and applicable data protection laws.</li>
        <li>Any misuse of the platform (e.g., fraud, impersonation, or system abuse) may lead to account suspension or termination.</li>
      </ul>
      <p class="mt-2">By clicking "I Agree", you confirm that you have read, understood, and accepted these terms.</p>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <button
        @click="showTermsModal = false"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400"
      >
        Cancel
      </button>
      <button
        @click="acceptTerms"
        class="px-5 py-2 bg-[#FF2D2D] text-white font-semibold rounded-lg hover:bg-[#E02626]"
      >
        I Agree
      </button>
    </div>
  </div>
</div>
  </div>
</template>
<style scoped>
.required::after {
  content: " *";
  color: #FF2D2D;
  font-weight: bold;
}
</style>
