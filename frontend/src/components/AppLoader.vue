<template>
  <Transition name="fade">
    <div v-if="isLoading" class="overlay">
      <div class="spinner-wrap">
        <div class="spinner" />
        <span class="label">Cargando...</span>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { isLoading } from '../service/axiosInstance'
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(244, 246, 250, 0.75);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
}

.spinner-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  background: white;
  padding: 28px 36px;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0,0,0,.12);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3.5px solid #E8ECF2;
  border-top-color: #1565C0;
  border-radius: 50%;
  animation: spin .75s linear infinite;
}

.label {
  font-family: 'Nunito', sans-serif;
  font-weight: 700;
  font-size: 13px;
  color: #5A6B82;
}

@keyframes spin { to { transform: rotate(360deg); } }

.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
