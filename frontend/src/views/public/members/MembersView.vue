
<script setup>
import { ref, watch } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'
import Pagination from '../../../components/common/Pagination.vue'
import Avatar from '../../../components/common/Avatar.vue'

const search = ref('')
const type = ref('')
const page = ref(1)

watch([search, type], () => {
  page.value = 1
})

const { data, loading, error } = useFetch(
  '/members',
  () => ({
    search: search.value || undefined,
    type: type.value || undefined,
    page: page.value
  })
)

const tabs = [
  ['', 'সকল সদস্য'],
  ['general', 'সাধারণ সদস্য'],
  ['life', 'আজীবন সদস্য'],
  ['associate', 'সহযোগী সদস্য'],
  ['honorary', 'সম্মানসূচক সদস্য']
]
</script>

<template>
  <PageTitle
    title="সদস্য তালিকা"
    subtitle="পদমর্যাদার ক্রমানুসারে সাজানো"
  />

  <!-- সদস্যের ধরন নির্বাচন ও অনুসন্ধান -->
  <div class="mb-5 flex flex-wrap items-center gap-2">

    <button
      v-for="[v, l] in tabs"
      :key="v"
      :class="[type === v ? 'btn-primary' : 'btn-outline', 'btn btn-sm']"
      @click="type = v"
    >
      {{ l }}
    </button>

    <input
      v-model.lazy="search"
      class="input ml-auto w-full sm:w-72"
      placeholder="নাম অথবা সদস্য আইডি দিয়ে খুঁজুন"
    />
  </div>

  <!-- সদস্যদের তালিকা -->
  <StateBlock
    :loading="loading"
    :error="error"
    :empty="!data?.data?.length"
    empty-text="কোনো সদস্য পাওয়া যায়নি।"
  >
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

      <div
        v-for="m in data.data"
        :key="m.id"
        class="card flex items-center gap-4 p-4"
      >
        <Avatar
          :src="m.photo_url"
          :name="m.name"
          size="h-16 w-16 text-xl"
        />

        <div class="min-w-0">
          <p class="truncate font-semibold">
            {{ m.name }}
          </p>

          <p
            v-if="m.designation"
            class="text-sm font-medium text-brand"
          >
            {{ m.designation }}
          </p>

          <p class="text-xs text-stone-500">
            সদস্য আইডি: {{ m.member_no }} ·
            {{ m.membership_type }}
          </p>
        </div>
      </div>

    </div>

    <!-- পৃষ্ঠা পরিবর্তন -->
    <Pagination
      :meta="data"
      @change="(p) => (page = p)"
    />

  </StateBlock>
</template>
