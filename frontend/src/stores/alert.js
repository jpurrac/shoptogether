import { reactive } from 'vue'

const alerts = reactive([])
let nextId = 0

export function Alert({ msg, color = 'blue', duration = 3000 }) {
  const id = ++nextId
  alerts.push({ id, msg, color })
  setTimeout(() => {
    const i = alerts.findIndex(a => a.id === id)
    if (i !== -1) alerts.splice(i, 1)
  }, duration)
}

export { alerts }
