import { ref } from 'vue'
import api from '../api/client'

const settings = ref({})
let request = null

/** সাইটের সেটিংস একবারই লোড হয়, সব কম্পোনেন্ট শেয়ার করে */
export function useSettings() {
  if (!request) {
    request = api.get('/settings').then((r) => { settings.value = r.data }).catch(() => {})
  }
  return settings
}
