<script setup>
import { ref, computed } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const { data, loading, error } = useFetch('/documents')
const labels = {
  constitution: 'গঠনতন্ত্র',
  rules: 'নিয়ম ও বিধিমালা',
  minutes: 'সভার কার্যবিবরণী',
  annual_report: 'বার্ষিক প্রতিবেদন',
  form: 'ফরমসমূহ',
  download: 'ডাউনলোডসমূহ',
}
const groups = computed(() => {
  const g = {}
  ;(data.value || []).forEach((d) => (g[d.type] ||= []).push(d))
  return Object.entries(g)
})
</script>

<template>
  <PageTitle title="গঠনতন্ত্র ও নিয়মাবলী" />
  <StateBlock :loading="loading" :error="error" :empty="!data?.length">
    <section v-for="[type, docs] in groups" :key="type" class="mb-8">
      <h2 class="mb-3 font-serif text-xl font-bold text-brand-dark">{{ labels[type] || type }}</h2>
      <ul class="card divide-y">
        <li v-for="d in docs" :key="d.id" class="flex items-center justify-between gap-4 p-4">
          <div><h3 class="font-semibold">{{ d.title }}</h3><p v-if="d.description" class="text-sm text-stone-500">{{ d.description }}</p></div>
          <a :href="d.file_url" target="_blank" class="btn btn-primary btn-sm shrink-0">ডাউনলোড করুন</a>
        </li>
      </ul>
    </section>
  </StateBlock>
</template>