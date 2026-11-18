<script setup>
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import ImageManager from '../../../components/admin/ImageManager.vue'

const route = useRoute()
const id = route.params.id
const { data: album } = useFetch(`/admin/albums/${id}`)
</script>

<template>
  <RouterLink to="/admin/albums" class="text-sm text-brand underline">← অ্যালবামসমূহ</RouterLink>
  <h1 class="mb-5 mt-1 font-serif text-2xl font-bold text-brand-dark">{{ album?.title || 'অ্যালবাম' }} — ছবিসমূহ</h1>
  <ImageManager list-url="/admin/photos" :list-params="{ album_id: id }" :upload-url="`/admin/albums/${id}/photos`" delete-url="/admin/photos" />
</template>