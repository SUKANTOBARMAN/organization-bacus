<script setup>
import { computed } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import { useAuthStore } from '../../../stores/auth'
import StateBlock from '../../../components/common/StateBlock.vue'

const auth = useAuthStore()
const { data, loading, error } = useFetch('/admin/dashboard')

const cards = computed(() => {
  const d = data.value || {}
  return [
    { label: 'নোটিশসমূহ', value: d.notices, to: '/admin/notices' },
    { label: 'সংবাদ ও ইভেন্ট', value: d.posts, to: '/admin/posts' },
    { label: 'আসন্ন ইভেন্টসমূহ', value: d.upcoming_events, to: '/admin/posts' },
    { label: 'অ্যালবামসমূহ', value: d.albums, to: '/admin/albums' },
    { label: 'সক্রিয় সদস্যবৃন্দ', value: d.members, to: '/admin/members' },
    { label: 'অপেক্ষমাণ আবেদন', value: d.pending_applications, to: '/admin/applications', alert: d.pending_applications > 0 },
    { label: 'অপঠিত বার্তা', value: d.unread_messages, to: '/admin/messages', alert: d.unread_messages > 0 },
    { label: 'চলতি বছর সংগৃহীত (৳)', value: d.payments_this_year?.toLocaleString(), to: '/admin/payments' },
  ].filter((c) => c.value !== undefined)
})
</script>

<template>
  <div>
    <h1 class="font-serif text-2xl font-bold text-brand-dark">স্বাগতম, {{ auth.user?.name }}</h1>
    <p class="mb-6 text-sm text-stone-500">এক নজরে সংগঠনের বর্তমান অবস্থা</p>

    <StateBlock :loading="loading" :error="error">
      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <RouterLink v-for="c in cards" :key="c.label" :to="c.to" :class="[c.alert ? 'border-amber-300 bg-amber-50' : 'bg-white', 'card p-4 transition hover:shadow']">
          <p class="text-sm text-stone-500">{{ c.label }}</p>
          <p class="mt-1 font-serif text-3xl font-bold text-brand-dark">{{ c.value }}</p>
        </RouterLink>
      </div>

      <div v-if="data?.recent_messages" class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card">
          <h2 class="border-b px-4 py-3 font-semibold">সাম্প্রতিক বার্তা</h2>
          <ul class="divide-y">
            <li v-for="m in data.recent_messages" :key="m.id" class="px-4 py-3 text-sm">
              <span :class="{ 'font-semibold': !m.read_at }">{{ m.name }}</span>
              <span class="text-stone-500"> — {{ m.subject || '(বিষয়বিহীন)' }}</span>
            </li>
            <li v-if="!data.recent_messages.length" class="px-4 py-6 text-center text-sm text-stone-400">কোনো বার্তা নেই</li>
          </ul>
        </div>
        <div class="card">
          <h2 class="border-b px-4 py-3 font-semibold">সাম্প্রতিক কার্যক্রম</h2>
          <ul class="divide-y">
            <li v-for="a in data.recent_activity" :key="a.id" class="px-4 py-3 text-sm">
              <span class="font-medium">{{ a.user?.name || 'সিস্টেম' }}</span>
              {{ a.action }} <span class="text-stone-500">{{ a.subject }} {{ a.summary ? '— ' + a.summary : '' }}</span>
            </li>
            <li v-if="!data.recent_activity.length" class="px-4 py-6 text-center text-sm text-stone-400">এখনো কিছু নেই</li>
          </ul>
        </div>
      </div>
    </StateBlock>
  </div>
</template>