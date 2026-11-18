<script setup>
import { useFetch } from '../../../composables/useFetch'
import { useSettings } from '../../../composables/useSettings'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const settings = useSettings()
const { data, loading, error } = useFetch('/pages/about')
</script>

<template>
  <PageTitle title="আমাদের সম্পর্কে" :subtitle="settings.established ? `প্রতিষ্ঠিত: ${settings.established}` : ''" />
  <StateBlock :loading="loading" :error="error">
    <article v-if="data" class="grid gap-8 lg:grid-cols-3">
      <div class="prose max-w-none leading-relaxed lg:col-span-2" v-html="data.body" />
      <img v-if="data.image_url" :src="data.image_url" :alt="data.title" class="w-full rounded-lg object-cover" />
    </article>
  </StateBlock>
</template>