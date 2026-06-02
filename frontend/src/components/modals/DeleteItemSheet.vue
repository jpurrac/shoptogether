<template>
  <BottomSheet v-model="open">
    <div style="text-align:center;padding:8px 0;">
      <div class="del-icon">🗑️</div>
      <h2 class="del-title">¿Eliminar producto?</h2>
      <p class="del-sub">{{ item?.name }} se eliminará de la lista permanentemente.</p>
      <div class="btn-row">
        <button class="btn btn-ghost" @click="cancel">Cancelar</button>
        <button class="btn btn-danger" @click="confirm">Sí, eliminar</button>
      </div>
    </div>
  </BottomSheet>
</template>

<script setup>
import { computed } from 'vue'
import BottomSheet from '../BottomSheet.vue'

const props = defineProps({ modelValue: Boolean, item: Object })
const emit  = defineEmits(['update:modelValue', 'confirm'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

function confirm() {
  emit('confirm', props.item)
  open.value = false
}

function cancel() {
  // Resetear el swipe del ítem antes de cerrar
  const el = document.getElementById(`ic-${props.item?.id}`)
  if (el) el.style.transform = ''
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
