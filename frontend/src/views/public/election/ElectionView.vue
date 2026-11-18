
<script setup>
import { ref } from 'vue'
import api from '../../../api/client'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'
import Avatar from '../../../components/common/Avatar.vue'

const { data, loading, error } = useFetch('/commission')
const open = ref(null)
const detail = ref(null)

async function toggle(e) {
  if (open.value === e.id) {
    open.value = null
    return
  }
  open.value = e.id
  detail.value = (await api.get(`/elections/${e.id}`)).data
}

const fmt = (d) => (d
  ? new Date(d).toLocaleDateString('bn-BD', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })
  : 'নির্ধারিত হয়নি')
</script>

<template>
  <PageTitle title="নির্বাচন কমিশন" />

  <StateBlock :loading="loading" :error="error">
    <template v-if="data">
      <!-- নির্বাচন কমিশনের সদস্য -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="m in data.members"
          :key="m.id"
          class="card flex items-center gap-3 p-4"
        >
          <Avatar :src="m.photo_url" :name="m.name" />
          <div>
            <p class="font-semibold">{{ m.name }}</p>
            <p class="text-sm text-stone-500">{{ m.designation }}</p>
          </div>
        </div>
      </div>

      <!-- নির্বাচনসমূহ -->
      <h2 class="mb-3 mt-10 border-b pb-2 font-serif text-2xl font-bold text-brand-dark">
        নির্বাচনসমূহ
      </h2>

      <p v-if="!data.elections.length" class="text-stone-500">
        এখনো কোনো নির্বাচন প্রকাশ করা হয়নি।
      </p>

      <div class="space-y-3">
        <div
          v-for="e in data.elections"
          :key="e.id"
          class="card"
        >
          <button
            class="flex w-full items-center justify-between gap-4 p-4 text-left"
            @click="toggle(e)"
          >
            <span>
              <span class="font-semibold">{{ e.title }}</span>
              <br />
              <span class="text-sm text-stone-500">
                নির্বাচনের তারিখ: {{ fmt(e.election_date) }}
              </span>
            </span>

            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs">
              {{ e.status }}
            </span>
          </button>

          <!-- নির্বাচনের বিস্তারিত তথ্য -->
          <div
            v-if="open === e.id && detail"
            class="border-t p-4 text-sm"
          >
            <p v-if="detail.description" class="mb-3">
              {{ detail.description }}
            </p>

            <table
              v-if="detail.candidates.length"
              class="w-full text-left"
            >
              <thead class="text-xs text-stone-500">
                <tr>
                  <th class="py-1">পদবি</th>
                  <th>প্রার্থীর নাম</th>
                  <th>প্রাপ্ত ভোট</th>
                  <th>ফলাফল</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="c in detail.candidates"
                  :key="c.id"
                  class="border-t"
                >
                  <td class="py-2">{{ c.position }}</td>
                  <td>{{ c.name }}</td>
                  <td>{{ c.votes }}</td>
                  <td>
                    <span
                      v-if="c.is_winner"
                      class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800"
                    >
                      বিজয়ী
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <p v-if="detail.result" class="mt-3 whitespace-pre-line">
              {{ detail.result }}
            </p>

            <a
              v-if="detail.result_file_url"
              :href="detail.result_file_url"
              target="_blank"
              class="btn btn-outline mt-3"
            >
              নির্বাচনের ফলাফল ডাউনলোড করুন
            </a>
          </div>
        </div>
      </div>
    </template>
  </StateBlock>
</template>
