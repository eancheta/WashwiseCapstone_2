<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: (errors) => console.error('Login errors:', errors),
    });
};
</script>

<template>
  <Head title="Login" />

  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">
    <!-- Top Bar -->
    <div class="w-full bg-white flex flex-col sm:flex-row items-center justify-between px-4 sm:px-8 py-2 border-b border-gray-200 text-sm font-semibold gap-2 sm:gap-8">
      <div class="flex items-center gap-2">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mx-auto block" draggable="false" />
      </div>
      <div class="flex flex-col sm:flex-row gap-2 sm:gap-8 text-[#002B5C] items-center">
        <div class="flex items-center gap-2">
          <span>📞</span> Call Us <span class="font-normal">+012 345 6789</span>
        </div>
        <div class="flex items-center gap-2">
          <span>✉️</span> Email Us <span class="font-normal">washwise00@gmail.com</span>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="w-full bg-[#182235] flex flex-col sm:flex-row sm:items-center px-4 sm:px-8 py-2 text-white font-semibold shadow z-10">
      <!-- Links -->
      <ul class="flex flex-col sm:flex-row gap-4 sm:gap-8 items-center flex-1">
        <TextLink :href="route('home')" class="text-white">Home</TextLink>
      </ul>

      <!-- Actions -->
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

    <!-- Login Form -->
    <div class="flex-grow flex items-center justify-center px-4 py-8 sm:py-16">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-md mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-[#182235]">Log in to your account</h2>
        <p class="text-center text-gray-500 mb-4 text-sm sm:text-base">Enter your credentials below to log in</p>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.error" class="alert-danger mb-4">
          {{ $page.props.flash.error }}
        </div>
        <div v-if="$page.props.flash.success" class="alert-success mb-4">
          {{ $page.props.flash.success }}
        </div>

        <div class="grid gap-3 w-full">
          <!-- Email -->
          <div class="grid gap-1 w-full">
            <Label for="email" class="text-gray-700 font-medium">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              autofocus
              autocomplete="email"
              v-model="form.email"
              placeholder="email@example.com"
              class="w-full text-black"
            />
            <InputError :message="form.errors.email" />
          </div>

          <!-- Password -->
          <div class="grid gap-1 w-full">
            <Label for="password" class="text-gray-700 font-medium">Password</Label>
            <Input
              id="password"
              type="password"
              required
              autocomplete="current-password"
              v-model="form.password"
              placeholder="Password"
              class="w-full text-black"
            />
            <InputError :message="form.errors.password" />
          </div>

          <!-- Remember Me -->
          <div class="flex items-center gap-2">
            <input
              id="remember"
              type="checkbox"
              v-model="form.remember"
              class="rounded border-gray-300 text-[#FF2D2D] shadow-sm focus:ring focus:ring-[#FF2D2D]"
            />
            <Label for="remember" class="text-sm text-gray-600">Remember me</Label>
          </div>

          <!-- Submit Button -->
          <Button
            type="submit"
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2 inline-block" />
            Log in
          </Button>
        </div>
<a :href="route('password.request', { type: 'user' })">Forgot your password?</a>

        <!-- Links -->
        <div class="text-center text-sm mt-2">
          <span class="text-gray-600">Don't have an account?</span>
          <TextLink
            :href="route('register')"
            class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D]"
            style="color:#002B5C;"
          >
            Register
          </TextLink>
        </div>
      </form>
    </div>
  </div>
</template>

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
</style>
