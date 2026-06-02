import { reactive } from 'vue'

const user = reactive({
  id: null,
  name: null,
  email: null,
  avatar: null,
  initials: null,
  color: null,
})

export function SetUser(data) {
  if (!data) return ClearUser()
  Object.assign(user, data)
}

export function ClearUser() {
  user.id = null
  user.name = null
  user.email = null
  user.avatar = null
  user.initials = null
  user.color = null
}

export default user
