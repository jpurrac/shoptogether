<template>
  <BottomSheet v-model="open">
    <!-- Header -->
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
      <div class="cl-icon">✅</div>
      <div>
        <h2 class="sh-title" style="margin:0;">Cerrar lista</h2>
        <div style="font-size:12px;color:#9AABBB;">{{ list?.name }}</div>
      </div>
    </div>

    <!-- Aviso si hay ítems sin marcar -->
    <div v-if="pending > 0" class="warn-box">
      ⚠️ Quedan {{ pending }} producto{{ pending !== 1 ? 's' : '' }} sin agregar al carrito. Puedes cerrar igual.
    </div>

    <!-- Resumen -->
    <div class="summary-box">
      <div class="sr"><span class="sr-l">Productos en carrito</span><span class="sr-v">{{ checked }} de {{ total }}</span></div>
      <div class="sr"><span class="sr-l">Presupuesto</span><span class="sr-v">{{ formatMoney(list?.budget) }}</span></div>
      <div class="sr"><span class="sr-l">Total en carrito</span><span class="sr-v" style="color:#1565C0;">{{ formatMoney(cartTotal) }}</span></div>
      <div class="sr" style="border:none;">
        <span class="sr-l">Diferencia</span>
        <span class="sr-v" style="color:#43A047;">+{{ formatMoney((list?.budget || 0) - cartTotal) }}</span>
      </div>
    </div>

    <!-- Total editable -->
    <div class="field">
      <label class="inp-label">Total real pagado <span class="opt">(edita si cambió en caja)</span></label>
      <input v-model="form.total" class="inp" inputmode="numeric" style="font-family:'Nunito',sans-serif;font-weight:800;font-size:18px;" />
    </div>

    <!-- Método de pago -->
    <div class="field">
      <label class="inp-label">Método de pago</label>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:4px;">
        <button
          v-for="m in payMethods"
          :key="m.value"
          class="pay-btn"
          :class="{ active: form.method === m.value }"
          @click="form.method = m.value"
        >{{ m.label }}</button>
      </div>
    </div>

    <!-- Quién pagó -->
    <div class="field">
      <label class="inp-label">¿Quién pagó?</label>
      <div style="display:flex;gap:8px;margin-top:4px;">
        <button
          v-for="member in list?.members"
          :key="member.id"
          class="payer-btn"
          :class="{ active: form.paidBy === member.id }"
          @click="form.paidBy = member.id"
        >
          <div class="av-sm" :style="{ background: member.color }">{{ member.initials }}</div>
          {{ member.name }}
        </button>
      </div>
    </div>

    <!-- Comentario -->
    <div class="field" style="margin-bottom:20px;">
      <label class="inp-label">Comentario <span class="opt">(opcional)</span></label>
      <input v-model="form.comment" class="inp" placeholder="Ej: Faltó el aceite..." />
    </div>

    <button class="btn btn-green" @click="submit">
      ✓ Confirmar y cerrar lista
    </button>
  </BottomSheet>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import BottomSheet from '../BottomSheet.vue'

const props = defineProps({ modelValue: Boolean, list: Object, items: Array })
const emit  = defineEmits(['update:modelValue', 'submit'])

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const payMethods = [
  { label: 'Débito',        value: 'debito' },
  { label: 'Crédito',       value: 'credito' },
  { label: 'Efectivo',      value: 'efectivo' },
  { label: 'Transferencia', value: 'transferencia' },
]

const form = reactive({ total: '', method: 'debito', paidBy: null, comment: '' })

const checked   = computed(() => (props.items || []).filter(i => i.checked).length)
const total     = computed(() => (props.items || []).length)
const pending   = computed(() => total.value - checked.value)
const cartTotal = computed(() => (props.items || []).filter(i => i.checked).reduce((s, i) => s + i.price * i.qty, 0))

watch([() => props.list, () => props.modelValue], ([l, open]) => {
  if (l && open) {
    form.total   = String(cartTotal.value)
    form.method  = 'debito'
    form.paidBy  = l.members?.[0]?.id ?? null
    form.comment = ''
  }
}, { immediate: true })

function formatMoney(v) {
  return v ? '$' + Number(v).toLocaleString('es-CL') : '-'
}

function submit() {
  emit('submit', {
    paid_by:        form.paidBy,
    total_real:     Number(form.total) || 0,
    payment_method: form.method,
    comment:        form.comment || null,
  })
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
