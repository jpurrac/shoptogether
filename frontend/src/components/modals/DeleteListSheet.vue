<template>
  <BottomSheet v-model="open">
    <div style="text-align:center;padding:8px 0;">
      <div class="del-icon">🗑️</div>
      <h2 class="del-title">¿Eliminar lista?</h2>
      <p class="del-sub">Se eliminarán todos los productos y no podrás recuperarla.</p>
      <div class="btn-row">
        <button class="btn btn-ghost" @click="open = false">Cancelar</button>
        <button class="btn btn-danger" @click="confirm">Sí, eliminar</button>
      </div>
    </div>
  </BottomSheet>
</template>

<script setup>
import { computed } from 'vue'
import BottomSheet from '../BottomSheet.vue'

const props = defineProps({ modelValue: Boolean, list: Object })
const emit  = defineEmits(['update:modelValue', 'confirm'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

function confirm() {
  emit('confirm', props.list)
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
