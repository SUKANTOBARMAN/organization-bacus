
<script setup>
import { reactive, ref } from 'vue'
import api, { errorMessage } from '../../../api/client'
import { BLOOD_GROUPS } from '../../../config/menu'
import PageTitle from '../../../components/common/PageTitle.vue'

const blank = () => ({
  name: '',
  phone: '',
  email: '',
  profession: '',
  workplace: '',
  blood_group: '',
  membership_type: 'general',
  address: '',
  message: '',
  website: ''
})

const form = reactive(blank())
const photo = ref(null)
const status = ref('')
const ok = ref(false)
const errors = ref({})
const sending = ref(false)

async function submit() {
  errors.value = {}
  status.value = ''
  sending.value = true

  const fd = new FormData()

  Object.entries(form).forEach(([k, v]) => v !== '' && fd.append(k, v))

  if (photo.value) fd.append('photo', photo.value)

  try {
    status.value = (await api.post('/membership-applications', fd)).data.message
    ok.value = true

    Object.assign(form, blank())
    photo.value = null

  } catch (e) {
    ok.value = false
    errors.value = e.response?.data?.errors || {}
    status.value = errorMessage(e, 'আবেদন জমা দেওয়া যায়নি')

  } finally {
    sending.value = false
  }
}
</script>

<template>
  <PageTitle
    title="সদস্য হতে আবেদন করুন"
    subtitle="ফর্মটি পূরণ করুন। আবেদন যাচাইয়ের পর কমিটি আপনার সঙ্গে যোগাযোগ করবে।"
  />

  <form
    class="card mx-auto grid max-w-3xl gap-4 p-6 sm:grid-cols-2"
    @submit.prevent="submit"
  >

    <!-- পূর্ণ নাম -->
    <div>
      <label class="label">পূর্ণ নাম *</label>
      <input v-model="form.name" class="input" />
      <p class="mt-1 text-xs text-red-600">
        {{ errors.name?.[0] }}
      </p>
    </div>

    <!-- মোবাইল নম্বর -->
    <div>
      <label class="label">মোবাইল নম্বর *</label>
      <input v-model="form.phone" class="input" />
      <p class="mt-1 text-xs text-red-600">
        {{ errors.phone?.[0] }}
      </p>
    </div>

    <!-- ইমেইল -->
    <div>
      <label class="label">ইমেইল ঠিকানা</label>
      <input
        v-model="form.email"
        type="email"
        class="input"
      />
      <p class="mt-1 text-xs text-red-600">
        {{ errors.email?.[0] }}
      </p>
    </div>

    <!-- সদস্যপদের ধরন -->
    <div>
      <label class="label">সদস্যপদের ধরন</label>

      <select v-model="form.membership_type" class="input">
        <option value="general">সাধারণ সদস্য</option>
        <option value="life">আজীবন সদস্য</option>
        <option value="associate">সহযোগী সদস্য</option>
      </select>
    </div>

    <!-- পেশা -->
    <div>
      <label class="label">পেশা</label>
      <input v-model="form.profession" class="input" />
    </div>

    <!-- কর্মস্থল -->
    <div>
      <label class="label">কর্মস্থল বা প্রতিষ্ঠানের নাম</label>
      <input v-model="form.workplace" class="input" />
    </div>

    <!-- রক্তের গ্রুপ -->
    <div>
      <label class="label">রক্তের গ্রুপ</label>

      <select v-model="form.blood_group" class="input">
        <option value="">নির্বাচন করুন</option>

        <option v-for="b in BLOOD_GROUPS" :key="b">
          {{ b }}
        </option>
      </select>
    </div>

    <!-- ছবি -->
    <div>
      <label class="label">ছবি সংযুক্ত করুন</label>

      <input
        type="file"
        accept="image/*"
        class="block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-stone-100 file:px-3 file:py-2"
        @change="photo = $event.target.files[0]"
      />

      <p class="mt-1 text-xs text-red-600">
        {{ errors.photo?.[0] }}
      </p>
    </div>

    <!-- ঠিকানা -->
    <div class="sm:col-span-2">
      <label class="label">বর্তমান ঠিকানা</label>

      <textarea
        v-model="form.address"
        rows="2"
        class="input"
      />
    </div>

    <!-- যোগদানের কারণ -->
    <div class="sm:col-span-2">
      <label class="label">
        আপনি কেন এই সংগঠনের সদস্য হতে চান?
      </label>

      <textarea
        v-model="form.message"
        rows="3"
        class="input"
      />
    </div>

    <!-- স্প্যাম প্রতিরোধের Hidden Field -->
    <input
      v-model="form.website"
      class="hidden"
      tabindex="-1"
      autocomplete="off"
      aria-hidden="true"
    />

    <!-- আবেদন জমা -->
    <div class="sm:col-span-2">
      <button
        class="btn btn-primary"
        :disabled="sending"
      >
        {{ sending ? 'আবেদন জমা হচ্ছে...' : 'সদস্যপদের আবেদন জমা দিন' }}
      </button>

      <p
        v-if="status"
        :class="ok ? 'text-green-700' : 'text-red-600'"
        class="mt-3 text-sm"
      >
        {{ status }}
      </p>
    </div>

  </form>
</template>
