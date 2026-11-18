export default {
  key: 'election-candidates', nested: true, endpoint: 'election-candidates', parentField: 'election_id',
  title: 'প্রার্থী ও ফলাফল', subtitle: 'প্রার্থী, প্রাপ্ত ভোট ও বিজয়ী',
  columns: [
    { label: 'পদবী', key: 'position' }, { label: 'ছবি', key: 'photo_url', type: 'image' },
    { label: 'নাম', key: 'name' }, { label: 'প্রাপ্ত ভোট', key: 'votes' }, { label: 'বিজয়ী', key: 'is_winner', type: 'bool' },
  ],
  fields: [
    { name: 'name', label: 'প্রার্থীর নাম', required: true }, { name: 'position', label: 'পদবী', required: true },
    { name: 'votes', label: 'ভোট সংখ্যা', type: 'number', default: 0 },
    { name: 'photo', label: 'ছবি', type: 'file', accept: 'image/*' },
    { name: 'is_winner', label: 'বিজয়ী', type: 'checkbox', full: true },
  ],
}