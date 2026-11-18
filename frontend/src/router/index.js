import { createRouter, createWebHistory } from 'vue-router'
import publicRoutes from './public.routes'
import adminRoutes from './admin.routes'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    ...publicRoutes,
    ...adminRoutes,
    { path: '/:pathMatch(.*)*', component: () => import('../views/NotFoundView.vue') },
  ],
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (to.meta.guest && auth.isLoggedIn) return { name: 'admin-dashboard' }

  if (to.matched.some((r) => r.meta.requiresAuth)) {
    if (!auth.isLoggedIn) return { name: 'admin-login', query: { redirect: to.fullPath } }

    if (!auth.user) {
      try { await auth.fetchMe() } catch (_) { auth.clear(); return { name: 'admin-login' } }
    }

    if (to.meta.roles && !auth.can(to.meta.roles)) return { name: 'admin-dashboard' }
  }
})

export default router
