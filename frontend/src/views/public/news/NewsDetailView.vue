
<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import StateBlock from '../../../components/common/StateBlock.vue'

const route = useRoute()
const { data, loading, error } = useFetch(`/posts/${route.params.id}`)
const active = ref(null)

const pictures = computed(() => {
  if (!data.value) return []

  const list = data.value.images.map((i) => i.image_url)

  return data.value.cover_image_url
    ? [data.value.cover_image_url, ...list]
    : list
})

const embed = computed(() => {
  const m = (data.value?.video_url || '').match(
    /(?:youtu\.be\/|v=|embed\/|shorts\/)([A-Za-z0-9_-]{11})/
  )

  return m ? `https://www.youtube.com/embed/${m[1]}` : null
})

const fmt = (d) =>
  d
    ? new Date(d).toLocaleString('bn-BD', {
        dateStyle: 'long',
        timeStyle: 'short'
      })
    : ''
</script>

<template>
  <!-- সংবাদ ও অনুষ্ঠানের তালিকায় ফিরে যাওয়া -->
  <RouterLink
    to="/news"
    class="mb-4 inline-block text-sm text-brand underline"
  >
    ← সংবাদ ও অনুষ্ঠানে ফিরে যান
  </RouterLink>

  <StateBlock :loading="loading" :error="error">
    <article
      v-if="data"
      class="mx-auto max-w-4xl"
    >

      <!-- সংবাদ বা অনুষ্ঠানের তথ্য -->
      <p class="text-sm text-accent">
        {{ data.type === 'event' ? 'অনুষ্ঠান' : 'সংবাদ' }}
        · {{ fmt(data.published_at) }}
        · {{ data.views_count }} বার দেখা হয়েছে
      </p>

      <!-- শিরোনাম -->
      <h1 class="mb-4 mt-1 font-serif text-3xl font-bold text-brand-dark sm:text-4xl">
        {{ data.title }}
      </h1>

      <!-- অনুষ্ঠানের বিস্তারিত -->
      <div
        v-if="data.type === 'event'"
        class="mb-5 rounded-lg border-l-4 border-accent bg-white p-4 text-sm"
      >
        <p v-if="data.event_at">
          <strong>তারিখ ও সময়:</strong>
          {{ fmt(data.event_at) }}
        </p>

        <p v-if="data.venue">
          <strong>স্থান:</strong>
          {{ data.venue }}
        </p>
      </div>

      <!-- প্রচ্ছদ ছবি -->
      <img
        v-if="data.cover_image_url"
        :src="data.cover_image_url"
        :alt="data.title"
        class="mb-6 w-full cursor-zoom-in rounded-lg"
        @click="active = data.cover_image_url"
      />

      <!-- সংক্ষিপ্ত বিবরণ -->
      <p
        v-if="data.excerpt"
        class="mb-4 text-lg text-stone-600"
      >
        {{ data.excerpt }}
      </p>

      <!-- মূল সংবাদ বা অনুষ্ঠানের বিবরণ -->
      <div class="whitespace-pre-line leading-relaxed">
        {{ data.body }}
      </div>

      <!-- ইউটিউব ভিডিও -->
      <div
        v-if="embed"
        class="mt-8 aspect-video overflow-hidden rounded-lg"
      >
        <iframe
          :src="embed"
          class="h-full w-full"
          title="ভিডিও"
          allowfullscreen
          loading="lazy"
        />
      </div>

      <!-- সংযুক্ত ছবিসমূহ -->
      <section
        v-if="data.images.length"
        class="mt-8"
      >
        <h2 class="mb-3 font-serif text-xl font-bold">
          ছবিসমূহ
        </h2>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
          <img
            v-for="i in data.images"
            :key="i.id"
            :src="i.image_url"
            :alt="i.caption || ''"
            class="aspect-[4/3] w-full cursor-zoom-in rounded-lg object-cover"
            loading="lazy"
            @click="active = i.image_url"
          />
        </div>
      </section>

    </article>
  </StateBlock>

  <!-- পূর্ণ পর্দায় ছবি দেখার ব্যবস্থা -->
  <Teleport to="body">
    <div
      v-if="active"
      class="fixed inset-0 z-50 grid place-items-center bg-black/90 p-4"
      @click="active = null"
    >
      <img
        :src="active"
        class="max-h-full max-w-full rounded"
        alt=""
      />

      <button
        class="absolute right-5 top-5 text-3xl text-white"
        aria-label="বন্ধ করুন"
        @click.stop="active = null"
      >
        ×
      </button>
    </div>
  </Teleport>
</template>
