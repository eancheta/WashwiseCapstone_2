<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import TextLink from '@/components/TextLink.vue';

const form = useForm({ code: '' });
const page = usePage();
const email = (page.props.flash as { email?: string })?.email ?? '';

function submit() {
  form.post(route('verify.code'), { preserveScroll: true });
}
</script>

<template>
  <Head title="Verify Email" />

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

    <!-- Verification Form -->
    <div class="flex-grow flex items-center justify-center px-4 py-8 sm:py-16">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-md mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-[#182235]">Verify Your Email</h2>
        <p class="text-center text-gray-500 mb-4 text-sm sm:text-base">
          Please enter the verification code we sent to wait atleast 5 minutes to 10 minutes see the code
          <span class="font-semibold text-[#002B5C]">{{ email }}</span>.
        </p>

        <div class="grid gap-3 w-full">
          <!-- Verification Code -->
          <div class="grid gap-1 w-full">
            <Label for="email_code" class="text-gray-700 font-medium">Verification Code</Label>
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
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
            :disabled="form.processing"
          >
            <span v-if="!form.processing">Verify Email</span>
            <span v-else>Processing...</span>
          </Button>
        </div>
      </form>
    </div>
  </div>
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6">

    <!-- About -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">About WashWise</h2>
      <p class="text-sm leading-relaxed">
        WashWise is your trusted platform for booking car wash services — connecting customers and business owners for a smoother, cleaner experience every day.
      </p>
    </div>
    <!-- Contact Info -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">Contact Us</h2>
      <ul class="text-sm space-y-1">
        <li>📞 +63 992 759 4673</li>
        <li>✉️ washwise00@gmail.com</li>
      </ul>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="border-t border-gray-700 mt-8 pt-4 text-center text-xs text-gray-400">
    © {{ new Date().getFullYear() }} WashWise. All rights reserved.
    <br class="sm:hidden" /> Developed by <span class="text-[#FF2D2D] font-semibold">Washwise Team.</span>
  </div>
</footer>
</template>
