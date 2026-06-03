/**
 * Polling silencioso — no activa el loader global.
 * Devuelve { stop } para que el componente lo detenga manualmente.
 *
 * IMPORTANTE: llamar stop() en onUnmounted() desde el componente,
 * no desde dentro del composable, para que Vue lo asocie correctamente.
 *
 * @param {Function} fetchFn  - función async a ejecutar en cada tick
 * @param {number}   interval - ms entre peticiones (default 10 000)
 */
export function usePolling(fetchFn, interval = 10_000) {
  let active = true
  let timer  = null

  async function tick() {
    if (!active) return
    try { await fetchFn() } catch {}
    if (active) timer = setTimeout(tick, interval)
  }

  timer = setTimeout(tick, interval)

  function stop() {
    active = false
    clearTimeout(timer)
  }

  return { stop }
}
