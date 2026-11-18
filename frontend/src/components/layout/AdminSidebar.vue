<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { adminMenu } from '../../config/admin-menu'
import { isActive } from '../../config/menu'
import { useAuthStore } from '../../stores/auth'
import { useSettings } from '../../composables/useSettings'

const route = useRoute()
const auth = useAuthStore()
const settings = useSettings()

const groups = computed(() =>
  adminMenu
    .map((g) => ({ ...g, items: g.items.filter((i) => auth.can(i.roles)) }))
    .filter((g) => g.items.length),
)
</script>

<template>
  <aside class="flex h-full w-64 flex-col bg-brand-dark text-white/80">
    <div class="border-b border-white/10 px-5 py-4">
      <p class="font-serif text-lg font-bold text-white">{{ settings.site_name || 'Organization' }}</p>
      <p class="text-xs">অ্যাডমিন প্যানেল</p>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Admin">
      <div v-for="(g, gi) in groups" :key="gi" class="mb-5">
        <p v-if="g.group" class="mb-1.5 px-3 text-xs font-semibold text-white/50">{{ g.group }}</p>
        <RouterLink
          v-for="item in g.items"
          :key="item.to"
          :to="item.to"
          :class="[
            isActive(route, item.to, item.exact) ? 'bg-white/15 text-white' : 'hover:bg-white/10 hover:text-white',
            'flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors',
          ]"
        >
          <component :is="item.icon" :size="17" />
          {{ item.label }}
        </RouterLink>
      </div>
    </nav>
  </aside>
</template>