<script setup>
defineProps({ src: String, name: String, size: { type: String, default: 'h-14 w-14' } })
</script>

<template>
  <img v-if="src" :src="src" :alt="name" :class="[size, 'shrink-0 rounded-full object-cover']" loading="lazy" />
  <span v-else :class="[size, 'grid shrink-0 place-items-center rounded-full bg-brand/10 font-serif font-bold text-brand']">{{ (name || '?').charAt(0) }}</span>
</template>
