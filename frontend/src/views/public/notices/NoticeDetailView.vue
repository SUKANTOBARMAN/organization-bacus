
<script setup>
import { useRoute } from 'vue-router'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const route = useRoute()
const { data, loading, error } = useFetch(`/notices/${route.params.id}`)
</script>

<template>
  <!-- সকল বিজ্ঞপ্তিতে ফিরে যাওয়া -->
  <RouterLink
    to="/notices"
    class="mb-4 inline-block text-sm text-brand underline"
  >
    ← সকল বিজ্ঞপ্তিতে ফিরে যান
  </RouterLink>

  <StateBlock :loading="loading" :error="error">
    <article
      v-if="data"
      class="max-w-3xl"
    >
      <!-- বিজ্ঞপ্তির শিরোনাম ও তথ্য -->
      <PageTitle
        :title="data.title"
        :subtitle="`${data.published_at} · ${data.category}`"
      />

      <!-- বিজ্ঞপ্তির বিস্তারিত বিবরণ -->
      <p class="whitespace-pre-line leading-relaxed">
        {{ data.body }}
      </p>

      <!-- সংযুক্ত ফাইল ডাউনলোড -->
      <a
        v-if="data.file_url"
        :href="data.file_url"
        target="_blank"
        class="btn btn-primary mt-6"
      >
        সংযুক্তি ডাউনলোড করুন
      </a>
    </article>
  </StateBlock>
</template>
