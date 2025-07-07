<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => router.visit('/emailvcode'),
    });
};
</script>

<template>

  <Head title="Register" />

  <!-- Page Wrapper -->
  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">

    <!-- Top Bar (Matches Welcome/Login) -->
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

    <!-- Register Form -->
    <div class="flex-grow flex items-center justify-center bg-[#F8FAFC]">
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 w-full max-w-sm mx-auto bg-white p-8 rounded-lg shadow-md"
      >
        <h2 class="text-2xl font-bold text-center text-[#182235]">Create an account</h2>
        <p class="text-center text-gray-500 mb-4">Enter your details below to create your account</p>

        <div class="grid gap-3 w-full">
          <!-- Name -->
          <div class="grid gap-1 w-full">
            <Label for="name" class="text-gray-700 font-medium">Name</Label>
            <Input
              id="name"
              type="text"
              required
              autofocus
              :tabindex="1"
              autocomplete="name"
              v-model="form.name"
              placeholder="Full name"
              class="w-full text-black"
            />
            <InputError :message="form.errors.name" />
          </div>

          <!-- Email -->
          <div class="grid gap-1 w-full">
            <Label for="email" class="text-gray-700 font-medium">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              :tabindex="2"
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
              :tabindex="3"
              autocomplete="new-password"
              v-model="form.password"
              placeholder="Password"
              class="w-full text-black"
            />
            <InputError :message="form.errors.password" />
          </div>

          <!-- Confirm Password -->
          <div class="grid gap-1 w-full">
            <Label for="password_confirmation" class="text-gray-700 font-medium">Confirm password</Label>
            <Input
              id="password_confirmation"
              type="password"
              required
              :tabindex="4"
              autocomplete="new-password"
              v-model="form.password_confirmation"
              placeholder="Confirm password"
              class="w-full text-black"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>

          <!-- Submit Button -->
          <Button
            type="submit"
            class="mt-2 w-full"
            style="background:#FF2D2D; color:#fff; font-weight:600; padding-top:0.5rem; padding-bottom:0.5rem; border-radius:0.5rem;"
            :tabindex="5"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Create account
          </Button>
        </div>

        <!-- Link to Log in -->
        <div class="text-center text-sm mt-2">
          <span class="text-gray-600">Already have an account?</span>
          <TextLink
            :href="route('login')"
            class="text-base font-semibold ml-1 transition hover:text-[#FF2D2D] [text-decoration:none] "
            style="color:#002B5C;"

            :tabindex="6"
          >
        Log in
          </TextLink>
        </div>
      </form>
    </div>
  </div>
=======
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
>>>>>>> ba453e732e0e0c65919244a30f2e899012f2132c
</template>
