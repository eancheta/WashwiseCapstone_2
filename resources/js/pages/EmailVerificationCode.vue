<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';

const goToDashboard = () => {
  router.visit('/dashboard');
};
</script>

<template>
  <Head title="Verify Email" />

  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">
    <!-- Top Bar -->
    <div class="w-full bg-white flex flex-wrap items-center justify-between px-8 py-2 border-b border-gray-200 text-sm font-semibold">
      <div class="flex items-center gap-2">
        <img
          src="/images/washwiselogo2.png"
          alt="WashWise Logo"
          class="h-14 w-auto mx-auto block"
          draggable="false"
        />
      </div>
      <div class="flex gap-8 items-center text-[#002B5C]">
        <div class="flex items-center gap-2">
          <span>📞</span> Call Us <span class="font-normal">+012 345 6789</span>
        </div>
        <div class="flex items-center gap-2">
          <span>✉️</span> Email Us <span class="font-normal">info@example.com</span>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="w-full bg-[#182235] flex items-center px-8 py-2 text-white font-semibold shadow z-10">
      <ul class="flex gap-8 items-center flex-1">
        <TextLink :href="route('home')" class="text-[#FF2D2D]">Home</TextLink>
        <li><TextLink :href="'#'">About</TextLink></li>
        <li><TextLink :href="'#'">Service</TextLink></li>
      </ul>
      <div class="flex items-center gap-4 ml-8">
        <TextLink
          :href="route('login')"
          class="text-white font-semibold hover:text-[#FF2D2D] transition [text-decoration:none]"
          style="font-size: 1.0rem; line-height: 1;"
        >
          Log in
        </TextLink>
        <TextLink
          :href="route('register')"
          class="px-6 py-2 rounded-full border-2 font-semibold transition [text-decoration:none]"
          style="border-color:#FF2D2D; color:#FF2D2D; font-size: 1.0rem; line-height: 1;"
        >
          Register
        </TextLink>
      </div>
    </nav>

    <!-- Email Verification Form -->
    <div class="flex-grow flex items-center justify-center bg-[#F8FAFC]">
      <form
        @submit.prevent="goToDashboard"
        class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl font-bold text-center text-[#182235]">Verify Your Email</h2>
        <p class="text-center text-gray-500 mb-4">
          Please enter the verification code we sent to your email.
        </p>

        <div class="grid gap-3 w-full">
          <!-- Verification Code -->
          <div class="grid gap-1 w-full">
            <Label for="email_code" class="text-gray-700 font-medium">Verification Code</Label>
            <Input
              id="email_code"
              type="text"
              required
              placeholder="Enter your code"
              class="w-full text-black"
            />
          </div>

          <!-- Submit Button -->
          <Button
            type="submit"
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
          >
            <LoaderCircle v-if="false" class="h-4 w-4 animate-spin" />
            <span>Verify Email</span>
          </Button>
        </div>

        <!-- Link to Resend Code -->
        <div class="text-center text-sm mt-2">
          <span class="text-gray-600">Didn't receive the code?</span>
          <TextLink
            :href="'#'"
            class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D] [text-decoration:none]"
            style="color:#002366;"
          >
            Resend Code
          </TextLink>
        </div>
      </form>
    </div>
  </div>
</template>
