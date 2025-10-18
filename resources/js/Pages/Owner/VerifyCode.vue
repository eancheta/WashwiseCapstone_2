<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import TextLink from '@/components/TextLink.vue';

const page = usePage();
const email = (page.props as any).email ?? (page.props.flash as any)?.email ?? '';

const form = useForm({
  email: email,
  code: ''
});

function submit() {
  form.post(route('owner.verify.submit'));
}

function resend() {
  form.post(route('owner.verify.resend'), { preserveScroll: true });
}
</script>

<template>
  <Head title="Verify Email" />

  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">
    <!-- Top Info Bar -->
    <div class="w-full bg-white flex flex-wrap items-center justify-between px-4 sm:px-6 py-2 border-b border-gray-200 text-sm font-semibold">
      <div class="flex items-center gap-2">
        <img
          src="/images/washwiselogo2.png"
          alt="WashWise Logo"
          class="h-12 sm:h-14 w-auto mx-auto block"
          draggable="false"
        />
      </div>
      <div class="hidden sm:flex gap-6 items-center text-[#002B5C] text-xs sm:text-sm">
        <div class="flex items-center gap-1 sm:gap-2">
          <span>📞</span>
          <span class="font-bold">Call Us</span>
          <span class="font-normal">+012 345 6789</span>
        </div>
        <div class="flex items-center gap-1 sm:gap-2">
          <span>✉️</span>
          <span class="font-bold">Email Us</span>
          <span class="font-normal">info@example.com</span>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="w-full bg-[#182235] flex flex-wrap sm:flex-nowrap items-center px-4 sm:px-6 py-2 text-white font-semibold shadow z-10">
      <ul class="flex flex-1 gap-4 sm:gap-6 items-center flex-wrap sm:flex-nowrap">
        <TextLink :href="route('home')" class="text-white">Home</TextLink>
      </ul>
      <div class="flex gap-2 sm:gap-4 mt-2 sm:mt-0">
        <TextLink
          :href="route('login')"
          class="text-white font-semibold hover:text-[#FF2D2D] transition [text-decoration:none] text-sm sm:text-base"
        >
          Log in
        </TextLink>
        <TextLink
          :href="route('register')"
          class="px-3 sm:px-6 py-2 rounded-full border-2 font-semibold transition [text-decoration:none] text-sm sm:text-base"
          style="border-color:#FF2D2D; color:#FF2D2D;"
        >
          Register
        </TextLink>
      </div>
    </nav>

    <!-- Email Verification Form -->
    <div class="flex-grow flex items-center justify-center px-4 sm:px-6 py-6">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md overflow-auto"
      >
        <h2 class="text-2xl font-bold text-center text-[#182235]">Verify Your Email</h2>
        <p class="text-center text-gray-500 text-sm sm:text-base mb-4">
          Please enter the verification code we sent to
          <span class="font-semibold text-[#002B5C]">{{ form.email }}</span>.
        </p>

        <div class="grid gap-3 w-full">
          <!-- Verification Code -->
          <div class="grid gap-1 w-full">
            <Label for="email_code" class="text-gray-700 font-medium text-sm sm:text-base">Verification Code</Label>
            <Input
              id="email_code"
              type="text"
              v-model="form.code"
              required
              placeholder="Enter your code"
              class="w-full text-black"
            />
            <div v-if="form.errors.code" class="text-red-600 text-sm mt-1">
              {{ form.errors.code }}
            </div>
          </div>

          <!-- Submit Button -->
          <Button
            type="submit"
            class="mt-2 w-full text-sm sm:text-base"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
          >
            Verify Email
          </Button>
        </div>

        <!-- Link to Resend Code -->
        <div class="text-center text-xs sm:text-sm mt-2">
          <span class="text-gray-600">Didn't receive the code?</span>
          <button
            type="button"
            @click="resend"
            class="text-sm font-semibold ml-1 transition hover:text-[#FF2D2D] [text-decoration:none]"
            style="color:#002366; background:none; border:none; cursor:pointer;"
          >
            Resend Code
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
