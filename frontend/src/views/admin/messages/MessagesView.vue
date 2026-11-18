<script setup>
import { ref, onMounted } from 'vue'
import api, { errorMessage } from '../../../api/client'
import { useToast } from '../../../stores/toast'
import Modal from '../../../components/common/Modal.vue'
import Pagination from '../../../components/common/Pagination.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const toast = useToast()
const rows = ref([])
const meta = ref({})
const page = ref(1)
const loading = ref(true)
const error = ref('')
const current = ref(null)

async function load() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/contact-messages', { params: { page: page.value } })
    rows.value = data.data
    meta.value = data
  } catch (e) { error.value = errorMessage(e) } finally { loading.value = false }
}
onMounted(load)

async function open(m) {
  current.value = (await api.get(`/admin/contact-messages/${m.id}`)).data
  m.read_at = current.value.read_at
}
async function remove() {
  await api.delete(`/admin/contact-messages/${current.value.id}`)
  toast.push('মুছে ফেলা হয়েছে')
  current.value = null
  load()
}
</script>

<template>
  <h1 class="mb-5 font-serif text-2xl font-bold text-brand-dark">বার্তা সমূহের তালিকা</h1>
  <StateBlock :loading="loading" :error="error" :empty="!rows.length" empty-text="কোনো বার্তা নেই।">
    <div class="card divide-y">
      <button v-for="m in rows" :key="m.id" class="flex w-full items-center justify-between gap-4 px-4 py-3 text-left hover:bg-stone-50" @click="open(m)">
        <span :class="{ 'font-semibold': !m.read_at }">{{ m.name }} <span class="font-normal text-stone-500">— {{ m.subject || m.message.slice(0, 60) }}</span></span>
        <span class="shrink-0 text-xs text-stone-400">{{ new Date(m.created_at).toLocaleDateString() }}</span>
      </button>
    </div>
    <Pagination :meta="meta" @change="(p) => { page = p; load() }" />
  </StateBlock>

  <Modal v-if="current" :title="current.subject || 'বার্তা'" @close="current = null">
    <p class="mb-1 text-sm"><strong>{{ current.name }}</strong> · <a :href="`mailto:${current.email}`" class="text-brand underline">{{ current.email }}</a> <span v-if="current.phone">· {{ current.phone }}</span></p>
    <p class="mb-4 text-xs text-stone-400">{{ new Date(current.created_at).toLocaleString() }}</p>
    <p class="whitespace-pre-line text-sm">{{ current.message }}</p>
    <template #footer>
      <button class="btn btn-danger" @click="remove">মুছে ফেলুন</button>
      <button class="btn btn-outline" @click="current = null">বন্ধ করুন</button>
    </template>
  </Modal>
</template>