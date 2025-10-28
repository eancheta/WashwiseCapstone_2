<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

function getPhotoSrc(photoPath: string | null): string {
  if (!photoPath) {
    return 'https://placehold.co/100x100?text=No+Photo'
  }
  if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
    return photoPath
  }
  return `/storage/${photoPath}`
}


interface Owner {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  password: string
  district: string
  address: string
  photo1: string | null
  photo2: string | null
  photo3: string | null
  status: string
  remember_token: string | null
  created_at: string
  updated_at: string
}

interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  verification_code: string | null
  created_at: string
  updated_at: string
  customer_status: string | null  // <-- add this
  status?: string | null
  reason: string | null
  picture_id?: string | null
}

const { owners, users } = defineProps<{
  owners: Owner[]
  users: User[]
}>()

const activeTab = ref('users')
const sidebarOpen = ref(false)

// Modal state for decline reason
const showDeclineModal = ref(false)
const declineReason = ref('')
const selectedOwnerId = ref<number | null>(null)

const approve = (id: number) => {
  if (!confirm('Are you sure you want to approve this owner?')) return
  router.post(route('owners.approve', { id }))
}



const logout = () => {
  router.post('/logout')
}

// Image preview modal state
const showImagePreview = ref(false)
const previewSrc = ref("")

function openImagePreview(src: string) {
  previewSrc.value = src
  showImagePreview.value = true
}

const showFeedbackModal = ref(false)
const selectedFeedback = ref<any[]>([])
const selectedOwnerName = ref('')

function openFeedbackModal(owner: any) {
  selectedOwnerName.value = owner.name
  selectedFeedback.value = owner.shop?.feedback || []
  showFeedbackModal.value = true
}

const showDeclineCustomerModal = ref(false)
const declineCustomerReason = ref('')
const selectedCustomerId = ref<number | null>(null)

const approveCustomer = (id: number) => {
  if (!confirm('Are you sure you want to approve this customer?')) return
  router.post(route('customers.approve', { id }))
}

const openDeclineCustomerModal = (id: number) => {
  selectedCustomerId.value = id
  showDeclineCustomerModal.value = true
}

const submitDeclineCustomer = () => {
  if (!selectedCustomerId.value || !declineCustomerReason.value.trim()) {
    alert('Please enter a reason before declining.')
    return
  }

  if (!confirm('Are you sure you want to delete this account?')) return

  router.post(route('customers.decline', { id: selectedCustomerId.value }), {
    reason: declineCustomerReason.value,
  })

  showDeclineCustomerModal.value = false
  declineCustomerReason.value = ''
}

const openDeclineModal = (id: number) => {
  selectedOwnerId.value = id
  showDeclineModal.value = true
}

// Submit decline with reason
const submitDecline = () => {
  if (!selectedOwnerId.value || !declineReason.value.trim()) {
    alert('Please enter a reason before declining.')
    return
  }

  if (!confirm('Are you sure you want to delete this account?')) return

  router.post(route('owners.decline', { id: selectedOwnerId.value }), {
    reason: declineReason.value,
  })

  showDeclineModal.value = false
  declineReason.value = ''
}
</script>

