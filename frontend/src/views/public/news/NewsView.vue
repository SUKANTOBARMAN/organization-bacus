
<script setup>
import { ref, watch } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'
import Pagination from '../../../components/common/Pagination.vue'

const type = ref('')
const page = ref(1)

watch(type, () => {
  page.value = 1
})

const { data, loading, error } = useFetch(
  '/posts',
  () => ({
    type: type.value || undefined,
    page: page.value
  })
)

const fmt = (d) =>
  d
    ? new Date(d).toLocaleDateString('bn-BD', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
    : ''
</script>

<template>
  <!-- পেজের শিরোনাম -->
  <PageTitle title="সংবাদ ও অনুষ্ঠান" />

  <!-- ক্যাটাগরি নির্বাচন -->
  <div class="mb-5 flex gap-2">
    <button
      v-for="[v, l] in [
        ['', 'সকল'],
        ['news', 'সংবাদ'],
        ['event', 'অনুষ্ঠান']
      ]"
      :key="v"
      :class="[type === v ? 'btn-primary' : 'btn-outline', 'btn btn-sm']"
      @click="type = v"
    >
      {{ l }}
    </button>
  </div>

  <!-- সংবাদ ও অনুষ্ঠানের তালিকা -->
  <StateBlock
    :loading="loading"
    :error="error"
    :empty="!data?.data?.length"
    empty-text="কোনো সংবাদ বা অনুষ্ঠান পাওয়া যায়নি।"
  >
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

      <RouterLink
        v-for="p in data.data"
        :key="p.id"
        :to="`/news/${p.id}`"
        class="card group overflow-hidden transition hover:shadow-md"
      >
        <!-- প্রচ্ছদ ছবি -->
        <div class="relative">
          <img
            v-if="p.cover_image_url"
            :src="p.cover_image_url"
            :alt="p.title"
            class="aspect-video w-full object-cover transition group-hover:scale-[1.02]"
            loading="lazy"
          />

          <div
            v-else
            class="grid aspect-video place-items-center bg-brand/10 font-serif text-4xl text-brand/40"
          >
            {{ p.title.charAt(0) }}
          </div>

          <!-- নির্বাচিত সংবাদ -->
          <span
            v-if="p.is_featured"
            class="absolute left-3 top-3 rounded bg-accent px-2 py-0.5 text-xs font-semibold text-black"
          >
            বিশেষ সংবাদ
          </span>

          <!-- ছবির সংখ্যা -->
          <span
            v-if="p.images_count"
            class="absolute bottom-3 right-3 rounded bg-black/60 px-2 py-0.5 text-xs text-white"
          >
            📷 {{ p.images_count + (p.cover_image_url ? 1 : 0) }}
          </span>
        </div>

        <!-- সংবাদ ও অনুষ্ঠানের বিবরণ -->
        <div class="p-4">
          <p class="text-xs text-accent">
            {{ p.type === 'event' ? 'অনুষ্ঠান' : 'সংবাদ' }}
            · {{ fmt(p.published_at) }}
          </p>

          <h3 class="mt-0.5 font-semibold leading-snug">
            {{ p.title }}
          </h3>

          <p
            v-if="p.excerpt"
            class="mt-1 line-clamp-2 text-sm text-stone-500"
          >
            {{ p.excerpt }}
          </p>
        </div>
      </RouterLink>

    </div>

    <!-- পৃষ্ঠা পরিবর্তন -->
    <Pagination
      :meta="data"
      @change="(n) => (page = n)"
    />

  </StateBlock>
</template>
