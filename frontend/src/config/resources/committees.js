export default {
  key: 'committees', path: 'committees', endpoint: 'committees',
  title: 'কমিটিসমূহ', subtitle: 'প্রতিটি মেয়াদের কমিটি। শুধু একটি "বর্তমান" থাকে, বাকিগুলো আর্কাইভ',
  columns: [
    { label: 'শিরোনাম', key: 'title' }, { label: 'ধরন', key: 'type', type: 'badge' },
    { label: 'মেয়াদ', key: 'term' }, { label: 'বর্তমান', key: 'is_current', type: 'bool' },
    { label: 'সদস্য সংখ্যা', key: 'members', render: (r) => r.members?.length ?? 0 },
  ],
  filters: [{ name: 'type', label: 'সকল ধরন', options: [{ value: 'executive', label: 'কার্যকরী কমিটি' }, { value: 'advisory', label: 'উপদেষ্টা কমিটি' }, { value: 'sub_committee', label: 'উপ-কমিটি' }] }],
  actions: [{ label: 'সদস্যবৃন্দ', to: (r) => `/admin/committees/${r.id}/members` }],
  fields: [
    { name: 'title', label: 'শিরোনাম', required: true, default: 'কার্যকরী কমিটি' },
    { name: 'type', label: 'ধরন', type: 'select', default: 'executive', options: [{ value: 'executive', label: 'কার্যকরী কমিটি' }, { value: 'advisory', label: 'উপদেষ্টা কমিটি' }, { value: 'sub_committee', label: 'উপ-কমিটি' }] },
    { name: 'term_start', label: 'মেয়াদ শুরু (বছর)', type: 'number', required: true, default: new Date().getFullYear() },
    { name: 'term_end', label: 'মেয়াদ শেষ (বছর)', type: 'number', required: true, default: new Date().getFullYear() + 1 },
    { name: 'description', label: 'বিবরণ', type: 'textarea' },
    { name: 'is_current', label: 'বর্তমান কমিটি', type: 'checkbox' },
  ],
}