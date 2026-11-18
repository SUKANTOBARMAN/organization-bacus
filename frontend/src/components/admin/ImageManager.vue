<script setup>
import { ref, onMounted } from 'vue'
import { Trash2, Upload } from '@lucide/vue'
import api, { errorMessage } from '../../api/client'
import { useToast } from '../../stores/toast'
import StateBlock from '../common/StateBlock.vue'

/** একাধিক ছবি আপলোড ও মোছা (অ্যালবাম / নিউজের ছবি) */
const props = defineProps({
  listUrl: String,
  listParams: Object,
  uploadUrl: String,
  deleteUrl: String, // যেমন /admin/photos  → DELETE {deleteUrl}/{id}
  imageKey: { type: String, default: 'image_url' },
})
const toast = useToast()
const items = ref([])
const loading = ref(true)
const error = ref('')
const uploading = ref(false)

async function load() {
  loading.value = true
  try {
    items.value = (await api.get(props.listUrl, { params: { ...props.listParams, per_page: 100 } })).data.data
  } catch (e) {
    error.value = errorMessage(e)
  } finally {
    loading.value = false
  }
}
onMounted(load)

async function upload(e) {
  const files = [...e.target.files]
  if (!files.length) return
  const fd = new FormData()
  files.forEach((f) => fd.append('images[]', f))
  uploading.value = true
  try {
    await api.post(props.uploadUrl, fd)
    toast.push(`${files.length} টি ছবি আপলোড হয়েছে`)
    e.target.value = ''
    await load()
  } catch (err) {
    toast.error(errorMessage(err, 'আপলোড হয়নি (প্রতিটি ছবি ৫ MB-এর মধ্যে হতে হবে)'))
  } finally {
    uploading.value = false
  }
}

async function remove(item) {
  if (!confirm('ছবিটি মুছে ফেলবেন?')) return
  try {
    await api.delete(`${props.deleteUrl}/${item.id}`)
    items.value = items.value.filter((i) => i.id !== item.id)
  } catch (e) {
    toast.error(errorMessage(e))
  }
}
</script>

<template>
  <div>
    <label class="btn btn-primary mb-5 cursor-pointer">
      <Upload :size="16" /> {{ uploading ? 'আপলোড হচ্ছে...' : 'ছবি যোগ করুন (একাধিক বাছাই করা যায়)' }}
      <input type="file" accept="image/*" multiple class="hidden" :disabled="uploading" @change="upload" />
    </label>

    <StateBlock :loading="loading" :error="error" :empty="!items.length" empty-text="এখনো কোনো ছবি নেই।">
      <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-5">
        <div v-for="i in items" :key="i.id" class="group relative overflow-hidden rounded-lg border bg-white">
          <img :src="i[imageKey]" class="aspect-square w-full object-cover" loading="lazy" />
          <button class="absolute right-2 top-2 rounded-full bg-red-600 p-1.5 text-white opacity-0 transition group-hover:opacity-100" aria-label="মুছে ফেলুন" @click="remove(i)">
            <Trash2 :size="14" />
          </button>
        </div>
      </div>
    </StateBlock>
  </div>
</template>