// পাবলিক ওয়েবসাইটের ১০টি বিভাগ
export const publicMenu = [
  { path: '/', label: 'হোম' },
  { path: '/about', label: 'আমাদের সম্পর্কে' },
  { path: '/committee', label: 'কার্যকরী কমিটি' },
  { path: '/election-commission', label: 'নির্বাচন কমিশন' },
  { path: '/notices', label: 'নোটিশ ও সার্কুলার' },
  { path: '/news', label: 'খবর ও ইভেন্ট' },
  { path: '/gallery', label: 'ফটো গ্যালারি' },
  { path: '/constitution', label: 'সংবিধান ও নিয়মাবলী' },
  { path: '/members', label: 'সদস্য তালিকা' },
  { path: '/contact', label: 'যোগাযোগ' },
]

export const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']

export const isActive = (route, path, exact = false) =>
  exact || path === '/' ? route.path === path : route.path.startsWith(path)