export default {
  key: 'videos', path: 'videos', endpoint: 'videos',
  title: 'ভিডিও গ্যালারি', subtitle: 'YouTube ভিডিও লিংক যোগ করুন',
  columns: [{ label: 'শিরোনাম', key: 'title' }, { label: 'লিংক', key: 'url' }, { label: 'ক্রম', key: 'sort_order' }, { label: 'প্রকাশিত', key: 'is_published', type: 'bool' }],
  fields: [
    { name: 'title', label: 'শিরোনাম', required: true, full: true },
    { name: 'url', label: 'ইউটিউব লিংক', type: 'url', required: true, full: true },
    { name: 'description', label: 'বিবরণ', type: 'textarea' },
    { name: 'sort_order', label: 'ক্রম', type: 'number', default: 0 },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}