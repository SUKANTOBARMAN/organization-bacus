import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8080/api/v1',
  headers: { Accept: 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// টোকেন মেয়াদোত্তীর্ণ/বাতিল হলে লগইন পেজে পাঠায়
api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401 && localStorage.getItem('token')) {
      localStorage.removeItem('token')
      if (!location.pathname.startsWith('/admin/login')) location.href = '/admin/login'
    }
    return Promise.reject(err)
  },
)

export const errorMessage = (e, fallback = 'কিছু একটা ভুল হয়েছে') => e.response?.data?.message || fallback

export default api
