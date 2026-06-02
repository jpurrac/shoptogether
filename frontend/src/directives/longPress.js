import { onLongPress } from '@vueuse/core'

export const vLongPress = {
  mounted(el, binding) {
    const { handler, delay = 600 } = binding.value

    el.style.userSelect = 'none'
    el.style.webkitUserSelect = 'none'
    el.style.webkitTouchCallout = 'none'

    el.addEventListener('contextmenu', e => e.preventDefault())

    el._cleanup = onLongPress(
      el,
      handler,
      {
        delay,
        modifiers: {
          prevent: true,
          stop: true,
        },
      }
    )
  },
  unmounted(el) {
    el._cleanup?.()
  },
}
