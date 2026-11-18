<script setup>
import { onMounted, onUnmounted } from 'vue'

defineProps({ title: String, wide: Boolean })
const emit = defineEmits(['close'])

const onKey = (e) => e.key === 'Escape' && emit('close')
onMounted(() => document.addEventListener('keydown', onKey))
onUnmounted(() => document.removeEventListener('keydown', onKey))
</script>

<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4 sm:p-8" @mousedown.self="emit('close')">
      <div :class="[wide ? 'max-w-3xl' : 'max-w-xl', 'w-full rounded-lg bg-white shadow-xl']" role="dialog" aria-modal="true">
        <div class="flex items-center justify-between border-b px-5 py-4">
          <h2 class="font-serif text-lg font-bold">{{ title }}</h2>
          <button class="rounded p-1 text-stone-500 hover:bg-stone-100" aria-label="বন্ধ করুন" @click="emit('close')">✕</button>
        </div>
        <div class="p-5"><slot /></div>
        <div v-if="$slots.footer" class="flex justify-end gap-2 border-t px-5 py-3"><slot name="footer" /></div>
      </div>
    </div>
  </Teleport>
</template>