export default {
  key: 'pages', path: 'pages', endpoint: 'pages',
  title: 'পেজসমূহ', subtitle: '"about" স্লাগের পেজটি আমাদের সম্পর্কে-তে দেখায়। বডিতে HTML লেখা যায়।',
  columns: [{ label: 'স্লাগ', key: 'slug' }, { label: 'শিরোনাম', key: 'title' }, { label: 'প্রকাশিত', key: 'is_published', type: 'bool' }],
  fields: [
    { name: 'slug', label: 'স্লাগ (যেমন: about)', required: true },
    { name: 'title', label: 'শিরোনাম', required: true },
    { name: 'body', label: 'বিষয়বস্তু (HTML)', type: 'textarea' },
    { name: 'image', label: 'ছবি', type: 'file', accept: 'image/*', full: true },
    { name: 'is_published', label: 'প্রকাশিত', type: 'checkbox', default: true },
  ],
}