<template>
  <div class="min-h-screen flex flex-col sm:flex-row bg-gray-50">
    <!-- Mobile Header -->
    <div class="sm:hidden flex justify-between items-center bg-white shadow px-4 py-3">
      <div class="flex items-center gap-2">
        <img src="/images/washwiselogo2.png" alt="Logo" class="h-8 w-auto" />
        <h1 class="font-bold text-[#182235]">WashWise Admin</h1>
      </div>
      <button @click="sidebarOpen = !sidebarOpen" class="text-[#182235] text-xl">☰</button>
    </div>

    <!-- Sidebar -->
    <aside
      class="bg-white shadow-lg flex flex-col py-6 px-4 w-64 fixed sm:static inset-y-0 left-0 z-30 transform transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
    >
      <div class="flex flex-col items-center mb-10">
        <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-14 w-auto mb-2" draggable="false" />
        <span class="text-xl font-bold text-[#182235]">Admin</span>
      </div>

      <nav class="flex flex-col gap-2">
        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'users' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'users'; sidebarOpen = false"
        >
          <span>👤</span> Customer Account
        </button>

        <button
          class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold transition"
          :class="activeTab === 'owners' ? 'bg-[#FF2D2D] text-white' : 'text-[#182235] hover:bg-[#f8fafc]'"
          @click="activeTab = 'owners'; sidebarOpen = false"
        >
          <span>🏢</span> Owners Account
        </button>
      </nav>

      <form @submit.prevent="logout" class="mt-auto pt-10">
        <button
          class="w-full bg-[#FF2D2D] hover:bg-[#d72626] text-white px-4 py-2 rounded-lg font-semibold shadow transition"
        >
          Logout
        </button>
      </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4 sm:p-8 mt-14 sm:mt-0">
      <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-[#182235]">
        {{ activeTab === 'users' ? 'Customer Account' : 'Owner Account' }}
      </h1>

      <!-- Users Table -->
      <div v-if="activeTab === 'users'" class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-full text-sm sm:text-base">
          <thead>
            <tr class="bg-gray-100 text-left text-[#182235]">
        <th class="py-2 px-3">ID</th>
        <th class="py-2 px-3">Picture</th>
        <th class="py-2 px-3">Name</th>
        <th class="py-2 px-3">Email</th>
        <th class="py-2 px-3">Customer Status</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3">Created</th>
        <th class="py-2 px-3">Updated</th>
        <th class="py-2 px-3">Actions</th>

            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in users"
              :key="user.id"
              class="hover:bg-gray-50 border-t text-[#182235] transition"
            >
        <td class="py-2 px-3">{{ user.id }}</td>
            <td class="py-2 px-3">
<img
  v-if="user.picture_id"
  :src="user.picture_id"
  alt="User Picture"
  class="w-10 h-10 object-cover rounded-full border cursor-pointer hover:opacity-80 transition"
  @click="openImagePreview(user.picture_id)"
/>
      <span v-else class="text-gray-400 text-xs">No picture</span>
    </td>
        <td class="py-2 px-3">{{ user.name }}</td>
        <td class="py-2 px-3">{{ user.email }}</td>
        <td class="py-2 px-3">{{ user.customer_status || 'Pending' }}</td>
        <td class="py-2 px-3">{{ user.status || 'Not verified' }}</td>
        <td class="py-2 px-3">{{ user.created_at }}</td>
        <td class="py-2 px-3">{{ user.updated_at }}</td>
                  <!-- Action Column -->
<td class="py-2 px-3">
  <div class="flex flex-wrap gap-2 justify-center">
    <!-- If approved -->
    <template v-if="user.customer_status === 'Approved'">
      <button
        @click="openDeclineCustomerModal(user.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        Delete
      </button>
    </template>

    <!-- If not approved -->
    <template v-else>
      <button
        @click="approveCustomer(user.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 rounded-lg font-semibold text-xs sm:text-sm transition
               text-white bg-green-600 hover:bg-green-700"
      >
        Approve
      </button>

      <button
        @click="openDeclineCustomerModal(user.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        Decline
      </button>
    </template>
  </div>
</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Owners Table -->
      <div v-if="activeTab === 'owners'" class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-[1000px] w-full text-xs sm:text-sm">
          <thead class="bg-gray-100 text-[#182235]">
            <tr>
              <th
                v-for="header in ['ID','Name','Email','Verified At','Password','District','Address','Photo1','Photo2','Photo3','Status','Token','Created','Updated','Actions']"
                :key="header"
                class="px-2 sm:px-4 py-2 sm:py-3 font-semibold whitespace-nowrap"
              >
                {{ header }}
              </th>
            </tr>
          </thead>
          <tbody class="text-[#182235]">
            <tr v-for="owner in owners" :key="owner.id" class="border-t hover:bg-gray-50 transition">
              <td class="px-2 py-2 text-center">{{ owner.id }}</td>
              <td class="px-2 py-2">{{ owner.name }}</td>
              <td class="px-2 py-2">{{ owner.email }}</td>
              <td class="px-2 py-2">{{ owner.email_verified_at }}</td>
              <td class="px-2 py-2">{{ owner.password }}</td>
              <td class="px-2 py-2">{{ owner.district }}</td>
              <td class="px-2 py-2">{{ owner.address }}</td>

<td class="px-2 py-2 text-center">
  <img
    :src="getPhotoSrc(owner.photo1)"
    alt="Photo 1"
    class="h-10 w-10 sm:h-16 sm:w-16 object-cover rounded border mx-auto cursor-pointer hover:opacity-80 transition"
    @click="openImagePreview(getPhotoSrc(owner.photo1))"
  />
</td>
<td class="px-2 py-2 text-center">
  <img
    :src="getPhotoSrc(owner.photo2)"
    alt="Photo 2"
    class="h-10 w-10 sm:h-16 sm:w-16 object-cover rounded border mx-auto cursor-pointer hover:opacity-80 transition"
    @click="openImagePreview(getPhotoSrc(owner.photo2))"
  />
</td>
<td class="px-2 py-2 text-center">
  <img
    :src="getPhotoSrc(owner.photo3)"
    alt="Photo 3"
    class="h-10 w-10 sm:h-16 sm:w-16 object-cover rounded border mx-auto cursor-pointer hover:opacity-80 transition"
    @click="openImagePreview(getPhotoSrc(owner.photo3))"
  />
</td>

              <td class="px-2 py-2 text-center">{{ owner.status }}</td>
              <td class="px-2 py-2 text-center truncate max-w-[80px]">{{ owner.remember_token }}</td>
              <td class="px-2 py-2 text-center">{{ owner.created_at }}</td>
              <td class="px-2 py-2 text-center">{{ owner.updated_at }}</td>

<td class="px-2 py-2 text-center">
  <div class="flex flex-wrap gap-2 justify-center">
    <!-- If approved -->
    <template v-if="owner.status === 'Approved'">
      <button
        @click="openDeclineModal(owner.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        Delete
      </button>

      <button
        @click="openFeedbackModal(owner)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        View Feedback
      </button>
    </template>

    <!-- If not approved -->
    <template v-else>
      <button
        @click="approve(owner.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 rounded-lg font-semibold text-xs sm:text-sm transition
               text-white bg-green-600 hover:bg-green-700"
      >
        Approve
      </button>

      <button
        @click="openDeclineModal(owner.id)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        Decline
      </button>

      <button
        @click="openFeedbackModal(owner)"
        class="min-w-[70px] px-3 py-1 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-xs sm:text-sm transition"
      >
        View Feedback
      </button>
    </template>
  </div>
</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
<!-- Image Preview Modal -->
<div
  v-if="showImagePreview"
  class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
>
  <img
    :src="previewSrc"
    alt="Preview"
    class="max-h-[80vh] rounded-xl shadow-2xl border-4 border-white"
  />
  <button
    @click="showImagePreview = false"
    class="absolute top-6 right-8 text-white text-3xl font-bold"
  >
    ✕
  </button>
</div>
    <!-- Decline Modal -->
    <div v-if="showDeclineModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 px-4">
      <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative">
        <h2 class="text-xl font-bold text-[#002B5C] mb-4">Delete/Decline a account</h2>
        <p class="text-gray-600 mb-3">Please write the reason for Delete/Decline this owner:</p>
        <textarea
          v-model="declineReason"
          rows="4"
          placeholder="Type reason here..."
          class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-[#FF2D2D] focus:outline-none"
        ></textarea>

        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="showDeclineModal = false"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400"
          >
            Cancel
          </button>
          <button
            @click="submitDecline"
            class="px-5 py-2 bg-[#FF2D2D] text-white font-semibold rounded-lg hover:bg-[#E02626] transition"
          >
            Send & Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Feedback Modal -->
<div
  v-if="showFeedbackModal"
  class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 px-4"
>
  <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 relative">
    <h2 class="text-xl font-bold text-[#002B5C] mb-4">
      Feedback for {{ selectedOwnerName }}
    </h2>

    <div v-if="selectedFeedback.length" class="space-y-3 max-h-[60vh] overflow-y-auto">
      <div
        v-for="(feedback, i) in selectedFeedback"
        :key="i"
        class="border border-gray-200 rounded-lg p-3"
      >
        <div class="flex justify-between items-center">
          <span class="font-semibold text-[#182235]">{{ feedback.user?.name || 'Anonymous' }}</span>
          <span class="text-yellow-500">⭐ {{ feedback.rating }}/5</span>
        </div>
        <p class="text-gray-700 mt-1">{{ feedback.comment }}</p>
      </div>
    </div>

    <div v-else class="text-gray-500 text-center py-4">
      No feedback yet.
    </div>

    <button
      @click="showFeedbackModal = false"
      class="absolute top-4 right-5 text-gray-600 hover:text-black text-2xl font-bold"
    >
      ✕
    </button>
  </div>
</div>
<div v-if="showDeclineCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 px-4">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative">
    <h2 class="text-xl font-bold text-[#002B5C] mb-4">Delete/Decline Account</h2>
    <p class="text-gray-600 mb-3">Please write the reason for Delete/Decline this customer:</p>
    <textarea
      v-model="declineCustomerReason"
      rows="4"
      placeholder="Type reason here..."
      class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-[#FF2D2D] focus:outline-none"
    ></textarea>

    <div class="mt-6 flex justify-end gap-3">
      <button
        @click="showDeclineCustomerModal = false"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400"
      >
        Cancel
      </button>
      <button
        @click="submitDeclineCustomer"
        class="px-5 py-2 bg-[#FF2D2D] text-white font-semibold rounded-lg hover:bg-[#E02626] transition"
      >
        Send & Delete
      </button>
    </div>
  </div>
</div>
  </div>
      <!-- Footer -->
<footer class="bg-[#182235] text-gray-200 text-center sm:text-left py-8 px-6 border-t border-gray-700">
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-6">

    <!-- About -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">About WashWise</h2>
      <p class="text-sm leading-relaxed">
        WashWise is your trusted platform for booking car wash services — connecting customers and business owners for a smoother, cleaner experience every day.
      </p>
    </div>
    <!-- Contact Info -->
    <div>
      <h2 class="text-lg font-bold text-white mb-3">Contact Us</h2>
      <ul class="text-sm space-y-1">
        <li>📞 +63 992 759 4673</li>
        <li>✉️ washwise00@gmail.com</li>
      </ul>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="border-t border-gray-700 mt-8 pt-4 text-center text-xs text-gray-400">
    © {{ new Date().getFullYear() }} WashWise. All rights reserved.
    <br class="sm:hidden" /> Developed by <span class="text-[#FF2D2D] font-semibold">Washwise Team.</span>
  </div>
</footer>
</template>
