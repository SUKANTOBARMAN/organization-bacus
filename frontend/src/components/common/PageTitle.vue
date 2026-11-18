<script setup>
defineProps({ title: String, subtitle: String })
</script>

<template>
  <div class="mb-8 border-b pb-5">
    <h1 class="font-serif text-3xl font-bold text-brand-dark">{{ title }}</h1>
    <p v-if="subtitle" class="mt-1 text-stone-500">{{ subtitle }}</p>
    <div class="mt-3 h-1 w-16 rounded bg-accent" />
  </div>
</template>
