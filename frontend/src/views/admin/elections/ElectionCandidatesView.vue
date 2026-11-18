<script setup>
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import { resources } from '../../../config/resources'
import ResourceCrud from '../../../components/admin/ResourceCrud.vue'

const route = useRoute()
const { data: election } = useFetch(`/admin/elections/${route.params.id}`)
</script>

<template>
  <RouterLink to="/admin/elections" class="text-sm text-brand underline">← নির্বাচনসমূহ</RouterLink>
  <p v-if="election" class="mb-2 mt-1 text-stone-500">{{ election.title }}</p>
  <ResourceCrud :config="resources['election-candidates']" :parent-id="route.params.id" />
</template>