export default {
  key: 'sliders', path: 'sliders', endpoint: 'sliders',
  title: 'হোম স্লাইডার', subtitle: 'হোম পেজের ব্যানার (প্রস্তাবিত মাপ ১৬০০×৬০০)',
  columns: [
    { label: 'ছবি', key: 'image_url', type: 'image' }, { label: 'শিরোনাম', key: 'title' },
    { label: 'ক্রম', key: 'sort_order' }, { label: 'সক্রিয়', key: 'is_active', type: 'bool' },
  ],
  fields: [
    { name: 'image', label: 'ব্যানার ছবি', type: 'file', accept: 'image/*', full: true, help: 'নতুন হলে বাধ্যতামূলক' },
    { name: 'title', label: 'প্রধান শিরোনাম' },
    { name: 'subtitle', label: 'উপ-শিরোনাম' },
    { name: 'link_url', label: 'লিংক (ঐচ্ছিক)', full: true },
    { name: 'sort_order', label: 'ক্রম', type: 'number', default: 0 },
    { name: 'is_active', label: 'সক্রিয়', type: 'checkbox', default: true },
  ],
}