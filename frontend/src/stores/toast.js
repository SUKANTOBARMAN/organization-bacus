import { defineStore } from 'pinia'

let seq = 0

export const useToast = defineStore('toast', {
  state: () => ({ items: [] }),
  actions: {
    push(message, type = 'success') {
      const t = { id: ++seq, message, type }
      this.items.push(t)
      setTimeout(() => this.remove(t.id), 3500)
    },
    error(message) { this.push(message, 'error') },
    remove(id) { this.items = this.items.filter((t) => t.id !== id) },
  },
})
