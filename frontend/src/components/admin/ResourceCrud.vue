<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { Plus, Pencil, Trash2, Search, Download } from '@lucide/vue'
import api, { errorMessage } from '../../api/client'
import { useToast } from '../../stores/toast'
import Modal from '../common/Modal.vue'
import Pagination from '../common/Pagination.vue'
import StateBlock from '../common/StateBlock.vue'
import FormField from './FormField.vue'

/**
 * কনফিগ-চালিত CRUD: তালিকা + সার্চ + ফিল্টার + পেজিনেশন + তৈরি/সম্পাদনা (ফাইলসহ) + মুছুন।
 * কনফিগ: src/config/resources/*.js
 */
const props = defineProps({ config: Object, parentId: [String, Number] })
const emit = defineEmits(['changed'])
const toast = useToast()

const rows = ref([])
const meta = ref({})
const loading = ref(true)
const error = ref('')
const page = ref(1)
const search = ref('')
const filters = reactive({})

const showForm = ref(false)
const editing = ref(null)
const form = reactive({})
const errors = ref({})
const saving = ref(false)
const confirmRow = ref(null)

const endpoint = computed(() => `/admin/${props.config.endpoint}`)
const fields = computed(() => props.config.fields || [])

async function load() {
  loading.value = true
  error.value = ''
  try {
    const params = { page: page.value, search: search.value, ...filters }
    if (props.parentId) params[props.config.parentField] = props.parentId
    Object.keys(params).forEach((k) => (params[k] === '' || params[k] == null) && delete params[k])
    const { data } = await api.get(endpoint.value, { params })
    rows.value = data.data
    meta.value = data
  } catch (e) {
    error.value = errorMessage(e, 'তথ্য আনা যায়নি')
  } finally {
    loading.value = false
  }
}

let timer = null
watch(search, () => { clearTimeout(timer); timer = setTimeout(() => { page.value = 1; load() }, 300) })
watch(filters, () => { page.value = 1; load() })
onMounted(load)

const get = (row, path) => path.split('.').reduce((o, k) => (o == null ? o : o[k]), row)

function cell(row, col) {
  if (col.render) return col.render(row)
  const v = get(row, col.key)
  if (col.type === 'date') return v ? String(v).slice(0, 10) : '—'
  if (col.type === 'datetime') return v ? new Date(v).toLocaleString() : '—'
  if (col.type === 'money') return v == null ? '—' : Number(v).toLocaleString('en-BD', { minimumFractionDigits: 2 })
  return v ?? '—'
}

const badgeClass = (v) => {
  if (['active', 'approved', 'published', 'completed', 'ongoing'].includes(v)) return 'bg-green-100 text-green-800'
  if (['pending', 'upcoming'].includes(v)) return 'bg-amber-100 text-amber-800'
  if (['inactive', 'rejected', 'deleted'].includes(v)) return 'bg-red-100 text-red-800'
  return 'bg-stone-100 text-stone-700'
}

