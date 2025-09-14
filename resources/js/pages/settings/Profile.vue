<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'

function goBack() {
  router.visit('/dashboard') // for customer dashboard
}

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
const isEmailUnverified = !user.email_verified_at;
</script>

<template>
  <Head title="Profile Settings" />

  <div class="min-h-screen flex items-center justify-center bg-[#F8FAFC] py-10 px-4">
    <!-- Return button -->
    <div class="absolute top-6 left-6">
      <button
        @click="goBack"
        type="button"
        class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-[#002B5C] rounded-lg font-medium shadow hover:bg-[#FF2D2D] hover:text-white transition"
      >
        ⬅ Return
      </button>
    </div>

    <!-- Profile Card -->
    <div class="w-full max-w-lg bg-white shadow-xl rounded-2xl p-8 relative z-10">
      <h2 class="text-2xl font-extrabold text-[#002B5C] mb-2">Edit Profile</h2>
      <p class="text-sm text-gray-500 mb-6">Update your name and email address</p>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-bold text-[#182235] mb-1">Full Name</label>
          <input
            id="name"
            v-model="form.name"
            required
            autocomplete="name"
            placeholder="Enter your full name"
            class="w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-bold text-[#182235] mb-1">Email Address</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            autocomplete="username"
            placeholder="Enter your email"
            class="w-full border-2 border-gray-300 rounded-lg p-2 text-[#182235] focus:ring-2 focus:ring-[#FF2D2D] transition"
          />
          <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Email Verification -->
        <div v-if="isMustVerify && isEmailUnverified" class="text-sm text-gray-600">
          <p>
            Your email address is unverified.
            <Link
              href="/email/verification-notification"
              method="post"
              as="button"
              class="ml-1 text-[#FF2D2D] underline hover:opacity-80 transition"
            >
              Resend verification email
            </Link>
          </p>
          <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-semibold text-green-600">
            ✅ A new verification link has been sent to your email.
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
          <button
            type="submit"
            class="w-full bg-[#002B5C] text-white py-2 rounded-lg font-bold hover:bg-[#FF2D2D] transition"
            :disabled="form.processing"
          >
            Save Changes
          </button>

          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-green-600 font-medium">✅ Saved.</p>
          </Transition>
        </div>
      </form>
    </div>
  </div>
</template>
