// router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import LoginView  from '../views/LoginView.vue'
import ListView   from '../views/ListView.vue'
import DetailView from '../views/DetailView.vue'
import StatsView  from '../views/StatsView.vue'

const routes = [
  { path: '/',          redirect: '/login' },
  { path: '/login',     name: 'login',  component: LoginView },
  { path: '/lists',     name: 'lists',  component: ListView,   meta: { requiresAuth: true } },
  { path: '/lists/:id', name: 'detail', component: DetailView, meta: { requiresAuth: true } },
  { path: '/stats',     name: 'stats',  component: StatsView,  meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// Guard de autenticación
// Reemplazar con lógica real de auth store (ej. Pinia)
router.beforeEach((to) => {
  const isLoggedIn = !!localStorage.getItem('st_token')
  if (to.meta.requiresAuth && !isLoggedIn) {
    return { name: 'login' }
  }
  if (to.name === 'login' && isLoggedIn) {
    return { name: 'lists' }
  }
})

export default router
