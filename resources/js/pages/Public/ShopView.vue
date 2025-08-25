<template>
  <div class="shop-view">
    <h1 class="text-2xl font-bold text-center text-[#002B5C] mb-6">{{ shop.name }}</h1>

    <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
      {{ $page.props.flash.success }}
    </div>

    <div class="space-y-4">
      <p><strong>Address:</strong> {{ shop.address }}</p>
      <p><strong>District:</strong> {{ shop.district }}</p>

      <div v-if="shop.logo">
        <img
          :src="`${backendBaseUrl}/storage/${shop.logo}`"
          alt="Shop Logo"
          style="max-width: 200px;"
          @error="handleImageError($event, `${backendBaseUrl}/images/default-carwash.png`)"
        />
      </div>

      <div v-if="shop.description">
        <h3>Description</h3>
        <p>{{ shop.description }}</p>
      </div>

      <div v-if="shop.services_offered">
        <h3>Services Offered</h3>
        <p>{{ shop.services_offered }}</p>
      </div>

      <div v-if="shop.qr_code">
        <h3>QR Code</h3>
        <img
          :src="`${backendBaseUrl}/storage/${shop.qr_code}`"
          alt="QR Code"
          style="max-width: 200px;"
          @error="handleImageError($event, `${backendBaseUrl}/images/default-qr.png`)"
        />
      </div>

      <router-link
        :to="{ name: 'customer.book.payment', params: { shop: shop.id } }"
        class="inline-block bg-[#002B5C] text-white py-2 px-4 rounded-lg font-semibold hover:opacity-90 transition"
      >
        Book Now
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'

interface Shop {
  id: number
  name: string
  address: string
  district: number
  logo?: string | null
  description?: string | null
  services_offered?: string | null
  qr_code?: string | null
}

const props = defineProps<{
  shop: Shop
}>()

const backendBaseUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost/Washwise'

function handleImageError(event: Event, fallbackSrc: string) {
  const target = event.target as HTMLImageElement | null
  if (target && props.shop) { // Reference props.shop to suppress ESLint warning
    target.src = fallbackSrc
  }
}
</script>

<style scoped>
.shop-view {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}
</style>
