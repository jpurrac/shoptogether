<template>
  <BottomSheet v-model="open">
    <h2 class="sh-title">Agregar producto</h2>

    <div class="field">
      <label class="inp-label">Nombre</label>
      <input v-model="form.name" class="inp" placeholder="Ej: Leche descremada" autofocus />
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
      <div>
        <label class="inp-label">Cantidad</label>
        <div class="stepper">
          <button class="step-btn" @click="form.qty = Math.max(1, form.qty - 1)" :disabled="form.qty <= 1">−</button>
          <div class="step-val">{{ form.qty }}</div>
          <button class="step-btn" @click="form.qty++">+</button>
        </div>
      </div>
      <div>
        <label class="inp-label">Precio <span class="opt">(opc.)</span></label>
        <input v-model="form.price" class="inp" placeholder="$ 0" inputmode="numeric" />
      </div>
    </div>

    <button class="btn btn-primary" @click="submit" :disabled="!form.name.trim()">
      Agregar producto
    </button>
  </BottomSheet>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import BottomSheet from '../BottomSheet.vue'

const props = defineProps({ modelValue: Boolean })
const emit  = defineEmits(['update:modelValue', 'submit'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

const form = reactive({ name: '', qty: 1, price: '' })

// Limpiar form al abrir
watch(open, (v) => { if (v) Object.assign(form, { name: '', qty: 1, price: '' }) })

function submit() {
  if (!form.name.trim()) return
  emit('submit', { name: form.name.trim(), qty: form.qty, price: Number(form.price) || 0 })
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
