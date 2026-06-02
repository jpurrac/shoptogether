<template>
  <BottomSheet v-model="open">
    <h2 class="sh-title">Editar producto</h2>
    <div class="field">
      <label class="inp-label">Nombre</label>
      <input v-model="form.name" class="inp" />
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
      <div>
        <label class="inp-label" style="margin-bottom:6px;display:block;">Cantidad</label>
        <div class="stepper">
          <button class="step-btn" @click="form.qty = Math.max(1, form.qty - 1)" :disabled="form.qty <= 1">−</button>
          <div class="step-val">{{ form.qty }}</div>
          <button class="step-btn" @click="form.qty++">+</button>
        </div>
      </div>
      <div>
        <label class="inp-label">Precio c/u</label>
        <input v-model="form.price" class="inp" inputmode="numeric" />
      </div>
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

const props = defineProps({ modelValue: Boolean, item: Object })
const emit  = defineEmits(['update:modelValue', 'submit'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const form = reactive({ name: '', qty: 1, price: '' })

watch(() => props.item, (it) => {
  if (it) {
    form.name  = it.name
    form.qty   = it.qty
    form.price = it.price ? String(it.price) : ''
  }
}, { immediate: true })

function submit() {
  emit('submit', { ...props.item, name: form.name, qty: form.qty, price: Number(form.price) || 0 })
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
