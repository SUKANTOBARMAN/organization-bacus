<script setup>
import { useToast } from '../../stores/toast'
const toast = useToast()
</script>

<template>
  <div class="fixed right-4 top-4 z-[60] flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2">
    <div
      v-for="t in toast.items"
      :key="t.id"
      :class="[t.type === 'error' ? 'bg-red-600' : 'bg-brand', 'rounded-md px-4 py-3 text-sm text-white shadow-lg']"
      role="status"
    >{{ t.message }}</div>
  </div>
</template>
