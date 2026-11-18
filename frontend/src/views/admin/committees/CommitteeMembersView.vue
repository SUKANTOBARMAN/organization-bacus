<script setup>
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import { resources } from '../../../config/resources'
import ResourceCrud from '../../../components/admin/ResourceCrud.vue'

const route = useRoute()
const { data: committee } = useFetch(`/admin/committees/${route.params.id}`)
</script>

<template>
  <RouterLink to="/admin/committees" class="text-sm text-brand underline">← কমিটিসমূহ</RouterLink>
  <p v-if="committee" class="mb-2 mt-1 text-stone-500">{{ committee.title }} {{ committee.term }}</p>
  <ResourceCrud :config="resources['committee-members']" :parent-id="route.params.id" />
</template>