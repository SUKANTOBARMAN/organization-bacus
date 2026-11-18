const types = [
  { value: 'constitution', label: 'সংবিধান' }, { value: 'rules', label: 'বিধিমালা' },
  { value: 'minutes', label: 'সভার কার্যবিবরণী' }, { value: 'annual_report', label: 'বার্ষিক প্রতিবেদন' },
  { value: 'form', label: 'ফরম' }, { value: 'download', label: 'অন্যান্য ডাউনলোড' },
]

export default {
  key: 'documents', path: 'documents', endpoint: 'documents',
  title: 'সংবিধান ও ডকুমেন্টস', subtitle: 'গঠনতন্ত্র, বিধিমালা, সভার কার্যবিবরণী, বার্ষিক প্রতিবেদন, ফর্ম',
  columns: [
    { label: 'শিরোনাম', key: 'title' }, { label: 'ধরন', key: 'type', type: 'badge' },
    { label: 'ফাইল', key: 'file_url', type: 'file' }, { label: 'ক্রম', key: 'sort_order' },
    { label: 'প্রকাশিত', key: 'is_published', type: 'bool' },
  ],
  filters: [{ name: 'type', label: 'সকল ধরন', options: types }],
  fields: [
    { name: 'title', label: 'শিরোনাম', required: true, full: true },
    { name: 'type', label: 'ধরন', type: 'select', default: 'constitution', options: types },
    { name: 'sort_order', label: 'ক্রম', type: 'number', default: 0 },
    { name: 'description', label: 'বিবরণ', type: 'textarea' },
    { name: 'file', label: 'ফাইল (PDF/Word/Excel)', type: 'file', accept: '.pdf,.doc,.docx,.xls,.xlsx', full: true, help: 'নতুন হলে বাধ্যতামূলক' },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}