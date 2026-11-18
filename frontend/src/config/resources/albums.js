export default {
  key: 'albums', path: 'albums', endpoint: 'albums',
  title: 'ফটো গ্যালারি', subtitle: 'অ্যালবাম তৈরি করে "ছবিসমূহ" থেকে ছবি আপলোড করুন',
  columns: [
    { label: 'কভার', key: 'cover_image_url', type: 'image' }, { label: 'শিরোনাম', key: 'title' },
    { label: 'ইভেন্টের তারিখ', key: 'event_date', type: 'date' }, { label: 'প্রকাশিত', key: 'is_published', type: 'bool' },
  ],
  actions: [{ label: 'ছবিসমূহ', to: (r) => `/admin/albums/${r.id}/photos` }],
  fields: [
    { name: 'title', label: 'অ্যালবামের শিরোনাম', required: true, full: true },
    { name: 'event_date', label: 'ইভেন্টের তারিখ', type: 'date' },
    { name: 'description', label: 'বিবরণ', type: 'textarea' },
    { name: 'cover_image', label: 'কভার ইমেজ', type: 'file', accept: 'image/*', full: true },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}