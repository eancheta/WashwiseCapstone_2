<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'

const picturePreview = ref<string | null>(null)

const form = useForm({
  id_number: '',
  picture_id: null as File | null,
})

function handlePictureChange(event: Event) {
  const target = event.target as HTMLInputElement | null
  if (target?.files?.length) {
    form.picture_id = target.files[0]
    picturePreview.value = URL.createObjectURL(target.files[0])
  } else {
    form.picture_id = null
    picturePreview.value = null
  }
}

function submit() {
  form.post(route('verify.store'), {
    onSuccess: () => {
      alert('✅ Verification submitted successfully!')
      form.reset()
      picturePreview.value = null
    },
  })
}

function handleIdNumberInput(e: Event) {
  const input = e.target as HTMLInputElement
  const cleaned = input.value.replace(/\D/g, '') // remove all non-digits
  input.value = cleaned
  form.id_number = cleaned
}
</script>

<template>
  <Head title="Verify Account" />
  <div class="min-h-screen bg-[#F8FAFC] flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-lg p-8 rounded-2xl shadow-lg">
      <h2 class="text-2xl font-bold text-[#002B5C] mb-6 text-center">🪪 Verify Your Account</h2>

      <form @submit.prevent="submit" class="space-y-5">
        <!-- ID Number -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">ID Number</label>
          <input
            type="text"
            required
            maxlength="20"
            @input="handleIdNumberInput"
            v-model="form.id_number"
            placeholder="Enter your ID number"
            class="w-full p-3 border border-gray-300 rounded-xl text-gray-900
                   focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm"
          />
          <InputError :message="form.errors.id_number" />
        </div>

        <!-- Picture ID Upload -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Picture ID</label>
          <input
            type="file"
            required
            accept="image/*"
            @change="handlePictureChange"
            class="w-full p-3 border border-gray-300 rounded-xl text-gray-900
                   focus:ring-2 focus:ring-[#002B5C] focus:outline-none shadow-sm
                   file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                   file:text-sm file:font-semibold file:bg-[#002B5C] file:text-white
                   hover:file:bg-[#003b7a] cursor-pointer"
          />
          <img
            v-if="picturePreview"
            :src="picturePreview"
            class="mt-2 w-32 h-32 object-cover border rounded"
          />
          <InputError :message="form.errors.picture_id" />
        </div>

        <Button
          type="submit"
          class="w-full bg-[#002B5C] text-white font-semibold py-3 rounded-xl hover:bg-[#003b7a]"
        >
          Submit Verification
        </Button>
      </form>
    </div>
  </div>
</template>
