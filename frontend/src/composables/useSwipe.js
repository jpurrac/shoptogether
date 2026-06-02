// composables/useSwipe.js
// Swipe-to-delete gesture para listas e ítems
import { ref } from 'vue'

const THRESHOLD = 68 // px mínimos para confirmar swipe

export function useSwipe(onSwipeConfirmed) {
  const swipeData = ref({})

  function onTouchStart(event, id) {
    const t = event.touches[0]
    swipeData.value[id] = { x: t.clientX, y: t.clientY, moved: false }
  }

  function onTouchMove(event, id) {
    const d = swipeData.value[id]
    if (!d) return

    const t = event.touches[0]
    const dx = t.clientX - d.x
    const dy = t.clientY - d.y

    // Si el movimiento vertical supera al horizontal, ignorar (scroll normal)
    if (Math.abs(dy) > Math.abs(dx) && !d.moved) {
      delete swipeData.value[id]
      return
    }

    d.moved = true

    if (dx < 0) {
      const el = document.getElementById(id)
      if (el) {
        el.style.transition = 'none'
        el.style.transform = `translateX(${Math.max(dx, -96)}px)`
      }
      event.preventDefault()
    }
  }

  function onTouchEnd(event, id) {
    const d = swipeData.value[id]
    if (!d || !d.moved) return

    const el = document.getElementById(id)
    if (el) el.style.transition = ''

    const dx = event.changedTouches[0].clientX - d.x

    if (dx < -THRESHOLD) {
      // Swipe confirmado → quedarse en -88px y disparar callback
      if (el) {
        el.style.transform = 'translateX(-88px)'
        // Auto-reset si no se confirma en 1.5s
        setTimeout(() => { if (el) el.style.transform = '' }, 1500)
      }
      onSwipeConfirmed(id)
    } else {
      if (el) el.style.transform = ''
    }

    delete swipeData.value[id]
  }

  function resetSwipes(ids = []) {
    const targets = ids.length ? ids : Object.keys(swipeData.value)
    targets.forEach(id => {
      const el = document.getElementById(id)
      if (el) el.style.transform = ''
    })
  }

  return { onTouchStart, onTouchMove, onTouchEnd, resetSwipes }
}
