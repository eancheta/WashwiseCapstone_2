<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import AuthBase from '@/layouts/AuthLayout.vue';

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
    });
};
</script>

<template>
  <Head title="Log in" />

  <!-- Page Wrapper -->
  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">

    <!-- Top Bar (Matches Welcome Page) -->
    <!-- Top Info Bar -->
  <div class="w-full bg-white flex flex-wrap items-center justify-between px-8 py-2 border-b border-gray-200 text-sm font-semibold">
    <div class="flex items-center gap-2">
      <!-- Logo Image -->
      <img
        src="/images/logo2.png"
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
        <TextLink :href="route('welcome')" class="text-[#FF2D2D]">Home</TextLink>

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

    <!-- Login Form -->
    <div class="flex-grow flex items-center justify-center bg-[#F8FAFC]">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl font-bold text-center text-[#182235]">Log in to your account</h2>
        <p class="text-center text-gray-500 mb-4">Enter your email and password below</p>

        <div class="grid gap-3 w-full">
          <div class="grid gap-1 w-full">
            <Label for="email" class="text-gray-700 font-medium">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              autofocus
              :tabindex="1"
              autocomplete="email"
              v-model="form.email"
              placeholder="email@example.com"
              class="w-full text-black"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div class="grid gap-1 w-full">
            <Label for="password" class="text-gray-700 font-medium">Password</Label>
            <Input
              id="password"
              type="password"
              required
              :tabindex="2"
              autocomplete="current-password"
              v-model="form.password"
              placeholder="Password"
              class="w-full text-black"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div class="flex items-center">
            <Checkbox id="remember" v-model="form.remember" :tabindex="3" class="mr-2" />
            <Label for="remember" class="text-gray-700 font-medium">Remember me</Label>
          </div>

          <Button
            type="submit"
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
            :tabindex="4"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Log in
          </Button>
        </div>

        <div class="text-center text-sm mt-2">
          <span class="text-gray-600">Don't have an account?</span>
          <TextLink
            :href="route('register')"
            class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D] [text-decoration:none]"
            style="color:#002366;"
            :tabindex="5"
          >Sign up</TextLink>
        </div>
        <div v-if="canResetPassword" class="text-center mt-4">
          <TextLink
            :href="route('password.request')"
            class="text-base font-semibold underline transition"
            style="color:#002B5C;"
          >
            Forgot password?
          </TextLink>
        </div>
      </form>
    </div>
  </div>
=======
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
>>>>>>> ba453e732e0e0c65919244a30f2e899012f2132c
</template>
