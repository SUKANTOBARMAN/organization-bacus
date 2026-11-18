const types = [
  { value: 'general', label: 'সাধারণ সদস্য' },
  { value: 'life', label: 'আজীবন সদস্য' },
  { value: 'associate', label: 'সহযোগী সদস্য' },
  { value: 'honorary', label: 'সম্মানিত সদস্য' },
]
const blood = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map((b) => ({ value: b, label: b }))

export default {
  key: 'members', path: 'members', endpoint: 'members', exportUrl: '/admin/members/export',
  title: 'সদস্যবৃন্দ', subtitle: 'পদবির র‍্যাঙ্ক অনুযায়ী সাজানো',
  columns: [
    { label: 'নং', key: 'member_no' }, { label: 'ছবি', key: 'photo_url', type: 'image' },
    { label: 'নাম', key: 'name', class: 'font-medium' },
    { label: 'পদবি', key: 'position.name', render: (r) => r.position?.name || r.designation || '—' },
    { label: 'ধরন', key: 'membership_type', type: 'badge' }, { label: 'ফোন', key: 'phone' },
    { label: 'রক্তের গ্রুপ', key: 'blood_group' }, { label: 'অবস্থা', key: 'status', type: 'badge' },
    { label: 'পাবলিক', key: 'is_public', type: 'bool' },
  ],
  filters: [
    { name: 'status', label: 'সকল অবস্থা', options: [{ value: 'active', label: 'সক্রিয়' }, { value: 'inactive', label: 'নিষ্ক্রিয়' }] },
    { name: 'membership_type', label: 'সকল ধরন', options: types },
    { name: 'blood_group', label: 'রক্তের গ্রুপ', options: blood },
  ],
  fields: [
    { name: 'member_no', label: 'সদস্য নম্বর', required: true },
    { name: 'name', label: 'পূর্ণ নাম', required: true },
    { name: 'designation_id', label: 'পদবি', type: 'select', optionsFrom: { endpoint: 'designations', label: (r) => `${r.name} (${r.rank})` } },
    { name: 'membership_type', label: 'সদস্যপদের ধরন', type: 'select', default: 'general', options: types },
    { name: 'phone', label: 'ফোন নম্বর' }, { name: 'email', label: 'ইমেইল', type: 'email' },
    { name: 'blood_group', label: 'রক্তের গ্রুপ', type: 'select', options: blood },
    { name: 'gender', label: 'লিঙ্গ', type: 'select', options: [{ value: 'male', label: 'পুরুষ' }, { value: 'female', label: 'নারী' }, { value: 'other', label: 'অন্যান্য' }] },
    { name: 'profession', label: 'পেশা' }, { name: 'workplace', label: 'কর্মস্থল' },
    { name: 'date_of_birth', label: 'জন্ম তারিখ', type: 'date' }, { name: 'joined_at', label: 'যোগদানের তারিখ', type: 'date' },
    { name: 'address', label: 'ঠিকানা', type: 'textarea' },
    { name: 'bio', label: 'সংক্ষিপ্ত পরিচিতি', type: 'textarea' },
    { name: 'photo', label: 'ছবি', type: 'file', accept: 'image/*', full: true },
    { name: 'status', label: 'অবস্থা', type: 'select', default: 'active', options: [{ value: 'active', label: 'সক্রিয়' }, { value: 'inactive', label: 'নিষ্ক্রিয়' }] },
    { name: 'is_public', label: 'পাবলিক ডিরেক্টরিতে দেখান', type: 'checkbox', default: true },
  ],
}