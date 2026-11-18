<script setup>
import { ref, onMounted } from 'vue'
import api from '../../../api/client'
import { resources } from '../../../config/resources'
import ResourceCrud from '../../../components/admin/ResourceCrud.vue'

const year = ref(new Date().getFullYear())
const summary = ref(null)
const months = ['জানু', 'ফেব্রু', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টে', 'অক্টো', 'নভে', 'ডিসে']

async function loadSummary() {
  summary.value = (await api.get('/admin/payments/summary', { params: { year: year.value } })).data
}
onMounted(loadSummary)

const money = (n) => Number(n || 0).toLocaleString('en-BD')
</script>

<script>
const typeLabels = {
  subscription: 'সদস্য চাঁদা',
  donation: 'অনুদান',
  admission_fee: 'ভর্তি ফি',
  other: 'অন্যান্য',
}

const methodLabels = {
  cash: 'নগদ (Cash)',
  bkash: 'বিকাশ (bKash)',
  nagad: 'নগদ (Nagad)',
  bank: 'ব্যাংক',
  other: 'অন্যান্য',
}

// পেমেন্ট রিসোর্সের কনফিগ এখানেই (সদস্য নির্বাচনের relation ফিল্ডসহ)
export const paymentConfig = {
  key: 'payments', endpoint: 'payments',
  title: 'পেমেন্ট ও বকেয়া', subtitle: 'সদস্য চাঁদা, ভর্তি ফি ও অনুদানের খাতা',
  columns: [
    { label: 'তারিখ', key: 'paid_at', type: 'date' },
    { label: 'সদস্য', key: 'member.name', render: (r) => (r.member ? `${r.member.member_no} — ${r.member.name}` : '—') },
    { label: 'ধরন', key: 'type', type: 'badge' }, { label: 'সময়কাল', key: 'period' },
    { label: 'পরিমাণ (৳)', key: 'amount', type: 'money', class: 'font-semibold' },
    { label: 'মাধ্যম', key: 'method' }, { label: 'রেফারেন্স', key: 'reference' },
  ],
  filters: [
    { name: 'type', label: 'সব ধরন', options: Object.entries(typeLabels).map(([value, label]) => ({ value, label })) },
    { name: 'method', label: 'সব মাধ্যম', options: Object.entries(methodLabels).map(([value, label]) => ({ value, label })) },
  ],
  fields: [
    { name: 'member_id', label: 'সদস্য', type: 'relation', required: true, full: true,
      relation: { endpoint: 'members', label: (r) => `${r.member_no} — ${r.name}` },
      labelFrom: (row) => (row?.member ? `${row.member.member_no} — ${row.member.name}` : '') },
    { name: 'type', label: 'ধরন', type: 'select', default: 'subscription', options: Object.entries(typeLabels).map(([value, label]) => ({ value, label })) },
    { name: 'amount', label: 'পরিমাণ (৳)', type: 'number', step: '0.01', required: true },
    { name: 'paid_at', label: 'পরিশোধের তারিখ', type: 'date', required: true, default: new Date().toISOString().slice(0, 10) },
    { name: 'period', label: 'সময়কাল (যেমন: 2026-11 / 2026)' },
    { name: 'method', label: 'মাধ্যম', type: 'select', default: 'cash', options: Object.entries(methodLabels).map(([value, label]) => ({ value, label })) },
    { name: 'reference', label: 'ট্রানজেকশন / রসিদ নং' },
    { name: 'note', label: 'মন্তব্য', type: 'textarea' },
  ],
}
</script>

<template>
  <div v-if="summary" class="mb-6 grid gap-4 lg:grid-cols-3">
    <div class="card p-4">
      <div class="flex items-center justify-between">
        <p class="text-sm text-stone-500">মোট সংগৃহীত</p>
        <select v-model="year" class="rounded border px-2 py-1 text-sm" @change="loadSummary">
          <option v-for="y in [year + 1, year, year - 1, year - 2]" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>
      <p class="mt-1 font-serif text-3xl font-bold text-brand-dark">৳ {{ money(summary.total) }}</p>
      <ul class="mt-3 space-y-1 text-sm">
        <li v-for="(v, k) in summary.by_type" :key="k" class="flex justify-between"><span class="text-stone-500">{{ k }}</span><span>৳ {{ money(v) }}</span></li>
      </ul>
    </div>
    <div class="card p-4 lg:col-span-2">
      <p class="mb-3 text-sm text-stone-500">মাসিক সংগ্রহ</p>
      <div class="flex h-28 items-end gap-2">
        <div v-for="(v, i) in summary.by_month" :key="i" class="flex flex-1 flex-col items-center gap-1">
          <div class="w-full rounded-t bg-brand" :style="{ height: Math.max(2, (v / Math.max(...summary.by_month, 1)) * 100) + '%' }" :title="'৳ ' + money(v)" />
          <span class="text-[10px] text-stone-500">{{ months[i] }}</span>
        </div>
      </div>
    </div>
  </div>
  <ResourceCrud :config="paymentConfig" @changed="loadSummary" />
</template>