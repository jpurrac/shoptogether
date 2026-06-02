<template>
  <BottomSheet v-model="open">
    <h2 class="sh-title">Editar lista</h2>
    <div class="field">
      <label class="inp-label">Nombre</label>
      <input v-model="form.name" class="inp" />
    </div>
    <div class="field" style="margin-bottom:20px;">
      <label class="inp-label">Presupuesto <span class="opt">(opcional)</span></label>
      <input v-model="form.budget" class="inp" inputmode="numeric" />
    </div>
    <div class="btn-row">
      <button class="btn btn-ghost" @click="open = false">Cancelar</button>
      <button class="btn btn-primary" @click="submit">Guardar</button>
    </div>
  </BottomSheet>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import BottomSheet from '../BottomSheet.vue'

const props = defineProps({ modelValue: Boolean, list: Object })
const emit  = defineEmits(['update:modelValue', 'submit'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const form = reactive({ name: '', budget: '' })

watch(() => props.list, (l) => {
  if (l) {
    form.name   = l.name
    form.budget = l.budget ? String(l.budget) : ''
  }
}, { immediate: true })

function submit() {
  emit('submit', { ...props.list, name: form.name, budget: form.budget || null })
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
