<script setup>
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import ImageManager from '../../../components/admin/ImageManager.vue'

const route = useRoute()
const id = route.params.id
const { data: post } = useFetch(`/admin/posts/${id}`)
</script>

<template>
  <RouterLink to="/admin/posts" class="text-sm text-brand underline">← সংবাদ ও ইভেন্টসমূহ</RouterLink>
  <h1 class="mb-5 mt-1 font-serif text-2xl font-bold text-brand-dark">{{ post?.title || 'পোস্ট' }} — ছবিসমূহ</h1>
  <ImageManager list-url="/admin/post-images" :list-params="{ post_id: id }" :upload-url="`/admin/posts/${id}/images`" delete-url="/admin/post-images" />
</template>