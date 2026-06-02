<template>
  <div class="app-wrap">
    <AppLoader />
    <RouterView />

    <!-- Alertas globales -->
    <div class="alert-container">
      <div
        v-for="a in alerts"
        :key="a.id"
        class="alert-pill"
        :class="`alert-${a.color}`"
      >
        {{ a.msg }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import { alerts } from './stores/alert.js'
import AppLoader from './components/AppLoader.vue'
</script>

<style>
/* ── CSS Variables globales (paleta ShopTogether)  */
:root {
  --navy:      #1D3461;
  --blue:      #1565C0;
  --teal:      #00ACC1;
  --green:     #43A047;
  --orange:    #F57C00;
  --t1:        #1A2332;
  --t2:        #5A6B82;
  --t3:        #9AABBB;
  --bg:        #F4F6FA;
  --surface:   #FFFFFF;
  --border:    #E8ECF2;
  --blue-grad: linear-gradient(135deg, #1565C0, #00ACC1);
  --green-grad:linear-gradient(135deg, #43A047, #66BB6A);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Plus Jakarta Sans', 'Nunito', system-ui, sans-serif;
  background: #F4F6FA;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Scrollbar personalizado */
::-webkit-scrollbar { width: 0; }

/* Fuentes (agregar en index.html o via Vite plugin) */
/* Google Fonts: Plus Jakarta Sans 400,500,600 | Nunito 700,800,900 */

.app-wrap {
  max-width: 430px;
  min-height: 100vh;
  margin: 0 auto;
  background: #F4F6FA;
  box-shadow: 0 0 60px rgba(0,0,0,.08);
  position: relative;
  overflow: hidden;
}

.alert-container {
  position: fixed;
  top: 16px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  gap: 8px;
  z-index: 9999;
  width: 90%;
  max-width: 380px;
}

.alert-pill {
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  text-align: center;
  animation: fadeIn .2s ease;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

.alert-green  { background: #43A047; }
.alert-red    { background: #E53935; }
.alert-yellow { background: #F57C00; color: #fff; }
.alert-blue   { background: #1565C0; }
</style>
