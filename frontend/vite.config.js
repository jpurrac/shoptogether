import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    vue(), 
    tailwindcss()
  ],
  server: {
    host: true,      // Necesario para Docker
    port: 5173,
    strictPort: true,
    allowedHosts: [
      'localhost',                // tu host local
      '127.0.0.1',                // localhost alternativo
      'front.jpurrac.cl', // tu URL de ngrok
      // puedes agregar más URLs de ngrok si cambian
    ],
    watch: {
      usePolling: true
    }
  }
})
