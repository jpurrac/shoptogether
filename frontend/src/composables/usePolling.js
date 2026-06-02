import { onUnmounted, ref } from 'vue'

/**
 * Polling de ítems cada `interval` ms.
 * Solo se activa si `shared` es true.
 * Se detiene automáticamente al desmontar el componente.
 *
 * @param {Function} fetchFn  - función async a ejecutar en cada tick
 * @param {boolean}  shared   - solo activa si la lista es compartida
 * @param {number}   interval - milisegundos entre peticiones (default 10 000)
 */
export function usePolling(fetchFn, shared, interval = 10_000) {
  if (!shared) return { stop: () => {} }

  const active = ref(true)
  let timer    = null

  async function tick() {
    if (!active.value) return
    try { await fetchFn() } catch {}
    if (active.value) timer = setTimeout(tick, interval)
  }

  // Primer tick después del primer intervalo (los datos ya se cargaron al montar)
  timer = setTimeout(tick, interval)

  function stop() {
    active.value = false
    clearTimeout(timer)
  }

  onUnmounted(stop)

  return { stop }
}
