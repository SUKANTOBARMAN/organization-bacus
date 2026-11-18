export default {
  key: 'notices', path: 'notices', endpoint: 'notices',
  title: 'নোটিশ ও সার্কুলার', subtitle: 'নোটিশ, সার্কুলার, টেন্ডার ও প্রেস বিজ্ঞপ্তি',
  columns: [
    { label: 'শিরোনাম', key: 'title' },
    { label: 'ধরন', key: 'category', type: 'badge' },
    { label: 'তারিখ', key: 'published_at', type: 'date' },
    { label: 'ফাইল', key: 'file_url', type: 'file' },
    { label: 'পিন করা', key: 'is_pinned', type: 'bool' },
    { label: 'প্রকাশিত', key: 'is_published', type: 'bool' },
  ],
  filters: [{ name: 'category', label: 'সকল ধরন', options: [
    { value: 'notice', label: 'নোটিশ' }, { value: 'circular', label: 'সার্কুলার' },
    { value: 'tender', label: 'টেন্ডার' }, { value: 'result', label: 'ফলাফল' }, { value: 'press_release', label: 'প্রেস বিজ্ঞপ্তি' },
  ] }],
  fields: [
    { name: 'title', label: 'শিরোনাম', required: true, full: true },
    { name: 'category', label: 'ধরন', type: 'select', default: 'notice', options: [
      { value: 'notice', label: 'নোটিশ' }, { value: 'circular', label: 'সার্কুলার' },
      { value: 'tender', label: 'টেন্ডার' }, { value: 'result', label: 'ফলাফল' }, { value: 'press_release', label: 'প্রেস বিজ্ঞপ্তি' },
    ] },
    { name: 'published_at', label: 'প্রকাশের তারিখ', type: 'date' },
    { name: 'body', label: 'বিস্তারিত', type: 'textarea' },
    { name: 'file', label: 'সংযুক্তি (PDF/Word/ছবি)', type: 'file', accept: '.pdf,.doc,.docx,image/*', full: true },
    { name: 'is_pinned', label: 'উপরে পিন করুন', type: 'checkbox' },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}