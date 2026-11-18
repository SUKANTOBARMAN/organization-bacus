<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/client'

const props = defineProps({ field: Object, modelValue: null, error: String, row: Object })
const emit = defineEmits(['update:modelValue'])

// ---- select: অপশন API থেকে (যেমন পদবি) ----
const optionCache = {}
const options = ref(props.field.options || [])
onMounted(async () => {
  const src = props.field.optionsFrom
  if (!src) return
  if (!optionCache[src.endpoint]) {
    optionCache[src.endpoint] = api.get(`/admin/${src.endpoint}`, { params: { per_page: 100 } }).then((r) => r.data.data)
  }
  const rows = await optionCache[src.endpoint]
  options.value = rows.map((r) => ({ value: r[src.value || 'id'], label: src.label(r) }))
})

// ---- relation: টাইপ করে খুঁজে বাছাই (যেমন সদস্য) ----
const q = ref('')
const results = ref([])
const selectedLabel = ref(props.field.labelFrom ? props.field.labelFrom(props.row) : '')
let timer = null

function search() {
  clearTimeout(timer)
  timer = setTimeout(async () => {
    if (q.value.length < 1) { results.value = []; return }
    const { data } = await api.get(`/admin/${props.field.relation.endpoint}`, { params: { search: q.value, per_page: 8 } })
    results.value = data.data
  }, 250)
}
function pick(r) {
  emit('update:modelValue', r.id)
  selectedLabel.value = props.field.relation.label(r)
  results.value = []
  q.value = ''
}

function onFile(e) { emit('update:modelValue', e.target.files[0] || null) }
</script>

<template>
  <div>
    <label v-if="field.type !== 'checkbox'" class="label">
      {{ field.label }}<span v-if="field.required" class="text-red-500"> *</span>
    </label>

    <textarea v-if="field.type === 'textarea'" class="input" rows="4" :value="modelValue" @input="emit('update:modelValue', $event.target.value)" />

    <select v-else-if="field.type === 'select'" class="input" :value="modelValue" @change="emit('update:modelValue', $event.target.value)">
      <option value="">— বাছাই করুন —</option>
      <option v-for="o in options" :key="o.value" :value="o.value">{{ o.label }}</option>
    </select>

    <label v-else-if="field.type === 'checkbox'" class="flex items-center gap-2 pt-1 text-sm">
      <input type="checkbox" class="h-4 w-4 rounded border-stone-300 text-brand" :checked="modelValue" @change="emit('update:modelValue', $event.target.checked)" />
      {{ field.label }}
    </label>

    <div v-else-if="field.type === 'file'">
      <input type="file" class="block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-stone-100 file:px-3 file:py-2" :accept="field.accept" @change="onFile" />
      <a v-if="row && row[field.name + '_url']" :href="row[field.name + '_url']" target="_blank" class="mt-1 inline-block text-xs text-brand underline">বর্তমান ফাইল দেখুন</a>
    </div>

    <div v-else-if="field.type === 'relation'" class="relative">
      <input v-model="q" class="input" placeholder="নাম বা নম্বর লিখে খুঁজুন" @input="search" />
      <ul v-if="results.length" class="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-md border bg-white shadow">
        <li v-for="r in results" :key="r.id" class="cursor-pointer px-3 py-2 text-sm hover:bg-stone-50" @click="pick(r)">{{ field.relation.label(r) }}</li>
      </ul>
      <p v-if="modelValue" class="mt-1 text-xs text-brand">নির্বাচিত: {{ selectedLabel || '#' + modelValue }}</p>
    </div>

    <input
      v-else
      class="input"
      :type="field.type === 'datetime' ? 'datetime-local' : field.type || 'text'"
      :step="field.step"
      :value="modelValue"
      @input="emit('update:modelValue', $event.target.value)"
    />

    <p v-if="field.help" class="mt-1 text-xs text-stone-500">{{ field.help }}</p>
    <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
  </div>
</template>
