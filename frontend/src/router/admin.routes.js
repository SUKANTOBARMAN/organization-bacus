import { standaloneResources } from '../config/resources'

const ADMINS = ['super_admin', 'admin']

export default [
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('../views/admin/auth/LoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'admin-dashboard', component: () => import('../views/admin/dashboard/DashboardView.vue') },

      // কনফিগ থেকে স্বয়ংক্রিয় CRUD রুট (notices, posts, members ...)
      ...standaloneResources.map((r) => ({
        path: r.path,
        name: `admin-${r.path}`,
        component: () => import('../views/admin/resources/ResourceView.vue'),
        meta: { resource: r.key, roles: r.roles },
      })),

      { path: 'committees/:id/members', name: 'admin-committee-members', component: () => import('../views/admin/committees/CommitteeMembersView.vue'), meta: { roles: ADMINS } },
      { path: 'elections/:id/candidates', name: 'admin-election-candidates', component: () => import('../views/admin/elections/ElectionCandidatesView.vue'), meta: { roles: ADMINS } },
      { path: 'posts/:id/images', name: 'admin-post-images', component: () => import('../views/admin/posts/PostImagesView.vue') },
      { path: 'applications', name: 'admin-applications', component: () => import('../views/admin/applications/ApplicationsView.vue'), meta: { roles: ADMINS } },
      { path: 'payments', name: 'admin-payments', component: () => import('../views/admin/payments/PaymentsView.vue'), meta: { roles: ADMINS } },
      { path: 'albums/:id/photos', name: 'admin-album-photos', component: () => import('../views/admin/gallery/AlbumPhotosView.vue') },
      { path: 'messages', name: 'admin-messages', component: () => import('../views/admin/messages/MessagesView.vue'), meta: { roles: ADMINS } },
      { path: 'settings', name: 'admin-settings', component: () => import('../views/admin/settings/SettingsView.vue'), meta: { roles: ADMINS } },
    ],
  },
]
