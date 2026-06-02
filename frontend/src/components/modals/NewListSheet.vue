<template>
  <BottomSheet v-model="open">
    <h2 class="sh-title">Nueva lista</h2>
    <div class="field">
      <label class="inp-label">Nombre</label>
      <input v-model="form.name" class="inp" placeholder="Ej: Supermercado semana" />
    </div>
    <div class="field" style="margin-bottom:20px;">
      <label class="inp-label">Presupuesto <span class="opt">(opcional)</span></label>
      <input v-model="form.budget" class="inp" placeholder="$ 0" inputmode="numeric" />
    </div>
    <button class="btn btn-primary" @click="submit" :disabled="!form.name.trim()">
      Crear lista
    </button>
  </BottomSheet>
</template>

<script setup>
import { reactive, computed } from 'vue'
import BottomSheet from '../BottomSheet.vue'
const props = defineProps({ modelValue: Boolean })
const emit  = defineEmits(['update:modelValue', 'submit'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const form = reactive({ name: '', budget: '' })

function submit() {
  if (!form.name.trim()) return
  emit('submit', { name: form.name.trim(), budget: form.budget || null })
  form.name = ''
  form.budget = ''
  emit('update:modelValue', false)
}
</script>

<style scoped>
@import './sheets.css';
</style>
