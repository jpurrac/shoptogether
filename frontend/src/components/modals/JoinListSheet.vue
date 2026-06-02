<template>
  <BottomSheet v-model="open">
    <h2 class="sh-title">Unirme a una lista</h2>
    <p style="font-size:13px;color:#5A6B82;margin-bottom:20px;line-height:1.5;">
      Ingresa el código de 5 caracteres que te compartieron.
    </p>
    <div class="code-wrap">
      <input
        v-for="(_, i) in 5"
        :key="i"
        :id="`cc${i}`"
        class="cc"
        maxlength="1"
        v-model="chars[i]"
        @input="onInput(i)"
        @keydown="onKeydown($event, i)"
      />
    </div>
    <p style="text-align:center;font-size:11px;color:#9AABBB;margin-bottom:22px;">
      El dueño de la lista te dará el código
    </p>
    <button class="btn btn-primary" @click="submit" :disabled="code.length < 5">
      Unirme a la lista
    </button>
    <button class="btn btn-ghost" style="margin-top:8px;" @click="open = false">
      Cancelar
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

const chars = reactive(['', '', '', '', ''])
const code  = computed(() => chars.join('').replace(/\s/g, ''))

function onInput(i) {
  chars[i] = (chars[i] || '').toUpperCase().slice(-1)
  if (chars[i] && i < 4) {
    document.getElementById(`cc${i + 1}`)?.focus()
  }
}

function onKeydown(e, i) {
  if (e.key === 'Backspace' && !chars[i] && i > 0) {
    document.getElementById(`cc${i - 1}`)?.focus()
  }
}

function submit() {
  emit('submit', code.value)
  chars.fill('')
  open.value = false
}
</script>

<style scoped>
@import './sheets.css';
</style>
