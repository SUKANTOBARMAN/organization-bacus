<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { LogOut, Menu, ExternalLink } from '@lucide/vue'
import AdminSidebar from '../components/layout/AdminSidebar.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()
const open = ref(false)

watch(() => route.fullPath, () => { open.value = false })

async function logout() {
  await auth.logout()
  router.push({ name: 'admin-login' })
}

const roleLabel = { super_admin: 'Super admin', admin: 'Admin', editor: 'Editor' }
</script>

<template>
  <div class="flex h-screen bg-stone-100">
    <!-- ডেস্কটপ সাইডবার -->
    <div class="hidden lg:block"><AdminSidebar /></div>

    <!-- মোবাইল ড্রয়ার -->
    <div v-if="open" class="fixed inset-0 z-40 lg:hidden">
      <div class="absolute inset-0 bg-black/50" @click="open = false" />
      <div class="absolute inset-y-0 left-0"><AdminSidebar /></div>
    </div>

    <div class="flex min-w-0 flex-1 flex-col">
      <header class="flex h-14 shrink-0 items-center justify-between border-b bg-white px-4">
        <button class="rounded p-2 hover:bg-stone-100 lg:hidden" aria-label="Menu" @click="open = true"><Menu :size="20" /></button>
        <a href="/" target="_blank" class="hidden items-center gap-1.5 text-sm text-stone-500 hover:text-brand lg:flex">
          <ExternalLink :size="15" /> View website
        </a>
        <div class="flex items-center gap-4">
          <div class="text-right text-sm leading-tight">
            <p class="font-medium">{{ auth.user?.name }}</p>
            <p class="text-xs text-stone-500">{{ roleLabel[auth.user?.role] }}</p>
          </div>
          <button class="btn btn-outline btn-sm" @click="logout"><LogOut :size="14" /> Logout</button>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-4 sm:p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
