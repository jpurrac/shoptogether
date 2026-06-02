// main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Directiva v-click-outside para cerrar menús
const clickOutside = {
  beforeMount(el, binding) {
    el._clickOutsideHandler = (event) => {
      if (!el.contains(event.target)) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el._clickOutsideHandler)
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutsideHandler)
  }
}

const app = createApp(App)

app.use(router)
app.directive('click-outside', clickOutside)

app.mount('#app')