const toLocalInput = (iso) => {
  const d = new Date(iso)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

function openForm(row = null) {
  editing.value = row
  errors.value = {}
  Object.keys(form).forEach((k) => delete form[k])
  for (const f of fields.value) {
    let v = row ? get(row, f.name) : f.default
    if (f.type === 'checkbox') v = row ? !!v : (f.default ?? false)
    else if (f.type === 'file') v = null
    else if (f.type === 'datetime' && v) v = toLocalInput(v)
    else if (f.type === 'date' && v) v = String(v).slice(0, 10)
    else if (v == null) v = ''
    form[f.name] = v
  }
  showForm.value = true
}

async function save() {
  saving.value = true
  errors.value = {}
  const fd = new FormData()
  const clearable = ['text', 'textarea', 'date', 'datetime', 'url', 'email']

  for (const f of fields.value) {
    if (f.showIf && !f.showIf(form)) continue
    const v = form[f.name]
    if (f.type === 'file') { if (v instanceof File) fd.append(f.name, v); continue }
    if (f.type === 'checkbox') { fd.append(f.name, v ? '1' : '0'); continue }
    if (v === '' || v == null) {
      if (editing.value && clearable.includes(f.type || 'text') && !f.required) fd.append(f.name, '')
      continue
    }
    fd.append(f.name, f.type === 'datetime' ? String(v).replace('T', ' ') : v)
  }
  if (props.parentId) fd.append(props.config.parentField, props.parentId)

  try {
    if (editing.value) {
      fd.append('_method', 'PUT') // PHP multipart-এ PUT পার্স করে না
      await api.post(`${endpoint.value}/${editing.value.id}`, fd)
    } else {
      await api.post(endpoint.value, fd)
    }
    toast.push(editing.value ? 'আপডেট হয়েছে' : 'তৈরি হয়েছে')
    showForm.value = false
    await load()
    emit('changed')
  } catch (e) {
    if (e.response?.status === 422) errors.value = Object.fromEntries(Object.entries(e.response.data.errors || {}).map(([k, v]) => [k, v[0]]))
    toast.error(errorMessage(e, 'সংরক্ষণ করা যায়নি'))
  } finally {
    saving.value = false
  }
}

async function remove() {
  try {
    await api.delete(`${endpoint.value}/${confirmRow.value.id}`)
    toast.push('মুছে ফেলা হয়েছে')
    confirmRow.value = null
    if (rows.value.length === 1 && page.value > 1) page.value--
    await load()
    emit('changed')
  } catch (e) {
    toast.error(errorMessage(e, 'মোছা যায়নি'))
  }
}

async function exportCsv() {
  const res = await api.get(props.config.exportUrl, { responseType: 'blob' })
  const url = URL.createObjectURL(res.data)
  const a = document.createElement('a')
  a.href = url
  a.download = `${props.config.endpoint}.csv`
  a.click()
  URL.revokeObjectURL(url)
}
</script>

<template>
  <div>
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="font-serif text-2xl font-bold text-brand-dark">{{ config.title }}</h1>
        <p v-if="config.subtitle" class="text-sm text-stone-500">{{ config.subtitle }}</p>
      </div>
      <div class="flex gap-2">
        <button v-if="config.exportUrl" class="btn btn-outline" @click="exportCsv"><Download :size="16" /> সিএসভি (CSV) এক্সপোর্ট</button>
        <button v-if="!config.readonly" class="btn btn-primary" @click="openForm()"><Plus :size="16" /> নতুন যোগ করুন</button>
      </div>
    </div>

    <div class="card mb-4 flex flex-wrap items-center gap-3 p-3">
      <div class="relative min-w-52 flex-1">
        <Search :size="16" class="absolute left-3 top-2.5 text-stone-400" />
        <input v-model="search" class="input pl-9" placeholder="খুঁজুন..." />
      </div>
      <select v-for="f in config.filters || []" :key="f.name" v-model="filters[f.name]" class="input w-auto">
        <option value="">{{ f.label }}</option>
        <option v-for="o in f.options" :key="o.value" :value="o.value">{{ o.label }}</option>
      </select>
    </div>

    <StateBlock :loading="loading" :error="error" :empty="!rows.length" empty-text="কোনো তথ্য পাওয়া যায়নি।">
      <div class="card overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr>
              <th v-for="c in config.columns" :key="c.label" class="th">{{ c.label }}</th>
              <th v-if="!config.readonly || config.actions" class="th text-right">কার্যক্রম</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="row in rows" :key="row.id" class="hover:bg-stone-50">
              <td v-for="c in config.columns" :key="c.label" class="td">
                <template v-if="c.type === 'image'">
                  <img v-if="get(row, c.key)" :src="get(row, c.key)" class="h-10 w-10 rounded object-cover" />
                  <span v-else class="grid h-10 w-10 place-items-center rounded bg-stone-100 text-xs text-stone-400">—</span>
                </template>
                <span v-else-if="c.type === 'bool'" :class="get(row, c.key) ? 'text-green-600' : 'text-stone-300'">{{ get(row, c.key) ? '✔' : '✖' }}</span>
                <span v-else-if="c.type === 'badge'" :class="[badgeClass(get(row, c.key)), 'rounded-full px-2.5 py-0.5 text-xs font-medium']">{{ get(row, c.key) ?? '—' }}</span>
                <template v-else-if="c.type === 'file'">
                  <a v-if="get(row, c.key)" :href="get(row, c.key)" target="_blank" class="text-brand underline">খুলুন</a>
                  <span v-else class="text-stone-400">—</span>
                </template>
                <span v-else :class="c.class">{{ cell(row, c) }}</span>
              </td>
              <td v-if="!config.readonly || config.actions" class="td">
                <div class="flex justify-end gap-1.5">
                  <RouterLink v-for="a in config.actions || []" :key="a.label" :to="a.to(row)" class="btn btn-outline btn-sm">{{ a.label }}</RouterLink>
                  <template v-if="!config.readonly">
                    <button class="btn btn-outline btn-sm" aria-label="সম্পাদনা করুন" @click="openForm(row)"><Pencil :size="14" /></button>
                    <button class="btn btn-outline btn-sm text-red-600" aria-label="মুছে ফেলুন" @click="confirmRow = row"><Trash2 :size="14" /></button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination :meta="meta" @change="(p) => { page = p; load() }" />
    </StateBlock>

    <Modal v-if="showForm" :title="(editing ? 'সম্পাদনা: ' : 'নতুন যোগ করুন: ') + config.title" wide @close="showForm = false">
      <form id="crud-form" class="grid gap-4 sm:grid-cols-2" @submit.prevent="save">
        <template v-for="f in fields" :key="f.name">
          <div v-if="!f.showIf || f.showIf(form)" :class="{ 'sm:col-span-2': f.full || f.type === 'textarea' }">
            <FormField v-model="form[f.name]" :field="f" :error="errors[f.name]" :row="editing" />
          </div>
        </template>
      </form>
      <template #footer>
        <button class="btn btn-outline" type="button" @click="showForm = false">বাতিল</button>
        <button class="btn btn-primary" form="crud-form" type="submit" :disabled="saving">{{ saving ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}</button>
      </template>
    </Modal>

    <Modal v-if="confirmRow" title="মুছে ফেলবেন?" @close="confirmRow = null">
      <p class="text-sm text-stone-600">এটি স্থায়ীভাবে মুছে যাবে এবং ফিরিয়ে আনা যাবে না।</p>
      <template #footer>
        <button class="btn btn-outline" @click="confirmRow = null">বাতিল</button>
        <button class="btn btn-danger" @click="remove">মুছে ফেলুন</button>
      </template>
    </Modal>
  </div>
</template>