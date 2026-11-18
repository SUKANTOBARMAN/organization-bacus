<script setup>
import { publicMenu } from '../../config/menu'
import { useSettings } from '../../composables/useSettings'

const settings = useSettings()
</script>

<template>
  <footer class="mt-10 bg-brand-dark text-white/80">
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-10 md:grid-cols-3">
      <div>
        <p class="font-serif text-xl font-bold text-white">{{ settings.site_name || 'প্রতিষ্ঠানের নাম' }}</p>
        <p class="mt-2 text-sm">{{ settings.site_tagline }}</p>
      </div>
      <div>
        <p class="mb-3 font-semibold text-white">গুরুত্বপূর্ণ লিংক</p>
        <ul class="grid grid-cols-2 gap-1.5 text-sm">
          <li v-for="item in publicMenu" :key="item.path"><RouterLink :to="item.path" class="hover:text-white">{{ item.label }}</RouterLink></li>
        </ul>
      </div>
      <div class="space-y-1.5 text-sm">
        <p class="mb-3 font-semibold text-white">যোগাযোগ</p>
        <p v-if="settings.address">{{ settings.address }}</p>
        <p v-if="settings.phone">{{ settings.phone }}</p>
        <p v-if="settings.email">{{ settings.email }}</p>
        <p class="flex gap-4 pt-2">
          <a v-if="settings.facebook" :href="settings.facebook" target="_blank" rel="noopener" class="underline">ফেসবুক</a>
          <a v-if="settings.youtube" :href="settings.youtube" target="_blank" rel="noopener" class="underline">ইউটিউব</a>
        </p>
      </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs">{{ settings.footer_text }}</div>
  </footer>
</template>