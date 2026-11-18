export default {
  key: 'commission-members', path: 'commission-members', endpoint: 'commission-members',
  title: 'নির্বাচন কমিশন', subtitle: 'নির্বাচন কমিশনের সদস্যবৃন্দ',
  columns: [
    { label: 'ক্রম', key: 'sort_order' }, { label: 'ছবি', key: 'photo_url', type: 'image' },
    { label: 'নাম', key: 'name' }, { label: 'পদবী', key: 'designation' }, { label: 'বর্তমান', key: 'is_current', type: 'bool' },
  ],
  fields: [
    { name: 'name', label: 'নাম', required: true }, { name: 'designation', label: 'পদবী', required: true },
    { name: 'phone', label: 'ফোন নম্বর' }, { name: 'sort_order', label: 'ক্রম', type: 'number', default: 0 },
    { name: 'photo', label: 'ছবি', type: 'file', accept: 'image/*', full: true },
    { name: 'is_current', label: 'বর্তমান কমিশন', type: 'checkbox', default: true },
  ],
}