import { defineStore } from 'pinia'
import api from '../api/client'

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: null, token: localStorage.getItem('token') }),
  getters: { isLoggedIn: (s) => !!s.token },
  actions: {
    async login(email, password) {
      const { data } = await api.post('/auth/login', { email, password })
      this.token = data.token
      this.user = data.user
      localStorage.setItem('token', data.token)
    },
    async fetchMe() {
      this.user = (await api.get('/admin/me')).data
    },
    async logout() {
      try { await api.post('/admin/logout') } catch (_) { /* ignore */ }
      this.clear()
    },
    clear() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
    },
    can(roles) {
      return !roles || roles.includes(this.user?.role)
    },
  },
})
