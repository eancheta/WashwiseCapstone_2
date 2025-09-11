<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

// Access page props
const page = usePage();
const props = page.props as any;

// Safely access user
const user = props.auth?.user ?? { name: '', email: '', email_verified_at: null };

// Form prefilled with current user values
const form = useForm({
  name: user.name ?? '',
  email: user.email ?? '',
});

// Submit patch to update profile
const submit = () => {
  form.patch(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      console.log('Profile updated successfully');
    },
    onError: (errors: any) => {
      console.error('Profile update errors:', errors);
    },
  });
};

// Email verification status
const isMustVerify = Boolean(props.mustVerifyEmail);
const status = props.status as string | undefined;
const isEmailUnverified = !user.email_verified_at; // Check for null instead of placeholder date
</script>

<template>
  <div class="app-layout">
    <Head title="Profile settings" />
    <div class="settings-layout flex flex-col space-y-6">
      <div>
        <h2 class="text-xl font-semibold custom-heading">Profile information</h2>
        <p class="text-sm text-neutral-600">Update your name and email address</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-2">
          <label for="name" class="text-sm font-medium">Name</label>
          <input
            id="name"
            class="mt-1 block w-full border border-gray-300 rounded-md p-2"
            v-model="form.name"
            required
            autocomplete="name"
            placeholder="Full name"
          />
          <p v-if="form.errors.name" class="error mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

        <div class="grid gap-2">
          <label for="email" class="text-sm font-medium">Email address</label>
          <input
            id="email"
            type="email"
            class="mt-1 block w-full border border-gray-300 rounded-md p-2"
            v-model="form.email"
            required
            autocomplete="username"
            placeholder="Email address"
          />
          <p v-if="form.errors.email" class="error mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>

        <div v-if="isMustVerify && isEmailUnverified" class="-mt-4 text-sm text-neutral-500">
          <p>
            Your email address is unverified.
            <Link
              href="/email/verification-notification"
              method="post"
              as="button"
              class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current dark:decoration-neutral-500 ml-1"
            >
              Click here to resend the verification email.
            </Link>
          </p>

          <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>

        <div class="flex items-center gap-4">
          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
            :disabled="form.processing"
          >
            Save
          </button>

          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
          </Transition>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap');

.custom-heading {
  font-family: 'Montserrat', sans-serif;
  letter-spacing: 0.5px;
}
</style>
