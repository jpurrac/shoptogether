<template>
  <!-- Wrapper con fondo rojo -->
  <div class="sw" :id="`sw-${item.id}`">
    <div class="idel-bg">
      <TrashIcon /> Eliminar
    </div>

    <!-- Tarjeta -->
    <div
      class="icard"
      :class="{ checked: item.checked }"
      :id="`ic-${item.id}`"
      @click="$emit('edit', item)"
      @touchstart="onTouchStart($event, `ic-${item.id}`)"
      @touchmove="onTouchMove($event, `ic-${item.id}`)"
      @touchend="onTouchEnd($event, `ic-${item.id}`)"
    >
      <div class="ii">
        <!-- Checkmark (detiene propagación para no abrir edición) -->
        <div class="chk" @click.stop="$emit('toggle', item)">
          <CheckIcon v-if="item.checked" />
        </div>

        <div class="iinfo">
          <div class="iname">{{ item.name }}</div>
          <div class="isub">x{{ item.qty }} {{ item.qty === 1 ? 'unidad' : 'unidades' }}</div>
        </div>

        <div class="ipr">
          <div class="itotal">{{ formatMoney(item.price * item.qty) }}</div>
          <div class="ippu">{{ formatMoney(item.price) }} c/u</div>
        </div>

        <!-- Ícono editar (visible en hover desktop) -->
        <PencilIcon class="edit-hint" />
      </div>

      <div class="iby" v-if="item.added_by">
        <div class="av" :style="{ background: item.added_by.color }">
          {{ item.added_by.initials }}
        </div>
        {{ item.added_by.name }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { h } from 'vue'
import { useSwipe } from '../composables/useSwipe'

// Íconos con h() — compatibles con Vite sin runtime compiler
const TrashIcon  = () => h('svg', { width: 14, height: 14, fill: 'none', stroke: 'currentColor', 'stroke-width': 2, viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })])
const CheckIcon  = () => h('svg', { width: 12, height: 12, fill: 'none', stroke: 'white', 'stroke-width': 3, viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M5 13l4 4L19 7' })])
const PencilIcon = () => h('svg', { width: 15, height: 15, fill: 'none', stroke: '#1565C0', 'stroke-width': 2, viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })])

const props = defineProps({
  item: { type: Object, required: true }
})

const emit = defineEmits(['toggle', 'edit', 'delete'])

const { onTouchStart, onTouchMove, onTouchEnd } = useSwipe((id) => {
  emit('delete', props.item)
})

function formatMoney(amount) {
  return '$' + Number(amount).toLocaleString('es-CL')
}
</script>

<style scoped>
.sw {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  margin: 0 16px 8px;
}

.idel-bg {
  position: absolute;
  inset: 0;
  background: #FFEBEE;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 22px;
  gap: 6px;
  color: #E53935;
  font-size: 12px;
  font-weight: 700;
}

.icard {
  background: #fff;
  border: 1px solid #E8ECF2;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
  transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  z-index: 1;
  cursor: pointer;
}
.icard.checked {
  background: #F0FBF0;
  border-color: #C8E6C9;
}

.ii {
  display: flex;
  align-items: center;
  padding: 12px 14px;
  gap: 12px;
}

.chk {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 2px solid #E8ECF2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}
.icard.checked .chk {
  background: #43A047;
  border-color: #43A047;
}

.iinfo { flex: 1; min-width: 0; }
.iname {
  font-family: 'Nunito', sans-serif;
  font-weight: 700;
  font-size: 14px;
  color: #1A2332;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.icard.checked .iname { color: #9AABBB; text-decoration: line-through; }

.isub { font-size: 11px; color: #9AABBB; }

.ipr { text-align: right; flex-shrink: 0; }
.itotal {
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: 14px;
  color: #1A2332;
}
.icard.checked .itotal { color: #9AABBB; }
.ippu { font-size: 11px; color: #9AABBB; }

.edit-hint {
  opacity: 0;
  transition: opacity 0.2s;
  flex-shrink: 0;
}
.icard:hover .edit-hint { opacity: 1; }

.iby {
  padding: 5px 14px 8px 52px;
  font-size: 10px;
  color: #9AABBB;
  display: flex;
  align-items: center;
  gap: 4px;
}

.av {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 6px;
  font-weight: 800;
  color: white;
}
</style>
