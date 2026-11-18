import { ref, watchEffect, toValue } from 'vue'
import api, { errorMessage } from '../api/client'

/** const { data, loading, error } = useFetch('/notices', () => ({ page: page.value })) */
export function useFetch(path, params = {}) {
  const data = ref(null)
  const loading = ref(true)
  const error = ref('')

  watchEffect(async () => {
    loading.value = true
    error.value = ''
    try {
      data.value = (await api.get(toValue(path), { params: toValue(params) })).data
    } catch (e) {
      error.value = errorMessage(e, 'তথ্য আনা যায়নি')
    } finally {
      loading.value = false
    }
  })

  return { data, loading, error }
}
