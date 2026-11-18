<script setup>
defineProps({
  loading: Boolean,
  error: String,
  empty: Boolean,
  emptyText: { type: String, default: 'এখানে এখনো কিছু নেই।' },
})
</script>

<template>
  <div v-if="loading" class="py-12 text-center text-stone-500">লোড হচ্ছে...</div>
  <div v-else-if="error" class="rounded-md border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
  <div v-else-if="empty" class="rounded-md border border-dashed bg-white p-10 text-center text-stone-500">{{ emptyText }}</div>
  <slot v-else />
</template>
