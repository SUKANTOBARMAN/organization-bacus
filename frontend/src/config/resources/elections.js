const status = [{ value: 'upcoming', label: 'আসন্ন' }, { value: 'ongoing', label: 'চলমান' }, { value: 'completed', label: 'সম্পন্ন' }]

export default {
  key: 'elections', path: 'elections', endpoint: 'elections',
  title: 'নির্বাচনসমূহ', subtitle: 'নির্বাচনের সময়সূচি, প্রার্থী ও ফলাফল',
  columns: [
    { label: 'শিরোনাম', key: 'title' }, { label: 'নির্বাচনের তারিখ', key: 'election_date', type: 'date' },
    { label: 'অবস্থা', key: 'status', type: 'badge' }, { label: 'প্রকাশিত', key: 'is_published', type: 'bool' },
  ],
  filters: [{ name: 'status', label: 'সকল অবস্থা', options: status }],
  actions: [{ label: 'প্রার্থীবৃন্দ', to: (r) => `/admin/elections/${r.id}/candidates` }],
  fields: [
    { name: 'title', label: 'শিরোনাম', required: true, full: true },
    { name: 'nomination_date', label: 'মনোনয়নের তারিখ', type: 'date' }, { name: 'election_date', label: 'নির্বাচনের তারিখ', type: 'date' },
    { name: 'status', label: 'অবস্থা', type: 'select', default: 'upcoming', options: status },
    { name: 'description', label: 'বিবরণ', type: 'textarea' },
    { name: 'result', label: 'ফলাফলের সারসংক্ষেপ', type: 'textarea' },
    { name: 'result_file', label: 'ফলাফলের ফাইল (PDF/ছবি)', type: 'file', accept: '.pdf,image/*', full: true },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}