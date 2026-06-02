// composables/useSheet.js
// Manejo centralizado de los bottom sheets (modales)
import { ref } from 'vue'

const activeSheet = ref(null)

export function useSheet() {
  function openSheet(name) {
    activeSheet.value = name
  }

  function closeSheet() {
    activeSheet.value = null
  }

  function isOpen(name) {
    return activeSheet.value === name
  }

  return { activeSheet, openSheet, closeSheet, isOpen }
}
