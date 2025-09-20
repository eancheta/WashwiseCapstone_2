<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col py-8 px-4">
      <div class="flex flex-col items-center mb-10">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mb-2" draggable="false" />
        <span class="text-xl font-bold text-[#182235]">Admin</span>
      </div>
      <nav class="flex flex-col gap-2">
        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'users' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'users'"
        >
          <span>👤</span> Users
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'owners' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'owners'"
        >
          <span>🏢</span> Owners
        </button>
      </nav>
      <form @submit.prevent="logout" class="mt-auto pt-10">
        <button class="w-full bg-[#FF2D2D] hover:bg-[#d72626] text-white px-4 py-2 rounded-lg font-semibold shadow transition">
          Logout
        </button>
      </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <h1 class="text-2xl font-bold mb-6 text-[#182235]">
        {{
          activeTab === 'users'
            ? 'Users'
            : activeTab === 'bookings'
            ? 'Bookings'
            : 'Owners'
        }}
      </h1>

      <!-- Users Table -->
      <div v-if="activeTab === 'users'" class="overflow-x-auto">
        <table class="min-w-full bg-white border border-[#182235] shadow-sm rounded">
          <thead>
            <tr class="bg-[#f8fafc] text-left">
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">ID</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Name</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Email</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Verified At</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Verification Code</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Created</th>
              <th class="py-2 px-4 border-b border-[#182235] text-[#182235] font-bold">Updated</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="hover:bg-[#e0e7ff] transition">
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.id }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.name }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.email }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.email_verified_at || 'Not verified' }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.verification_code || 'N/A' }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.created_at }}</td>
              <td class="py-2 px-4 border-b border-[#182235] text-[#182235]">{{ user.updated_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Owners Table -->
      <div v-if="activeTab === 'owners'" class="overflow-x-auto">
        <h1 class="text-2xl font-bold mb-4 text-[#182235]">Car Wash Owners</h1>
        <table class="min-w-[1000px] w-full bg-white border border-gray-200 shadow-sm rounded-lg text-xs sm:text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">ID</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Name</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Email</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Verified At</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Password</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">District</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Address</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Photo1</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Photo2</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Photo3</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Status</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Remember Token</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Created At</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Updated At</th>
              <th class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-[#182235] whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="text-[#182235] font-medium bg-white">
            <tr v-for="owner in owners" :key="owner.id" class="border-t hover:bg-[#F8FAFC] transition">
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ owner.id }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.name }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.email }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.email_verified_at }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.password }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.district }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3">{{ owner.address }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                <img
                  v-if="owner.photo1"
                  :src="`/storage/${owner.photo1}`"
                  alt="Photo 1"
                  class="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded border border-gray-300 mx-auto"
                />
                <span v-else class="text-gray-400 italic">No photo</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                <img
                  v-if="owner.photo2"
                  :src="`/storage/${owner.photo2}`"
                  alt="Photo 2"
                  class="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded border border-gray-300 mx-auto"
                />
                <span v-else class="text-gray-400 italic">No photo</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                <img
                  v-if="owner.photo3"
                  :src="`/storage/${owner.photo3}`"
                  alt="Photo 3"
                  class="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded border border-gray-300 mx-auto"
                />
                <span v-else class="text-gray-400 italic">No photo</span>
              </td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ owner.status }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ owner.remember_token }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ owner.created_at }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ owner.updated_at }}</td>
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center space-x-2">
                <a href="#" @click.prevent="approve(owner.id)" class="text-green-600 hover:underline">Approve</a>
                <button @click="decline(owner.id)" class="px-3 py-1 sm:px-4 sm:py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition text-xs sm:text-sm">Decline</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

interface Owner {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  password: string;
  district: string;
  address: string;
  photo1: string | null;
  photo2: string | null;
  photo3: string | null;
  status: string;
  remember_token: string | null;
  created_at: string;
  updated_at: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  verification_code: string | null;
  created_at: string;
  updated_at: string;
}

const { owners, users } = defineProps<{
  owners: Owner[];
  users: User[];
}>();

const activeTab = ref('users');

const approve = (id: number) => {
  router.post(`/admin/owners/${id}/approve`);
};

const decline = (id: number) => {
  router.post(`/admin/owners/${id}/decline`);
};

const logout = () => {
  router.post('/logout');
};
</script>
