<script setup lang="ts">
const { shop } = defineProps<{
  shop: {
    id: number
    name: string
    address: string
    district: number
    logo?: string | null
    description?: string | null
    services_offered?: string | null
    qr_code?: string | null
  }
}>()
</script>

<template>
  <div class="shop-view">
    <h1>{{ shop.name }}</h1>
    <p><strong>Address:</strong> {{ shop.address }}</p>
    <p><strong>District:</strong> {{ shop.district }}</p>

    <div v-if="shop.logo">
      <img :src="`/storage/${shop.logo}`" alt="Shop Logo" style="max-width: 200px;" />
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
      <img :src="`/storage/${shop.qr_code}`" alt="QR Code" style="max-width: 200px;" />
    </div>
  </div>
</template>

<style scoped>
.shop-view {
  max-width: 600px;
  margin: 0 auto;
}
</style>
