export default {
  key: 'users', path: 'users', endpoint: 'users', roles: ['super_admin'],
  title: 'ব্যবহারকারীগণ', subtitle: 'অ্যাডমিন প্যানেলে যারা ঢুকতে পারবে',
  columns: [
    { label: 'নাম', key: 'name' }, { label: 'ইমেইল', key: 'email' },
    { label: 'রোল', key: 'role', type: 'badge' }, { label: 'সক্রিয়', key: 'is_active', type: 'bool' },
  ],
  fields: [
    { name: 'name', label: 'নাম', required: true }, { name: 'email', label: 'ইমেইল', type: 'email', required: true },
    { name: 'password', label: 'পাসওয়ার্ড', type: 'password', help: 'সম্পাদনায় ফাঁকা রাখলে বদলাবে না (ন্যূনতম ৮ অক্ষর)' },
    { name: 'role', label: 'রোল', type: 'select', default: 'editor', options: [
      { value: 'editor', label: 'এডিটর — কনটেন্ট' }, { value: 'admin', label: 'অ্যাডমিন — সদস্য ও সংগঠন সহ' }, { value: 'super_admin', label: 'সুপার অ্যাডমিন — সব' },
    ] },
    { name: 'is_active', label: 'সক্রিয়', type: 'checkbox', default: true },
  ],
}