<script setup>
const props = defineProps({ meta: Object })
const emit = defineEmits(['change'])
</script>

<template>
  <div v-if="meta && meta.last_page > 1" class="mt-4 flex items-center justify-between text-sm">
    <span class="text-stone-500">মোট {{ meta.total }} টি</span>
    <div class="flex items-center gap-2">
      <button class="btn btn-outline btn-sm" :disabled="meta.current_page <= 1" @click="emit('change', meta.current_page - 1)">পূর্ববর্তী</button>
      <span>{{ meta.current_page }} / {{ meta.last_page }}</span>
      <button class="btn btn-outline btn-sm" :disabled="meta.current_page >= meta.last_page" @click="emit('change', meta.current_page + 1)">পরবর্তী</button>
    </div>
  </div>
</template>