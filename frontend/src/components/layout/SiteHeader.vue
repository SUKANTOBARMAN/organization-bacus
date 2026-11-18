<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { publicMenu, isActive } from '../../config/menu'
import { useSettings } from '../../composables/useSettings'

const settings = useSettings()
const route = useRoute()
const open = ref(false)

watch(() => route.fullPath, () => { open.value = false })
</script>

<template>
  <!-- display:contents → sticky অংশগুলো পুরো পেজের সাপেক্ষে আটকে থাকে -->
  <div class="contents">
    <!-- ১. উপরের পাতলা বার -->
    <div class="hidden bg-brand-dark text-xs text-white/80 sm:block">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-1.5">
        <p class="flex flex-wrap gap-x-6">
          <span v-if="settings.phone">{{ settings.phone }}</span>
          <span v-if="settings.email">{{ settings.email }}</span>
        </p>
        <RouterLink :to="{ name: 'admin-login' }" class="hover:text-white">অ্যাডমিন লগইন</RouterLink>
      </div>
    </div>

    <!-- ২. ব্র্যান্ড সারি (মোবাইলে স্টিকি) -->
    <div class="border-b bg-white max-[1239px]:sticky max-[1239px]:top-0 max-[1239px]:z-30">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3.5">
        <RouterLink to="/" class="flex items-center gap-3">
          <img v-if="settings.logo_url" :src="settings.logo_url" alt="" class="h-12 w-12 shrink-0 rounded-full object-cover" />
          <span v-else class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-brand font-serif text-xl font-bold text-white">
            {{ (settings.site_name || 'O').charAt(0) }}
          </span>
          <span class="block">
            <span class="block font-serif text-xl font-bold leading-tight text-brand-dark sm:text-2xl">{{ settings.site_name || 'প্রতিষ্ঠানের নাম' }}</span>
            <span v-if="settings.site_tagline" class="hidden text-sm text-stone-500 sm:block">{{ settings.site_tagline }}</span>
          </span>
        </RouterLink>

        <RouterLink :to="{ name: 'join' }" class="btn btn-primary ml-auto hidden sm:inline-flex">সদস্য হোন</RouterLink>
        <button
          class="btn btn-outline min-[1240px]:hidden"
          :aria-expanded="open"
          aria-controls="mobile-nav"
          @click="open = !open"
        >{{ open ? 'বন্ধ করুন' : 'মেনু' }}</button>
      </div>

      <nav v-show="open" id="mobile-nav" class="border-t bg-white min-[1240px]:hidden" aria-label="Main">
        <ul class="mx-auto max-w-7xl px-3 py-2">
          <li v-for="item in publicMenu" :key="item.path">
            <RouterLink
              :to="item.path"
              :class="[isActive(route, item.path) ? 'bg-brand/10 font-semibold text-brand-dark' : 'text-stone-700', 'block rounded-md px-3 py-2.5 text-[15px]']"
            >{{ item.label }}</RouterLink>
          </li>
        </ul>
      </nav>
    </div>

    <!-- ৩. ডেস্কটপ নেভিগেশন: এক লাইনে, স্টিকি -->
    <nav class="hidden border-b bg-white shadow-sm min-[1240px]:sticky min-[1240px]:top-0 min-[1240px]:z-30 min-[1240px]:block" aria-label="Main">
      <ul class="mx-auto flex max-w-7xl justify-between px-5">
        <li v-for="item in publicMenu" :key="item.path" class="shrink-0">
          <RouterLink
            :to="item.path"
            :class="[
              isActive(route, item.path) ? 'border-accent font-semibold text-brand-dark' : 'border-transparent text-stone-600 hover:bg-stone-50 hover:text-brand',
              'block whitespace-nowrap border-b-[3px] px-2.5 py-3.5 text-sm transition-colors',
            ]"
          >{{ item.label }}</RouterLink>
        </li>
      </ul>
    </nav>
  </div>
</template>