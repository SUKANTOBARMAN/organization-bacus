<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { errorMessage } from '../../../api/client'
import { useAuthStore } from '../../../stores/auth'
import { useSettings } from '../../../composables/useSettings'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const settings = useSettings()
const form = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form.email, form.password)
    router.push(route.query.redirect || { name: 'admin-dashboard' })
  } catch (e) {
    error.value = e.response?.data?.errors?.email?.[0] || errorMessage(e, 'লগইন করা যায়নি')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="grid min-h-screen place-items-center bg-brand-dark p-4">
    <form class="w-full max-w-sm rounded-xl bg-white p-8 shadow-xl" @submit.prevent="submit">
      <div class="mb-6 text-center">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-brand font-serif text-2xl font-bold text-white">{{ (settings.site_name || 'O').charAt(0) }}</span>
        <h1 class="mt-3 font-serif text-xl font-bold text-brand-dark">{{ settings.site_name || 'Organization' }}</h1>
        <p class="text-sm text-stone-500">অ্যাডমিন প্যানেল লগইন</p>
      </div>
      <label class="label" for="email">ইমেইল</label>
      <input id="email" v-model="form.email" type="email" class="input mb-4" required autocomplete="username" />
      <label class="label" for="password">পাসওয়ার্ড</label>
      <input id="password" v-model="form.password" type="password" class="input mb-4" required autocomplete="current-password" />
      <p v-if="error" class="mb-4 rounded bg-red-50 p-2 text-sm text-red-700">{{ error }}</p>
      <button class="btn btn-primary w-full" :disabled="loading">{{ loading ? 'লগইন হচ্ছে...' : 'লগইন করুন' }}</button>
      <RouterLink to="/" class="mt-4 block text-center text-sm text-stone-500 hover:text-brand">← ওয়েবসাইটে ফিরে যান</RouterLink>
    </form>
  </div>
</template>