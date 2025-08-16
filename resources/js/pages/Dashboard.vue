<script setup lang="ts">
import { Inertia } from '@inertiajs/inertia'

interface Shop {
    id: number
    name: string
    address: string
}

interface AuthUser {
    id: number
    name: string
}

interface Props {
    shops: Shop[]
    auth: {
        user: AuthUser | null
    }
}

const props = defineProps<Props>()

// Navigate to Booking page
const goToBooking = (shopId: number) => {
    console.log('Authenticated user:', props.auth.user)
    console.log('Shops available:', props.shops)
    console.log('Navigating to booking for shop ID:', shopId)
    if (!props.auth.user) {
        console.warn('User not authenticated, redirecting to login')
        Inertia.get('/login', {}, {
            onSuccess: () => console.log('Redirected to login page'),
            onError: (errors) => console.error('Login redirect error:', errors)
        })
        return
    }
    if (!props.shops.find(shop => shop.id === shopId)) {
        console.error('Shop ID not found in shops list:', shopId)
        alert('Invalid shop ID. Please select a valid shop.')
        return
    }
    Inertia.get(`/customer/book/${shopId}`, {}, {
        onStart: () => console.log('Starting navigation to /customer/book/' + shopId),
        onSuccess: () => console.log('Successfully navigated to booking page'),
        onError: (errors) => {
            console.error('Navigation error:', errors)
            alert('Failed to navigate to booking page: ' + JSON.stringify(errors))
        },
        onFinish: () => console.log('Navigation attempt finished')
    })
}
</script>

<template>
<div class="container py-5">
    <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
        {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
        {{ $page.props.flash.error }}
    </div>

    <h1 class="text-2xl font-bold mb-4">
        Welcome, {{ props.auth.user?.name || 'Customer' }}
    </h1>

    <h2 class="text-xl font-semibold mb-2">Available Car Wash Shops</h2>

    <div v-if="props.shops.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="shop in props.shops" :key="shop.id" class="p-4 border rounded">
            <h3 class="text-lg font-bold">{{ shop.name }}</h3>
            <p>{{ shop.address }}</p>
            <button @click.prevent="goToBooking(shop.id)"
                class="bg-blue-500 text-white px-4 py-2 mt-2 rounded hover:bg-blue-600">
                Book Now
            </button>
        </div>
    </div>

    <p v-else class="text-gray-500">No approved shops available at the moment.</p>
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
