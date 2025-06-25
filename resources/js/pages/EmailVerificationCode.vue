<script setup lang="ts">

import { Head, router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const goToDashboard = () => {
  router.visit('/dashboard');
};
</script>

<template>
  <Head title="Verify Email" />

    <div class="p-6">
      <h1 class="text-2xl font-bold">Email Verification</h1>
      <p class="text-muted-foreground mt-2">
        Please check your email and enter the verification code we sent you.
      </p>


      <div class="mt-6 grid gap-2 max-w-sm">
        <Label for="email_code">Verification Code</Label>
        <Input id="email_code" type="text" placeholder="Enter your code" />
      </div>


      <button
        @click="goToDashboard"
        class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Go to Dashboard
      </button>
    </div>

</template>
