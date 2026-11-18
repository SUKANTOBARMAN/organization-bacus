<script setup>
import { reactive, ref } from 'vue'
import api, { errorMessage } from '../../../api/client'
import { useSettings } from '../../../composables/useSettings'
import PageTitle from '../../../components/common/PageTitle.vue'

const settings = useSettings()
const blank = () => ({ name: '', email: '', phone: '', subject: '', message: '', website: '' })
const form = reactive(blank())
const status = ref('')
const ok = ref(false)
const errors = ref({})
const sending = ref(false)

async function submit() {
  errors.value = {}; status.value = ''; sending.value = true
  try {
    status.value = (await api.post('/contact', form)).data.message
    ok.value = true
    Object.assign(form, blank())
  } catch (e) {
    ok.value = false
    errors.value = e.response?.data?.errors || {}
    status.value = errorMessage(e, 'পাঠানো যায়নি')
  } finally { sending.value = false }
}
</script>

<template>
  <PageTitle title="যোগাযোগ করুন" />
  <div class="grid gap-10 md:grid-cols-5">
    <div class="space-y-5 md:col-span-2">
      <div v-if="settings.address"><p class="label">ঠিকানা</p><p>{{ settings.address }}</p></div>
      <div v-if="settings.phone"><p class="label">ফোন</p><p>{{ settings.phone }}</p></div>
      <div v-if="settings.email"><p class="label">ইমেইল</p><p>{{ settings.email }}</p></div>
    </div>
    <form class="card space-y-3 p-5 md:col-span-3" @submit.prevent="submit">
      <div class="grid gap-3 sm:grid-cols-2">
        <div><input v-model="form.name" class="input" placeholder="আপনার নাম" /><p class="mt-1 text-xs text-red-600">{{ errors.name?.[0] }}</p></div>
        <div><input v-model="form.email" type="email" class="input" placeholder="আপনার ইমেইল" /><p class="mt-1 text-xs text-red-600">{{ errors.email?.[0] }}</p></div>
      </div>
      <input v-model="form.subject" class="input" placeholder="বিষয়" />
      <input v-model="form.website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />
      <div><textarea v-model="form.message" rows="5" class="input" placeholder="আপনার বার্তা" /><p class="mt-1 text-xs text-red-600">{{ errors.message?.[0] }}</p></div>
      <button class="btn btn-primary" :disabled="sending">{{ sending ? 'পাঠানো হচ্ছে...' : 'বার্তা পাঠান' }}</button>
      <p v-if="status" :class="ok ? 'text-green-700' : 'text-red-600'" class="text-sm">{{ status }}</p>
    </form>
  </div>
</template>