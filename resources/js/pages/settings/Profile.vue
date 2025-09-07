<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import DeleteUser from '@/components/DeleteUser.vue';
import { type BreadcrumbItem, type User } from '@/types';

// Access page props
const page = usePage();
const props = page.props as any;

// Safely access user
const user = (props.auth?.user as User) ?? { name: '', email: '' };

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: '/settings/profile',
  },
];

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
const isEmailUnverified = !user.email_verified_at || user.email_verified_at === '1970-01-01 00:00:00';
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Profile settings" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Profile information" description="Update your name and email address" />

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
              id="name"
              class="mt-1 block w-full"
              v-model="form.name"
              required
              autocomplete="name"
              placeholder="Full name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input
              id="email"
              type="email"
              class="mt-1 block w-full"
              v-model="form.email"
              required
              autocomplete="username"
              placeholder="Email address"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div v-if="isMustVerify && isEmailUnverified" class="-mt-4 text-sm text-muted-foreground">
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
            <Button :disabled="form.processing">Save</Button>

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

        <DeleteUser />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap');

.custom-heading {
  font-family: 'Montserrat', sans-serif;
  letter-spacing: 0.5px;
}
</style>
