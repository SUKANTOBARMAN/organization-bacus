<script setup>
import { reactive, ref, onMounted } from 'vue'
import api, { errorMessage } from '../../../api/client'
import { useToast } from '../../../stores/toast'
import { useSettings } from '../../../composables/useSettings'

const toast = useToast()
const publicSettings = useSettings()
const fields = [
  ['site_name', 'সংগঠনের নাম'], ['site_tagline', 'স্লোগান / ট্যাগলাইন'], ['hero_title', 'হোম পেজ হিরো শিরোনাম'], ['hero_subtitle', 'হোম পেজ হিরো সাবটাইটেল'],
  ['established', 'প্রতিষ্ঠিত (সাল)'], ['registration_no', 'নিবন্ধন নং'], ['phone', 'ফোন নম্বর'], ['email', 'ইমেইল ঠিকানা'],
  ['address', 'ঠিকানা'], ['facebook', 'ফেইসবুক পেজ ইউআরএল'], ['youtube', 'ইউটিউব চ্যানেল ইউআরএল'], ['footer_text', 'ফুটার টেক্সট'],
]
const form = reactive(Object.fromEntries(fields.map(([k]) => [k, ''])))
const logoUrl = ref(null)
const saving = ref(false)

async function load() {
  const { data } = await api.get('/admin/settings')
  fields.forEach(([k]) => { form[k] = data[k] || '' })
  logoUrl.value = data.logo_url
}
onMounted(load)

async function save() {
  saving.value = true
  try {
    await api.put('/admin/settings', { settings: { ...form } })
    Object.assign(publicSettings.value, form)
    toast.push('সেটিংস সংরক্ষিত হয়েছে')
  } catch (e) { toast.error(errorMessage(e)) } finally { saving.value = false }
}

async function uploadLogo(e) {
  const file = e.target.files[0]
  if (!file) return
  const fd = new FormData()
  fd.append('logo', file)
  try {
    const { data } = await api.post('/admin/settings/logo', fd)
    logoUrl.value = data.logo_url
    publicSettings.value.logo_url = data.logo_url
    toast.push('লোগো আপডেট হয়েছে')
  } catch (err) { toast.error(errorMessage(err, 'লোগো আপলোড হয়নি')) }
}
</script>

<template>
  <h1 class="mb-5 font-serif text-2xl font-bold text-brand-dark">সাইট সেটিংস</h1>
  <div class="grid gap-6 lg:grid-cols-3">
    <form class="card grid gap-4 p-5 sm:grid-cols-2 lg:col-span-2" @submit.prevent="save">
      <div v-for="[key, label] in fields" :key="key" :class="{ 'sm:col-span-2': ['address', 'footer_text', 'hero_subtitle'].includes(key) }">
        <label class="label" :for="key">{{ label }}</label>
        <input :id="key" v-model="form[key]" class="input" />
      </div>
      <div class="sm:col-span-2"><button class="btn btn-primary" :disabled="saving">{{ saving ? 'সংরক্ষণ হচ্ছে...' : 'সেটিংস সংরক্ষণ করুন' }}</button></div>
    </form>

    <div class="card h-fit p-5">
      <h2 class="mb-3 font-semibold">লোগো</h2>
      <div class="mb-3 grid h-28 place-items-center rounded border border-dashed bg-stone-50">
        <img v-if="logoUrl" :src="logoUrl" class="max-h-24 max-w-full object-contain" />
        <span v-else class="text-sm text-stone-400">লোগো নেই</span>
      </div>
      <input type="file" accept="image/*" class="block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-stone-100 file:px-3 file:py-2" @change="uploadLogo" />
    </div>
  </div>
</template>