
<script setup>
import { ref, watch } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'
import Pagination from '../../../components/common/Pagination.vue'

const tabs = [
  ['', 'সকল'],
  ['notice', 'বিজ্ঞপ্তি'],
  ['circular', 'পরিপত্র'],
  ['tender', 'দরপত্র'],
  ['result', 'ফলাফল'],
  ['press_release', 'প্রেস বিজ্ঞপ্তি']
]

const category = ref('')
const search = ref('')
const page = ref(1)

watch([category, search], () => {
  page.value = 1
})

const { data, loading, error } = useFetch('/notices', () => ({
  category: category.value || undefined,
  search: search.value || undefined,
  page: page.value
}))
</script>

<template>
  <PageTitle title="বিজ্ঞপ্তি ও পরিপত্র" />

  <div class="mb-5 flex flex-wrap items-center gap-2">
    <button
      v-for="[v, label] in tabs"
      :key="v"
      :class="[category === v ? 'btn-primary' : 'btn-outline', 'btn btn-sm']"
      @click="category = v"
    >
      {{ label }}
    </button>

    <input
      v-model.lazy="search"
      class="input ml-auto w-full sm:w-64"
      placeholder="বিজ্ঞপ্তি খুঁজুন..."
    />
  </div>

  <StateBlock
    :loading="loading"
    :error="error"
    :empty="!data?.data?.length"
  >
    <div class="card divide-y">
      <div
        v-for="n in data.data"
        :key="n.id"
        class="flex flex-wrap items-center justify-between gap-3 px-4 py-3.5"
      >
        <RouterLink
          :to="`/notices/${n.id}`"
          class="min-w-0 flex-1 hover:text-brand"
        >
          <p class="text-xs text-accent">
            {{ n.published_at }} · {{ n.category }}
            <span v-if="n.is_pinned">📌</span>
          </p>

          <p class="font-medium">{{ n.title }}</p>
        </RouterLink>

        <a
          v-if="n.file_url"
          :href="n.file_url"
          target="_blank"
          class="btn btn-outline btn-sm"
        >
          ডাউনলোড করুন
        </a>
      </div>
    </div>

    <Pagination
      :meta="data"
      @change="(p) => (page = p)"
    />
  </StateBlock>
</template>
