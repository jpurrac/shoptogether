<template>
  <!-- Overlay oscuro -->
  <Teleport to="body">
    <Transition name="sheet-overlay">
      <div
        v-if="modelValue"
        class="sheet-overlay"
        @click="$emit('update:modelValue', false)"
      >
        <!-- Sheet panel -->
        <Transition name="sheet-panel">
          <div
            v-if="modelValue"
            class="sheet-panel"
            @click.stop
          >
            <div class="sheet-handle" />
            <slot />
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, required: true }
})
defineEmits(['update:modelValue'])
</script>

<style scoped>
.sheet-overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 20, 35, 0.55);
  backdrop-filter: blur(6px);
  z-index: 200;
  display: flex;
  align-items: flex-end;
}

.sheet-panel {
  width: 100%;
  background: #fff;
  border-radius: 28px 28px 0 0;
  padding: 0 20px 44px;
  max-height: 90vh;
  overflow-y: auto;
}

.sheet-handle {
  width: 36px;
  height: 4px;
  background: #E8ECF2;
  border-radius: 99px;
  margin: 14px auto 18px;
}

/* Transiciones */
.sheet-overlay-enter-active,
.sheet-overlay-leave-active { transition: opacity 0.2s ease; }
.sheet-overlay-enter-from,
.sheet-overlay-leave-to { opacity: 0; }

.sheet-panel-enter-active,
.sheet-panel-leave-active { transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1); }
.sheet-panel-enter-from,
.sheet-panel-leave-to { transform: translateY(100%); }
</style>
