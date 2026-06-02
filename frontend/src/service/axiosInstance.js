// frontend/src/service/axiosInstance.js
import axios from "axios"
import { ref } from "vue"
import router from "../router"

const baseURL = import.meta.env.VITE_API_URL

const api = axios.create({
  baseURL,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
})

/* --------------------------
   Token helpers
-------------------------- */

const TOKEN_KEY = "st_token"

export function SetAuthToken(token) {
  if (token) {
    localStorage.setItem(TOKEN_KEY, token)
    api.defaults.headers.common.Authorization = `Bearer ${token}`
  } else {
    ClearAuthToken()
  }
}

export function GetAuthToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export function ClearAuthToken() {
  localStorage.removeItem(TOKEN_KEY)
  delete api.defaults.headers.common.Authorization
}

/* --------------------------
   Global loading state
-------------------------- */

export const isLoading = ref(false)
let pendingRequests = 0

const start = () => {
  pendingRequests++
  isLoading.value = true
}

const stop = () => {
  pendingRequests--
  if (pendingRequests <= 0) {
    pendingRequests = 0
    isLoading.value = false
  }
}

/* --------------------------
   Request interceptor
-------------------------- */
api.interceptors.request.use(
  (config) => {
    start() // ✅ SIEMPRE

    const token = GetAuthToken()
    if (token && !config.headers?.Authorization) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config
  },
  (error) => {
    stop()
    return Promise.reject(error)
  }
)

/* --------------------------
   Response interceptor
-------------------------- */
api.interceptors.response.use(
  (response) => {
    stop()
    return response
  },
  (error) => {
    const { response } = error
    stop()

    if (response?.status === 401) {
      ClearAuthToken()
      router.push({ name: 'login' })
    }

    return Promise.reject(error)
  }
)

export default api
