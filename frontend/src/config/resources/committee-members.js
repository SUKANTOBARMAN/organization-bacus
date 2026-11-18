export default {
  key: 'committee-members', nested: true, endpoint: 'committee-members', parentField: 'committee_id',
  title: 'কমিটির সদস্যবৃন্দ', subtitle: 'পদবি বাছলে ক্রম স্বয়ংক্রিয়ভাবে র‍্যাঙ্ক অনুযায়ী বসে',
  columns: [
    { label: 'ক্রম', key: 'sort_order' }, { label: 'ছবি', key: 'photo_url', type: 'image' },
    { label: 'নাম', key: 'name' }, { label: 'পদবি', key: 'designation' }, { label: 'ফোন', key: 'phone' },
  ],
  fields: [
    { name: 'name', label: 'নাম', required: true },
    { name: 'designation_id', label: 'পদবি', type: 'select', optionsFrom: { endpoint: 'designations', label: (r) => r.name } },
    { name: 'designation', label: 'কাস্টম পদবি (ঐচ্ছিক)', help: 'পদবি তালিকায় না থাকলে এখানে লিখুন' },
    { name: 'phone', label: 'ফোন নম্বর' },
    { name: 'sort_order', label: 'ক্রম (ঐচ্ছিক)', type: 'number' },
    { name: 'photo', label: 'ছবি', type: 'file', accept: 'image/*', full: true },
    { name: 'member_id', label: 'সদস্যের সাথে যুক্ত করুন (ঐচ্ছিক)', type: 'relation', full: true,
      relation: { endpoint: 'members', label: (r) => `${r.member_no} — ${r.name}` }, labelFrom: () => '' },
  ],
}