export default {
  key: 'activity-logs', path: 'activity-logs', endpoint: 'activity-logs', readonly: true, roles: ['super_admin', 'admin'],
  title: 'অ্যাক্টিভিটি লগ', subtitle: 'কে কখন কী করেছে',
  columns: [
    { label: 'সময়', key: 'created_at', type: 'datetime' }, { label: 'ব্যবহারকারী', key: 'user.name' },
    { label: 'অ্যাকশন', key: 'action', type: 'badge' }, { label: 'আইটেম', key: 'subject' }, { label: 'বিস্তারিত', key: 'summary' },
  ],
  filters: [{ name: 'action', label: 'সকল অ্যাকশন', options: ['created', 'updated', 'deleted', 'login', 'approved', 'rejected'].map((a) => ({ value: a, label: a })) }],
  fields: [],
}