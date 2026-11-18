<script setup>
import { ref, computed } from 'vue'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'
import Avatar from '../../../components/common/Avatar.vue'

const type = ref('executive')
const tabs = [['executive', 'কার্যনির্বাহী কমিটি'], ['advisory', 'উপদেষ্টা পরিষদ']]
const { data, loading, error } = useFetch('/committee', () => ({ type: type.value }))
const { data: archive } = useFetch('/committees', () => ({ type: type.value }))

const selectedId = ref('')
const { data: past, loading: pastLoading } = useFetch(
  () => (selectedId.value ? `/committees/${selectedId.value}` : '/committee'),
  () => ({ type: type.value }),
)
const shown = computed(() => (selectedId.value ? past.value : data.value))
const pastTerms = computed(() => (archive.value || []).filter((c) => !c.is_current))
</script>

<template>
  <PageTitle title="কার্যনির্বাহী কমিটি ও পরিষদ" subtitle="পদবির ক্রম অনুযায়ী" />

  <div class="mb-6 flex flex-wrap items-center gap-2">
    <button v-for="[v, label] in tabs" :key="v" :class="[type === v ? 'btn-primary' : 'btn-outline', 'btn']" @click="type = v; selectedId = ''">{{ label }}</button>
    <select v-if="pastTerms.length" v-model="selectedId" class="input ml-auto w-auto">
      <option value="">বর্তমান মেয়اد</option>
      <option v-for="c in pastTerms" :key="c.id" :value="c.id">আর্কাইভ: {{ c.term }}</option>
    </select>
  </div>

  <StateBlock :loading="loading || pastLoading" :error="error" :empty="!shown" empty-text="এই তালিকা এখনো প্রকাশ করা হয়নি।">
    <div v-if="shown">
      <h2 class="mb-4 font-serif text-xl font-bold">{{ shown.title }} <span class="text-accent">{{ shown.term }}</span></h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="m in shown.members" :key="m.id" class="card flex flex-col items-center p-5 text-center">
          <Avatar :src="m.photo_url" :name="m.name" size="h-24 w-24 text-2xl" />
          <h3 class="mt-3 font-semibold">{{ m.name }}</h3>
          <p class="text-sm font-medium text-brand">{{ m.designation }}</p>
          <p v-if="m.phone" class="mt-1 text-xs text-stone-500">{{ m.phone }}</p>
        </div>
      </div>
    </div>
  </StateBlock>
</template>