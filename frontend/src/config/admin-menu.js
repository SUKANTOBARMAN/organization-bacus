import {
  LayoutDashboard, Bell, Newspaper, Images, BookOpen, FileText,
  Users, UsersRound, Vote, Mail, Settings, ShieldCheck, Gavel,
  GalleryHorizontal, Video, Award, UserPlus, Wallet, ScrollText,
} from '@lucide/vue'

const ADMINS = ['super_admin', 'admin']

export const adminMenu = [
  { group: null, items: [{ label: 'ড্যাশবোর্ড', to: '/admin', icon: LayoutDashboard, exact: true }] },
  {
    group: 'কনটেন্ট',
    items: [
      { label: 'নোটিশ ও সার্কুলার', to: '/admin/notices', icon: Bell },
      { label: 'খবর ও ইভেন্ট', to: '/admin/posts', icon: Newspaper },
      { label: 'হোম স্লাইডার', to: '/admin/sliders', icon: GalleryHorizontal },
      { label: 'ফটো গ্যালারি', to: '/admin/albums', icon: Images },
      { label: 'ভিডিও গ্যালারি', to: '/admin/videos', icon: Video },
      { label: 'সংবিধান ও ডকুমেন্টস', to: '/admin/documents', icon: BookOpen },
      { label: 'পেজ (আমাদের সম্পর্কে)', to: '/admin/pages', icon: FileText },
    ],
  },
  {
    group: 'সংগঠন',
    items: [
      { label: 'সদস্যপদের আবেদন', to: '/admin/applications', icon: UserPlus, roles: ADMINS },
      { label: 'সদস্যগণ', to: '/admin/members', icon: Users, roles: ADMINS },
      { label: 'পদবী ও র‍্যাংক', to: '/admin/designations', icon: Award, roles: ADMINS },
      { label: 'পেমেন্ট ও বকেয়া', to: '/admin/payments', icon: Wallet, roles: ADMINS },
      { label: 'কার্যকরী কমিটি', to: '/admin/committees', icon: UsersRound, roles: ADMINS },
      { label: 'নির্বাচন কমিশন', to: '/admin/commission-members', icon: Gavel, roles: ADMINS },
      { label: 'নির্বাচনসমূহ', to: '/admin/elections', icon: Vote, roles: ADMINS },
    ],
  },
  {
    group: 'সিস্টেম',
    items: [
      { label: 'মেসেজসমূহ', to: '/admin/messages', icon: Mail, roles: ADMINS },
      { label: 'সাইট সেটিংস', to: '/admin/settings', icon: Settings, roles: ADMINS },
      { label: 'ব্যবহারকারীগণ', to: '/admin/users', icon: ShieldCheck, roles: ['super_admin'] },
      { label: 'অ্যাক্টিভিটি লগ', to: '/admin/activity-logs', icon: ScrollText, roles: ADMINS },
    ],
  },
]