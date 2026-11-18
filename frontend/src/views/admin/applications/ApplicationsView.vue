<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import api, { errorMessage } from '../../../api/client'
import { useToast } from '../../../stores/toast'
import Modal from '../../../components/common/Modal.vue'
import Pagination from '../../../components/common/Pagination.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const toast = useToast()
const rows = ref([])
const meta = ref({})
const page = ref(1)
const status = ref('pending')
const loading = ref(true)
const error = ref('')
const current = ref(null)
const designations = ref([])
const decision = reactive({ designation_id: '', review_note: '' })
const busy = ref(false)

async function load() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/membership-applications', { params: { page: page.value, status: status.value || undefined } })
    rows.value = data.data
    meta.value = data
  } catch (e) { error.value = errorMessage(e) } finally { loading.value = false }
}
onMounted(async () => {
  load()
  designations.value = (await api.get('/admin/designations', { params: { per_page: 100 } })).data.data
})
watch(status, () => { page.value = 1; load() })

function open(a) { current.value = a; decision.designation_id = ''; decision.review_note = '' }

async function act(kind) {
  busy.value = true
  try {
    await api.post(`/admin/membership-applications/${current.value.id}/${kind}`, {
      designation_id: decision.designation_id || undefined,
      review_note: decision.review_note || undefined,
    })
    toast.push(kind === 'approve' ? 'অনুমোদিত — সদস্য তৈরি হয়েছে' : 'বাতিল করা হয়েছে')
    current.value = null
    load()
  } catch (e) { toast.error(errorMessage(e)) } finally { busy.value = false }
}

const rowsInfo = [['ফোন', 'phone'], ['ইমেইল', 'email'], ['ধরন', 'membership_type'], ['পেশা', 'profession'], ['কর্মক্ষেত্র', 'workplace'], ['রক্তের গ্রুপ', 'blood_group'], ['ঠিকানা', 'address']]
</script>

<template>
  <div class="mb-5 flex items-center justify-between">
    <h1 class="font-serif text-2xl font-bold text-brand-dark">সদস্যপদের আবেদনসমূহ</h1>
    <select v-model="status" class="input w-auto">
      <option value="pending">অপেক্ষমাণ (Pending)</option><option value="approved">অনুমোদিত (Approved)</option><option value="rejected">বাতিলকৃত (Rejected)</option><option value="">সব</option>
    </select>
  </div>

  <StateBlock :loading="loading" :error="error" :empty="!rows.length" empty-text="কোনো আবেদন নেই।">
    <div class="card divide-y">
      <button v-for="a in rows" :key="a.id" class="flex w-full items-center justify-between gap-4 px-4 py-3 text-left hover:bg-stone-50" @click="open(a)">
        <span><span class="font-medium">{{ a.name }}</span> <span class="text-sm text-stone-500">· {{ a.phone }}</span></span>
        <span class="flex items-center gap-3 text-xs">
          <span class="text-stone-400">{{ new Date(a.created_at).toLocaleDateString() }}</span>
          <span class="rounded-full bg-stone-100 px-2.5 py-0.5">{{ a.status }}</span>
        </span>
      </button>
    </div>
    <Pagination :meta="meta" @change="(p) => { page = p; load() }" />
  </StateBlock>

  <Modal v-if="current" :title="current.name" @close="current = null">
    <img v-if="current.photo_url" :src="current.photo_url" class="mb-4 h-24 w-24 rounded-full object-cover" />
    <dl class="grid grid-cols-3 gap-y-2 text-sm">
      <template v-for="[label, key] in rowsInfo" :key="key"><dt class="text-stone-500">{{ label }}</dt><dd class="col-span-2">{{ current[key] || '—' }}</dd></template>
    </dl>
    <p v-if="current.message" class="mt-3 rounded bg-stone-50 p-3 text-sm">{{ current.message }}</p>

    <div v-if="current.status === 'pending'" class="mt-5 space-y-3 border-t pt-4">
      <div>
        <label class="label">পদবি (অনুমোদন করলে)</label>
        <select v-model="decision.designation_id" class="input">
          <option value="">— পরে ঠিক করব —</option>
          <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>
      </div>
      <div><label class="label">মন্তব্য (Note)</label><input v-model="decision.review_note" class="input" /></div>
    </div>
    <p v-else class="mt-4 text-sm text-stone-500">স্ট্যাটাস: <strong>{{ current.status }}</strong> <span v-if="current.member">→ সদস্য নং #{{ current.member.member_no }}</span></p>

    <template v-if="current.status === 'pending'" #footer>
      <button class="btn btn-outline text-red-600" :disabled="busy" @click="act('reject')">বাতিল করুন (Reject)</button>
      <button class="btn btn-primary" :disabled="busy" @click="act('approve')">অনুমোদন ও সদস্য তৈরি করুন</button>
    </template>
  </Modal>
</template>