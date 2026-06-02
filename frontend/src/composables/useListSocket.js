import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { onUnmounted } from 'vue'
import { GetAuthToken } from '../service/axiosInstance'

let echo = null

function getEcho() {
  if (echo) return echo

  window.Pusher = Pusher

  echo = new Echo({
    broadcaster:    'reverb',
    key:            import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:         import.meta.env.VITE_REVERB_HOST,
    wsPort:         import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:        import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS:       false,
    enabledTransports: ['ws'],
    authEndpoint:   `${import.meta.env.VITE_API_URL}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${GetAuthToken()}`,
        Accept: 'application/json',
      },
    },
  })

  return echo
}

/**
 * Se suscribe al canal privado list.{listId} y llama a onEvent(type, payload)
 * Solo se activa si shared === true (lista con más de 1 miembro).
 */
export function useListSocket(listId, shared, onEvent) {
  if (!shared) return { disconnect: () => {} }

  const channel = getEcho().private(`list.${listId}`)

  channel.listen('.ListChanged', ({ type, payload }) => {
    onEvent(type, payload)
  })

  const disconnect = () => {
    getEcho().leave(`list.${listId}`)
  }

  onUnmounted(disconnect)

  return { disconnect }
}
