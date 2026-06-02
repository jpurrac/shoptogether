<template>
  <!-- Wrapper con fondo rojo de "eliminar" -->
  <div class="lcw" :id="`lcw-${list.id}`">
    <!-- Fondo que aparece al deslizar -->
    <div class="ldel-bg">
      <TrashIcon /> Eliminar
    </div>
    
    <!-- Botón ⋯ opciones -->
    <button class="opts-btn" ref="optsBtn" @click.stop="toggleCtx">
      <DotsVerticalIcon />
    </button>

    <!-- Menú contextual (Teleport fuera del overflow:hidden) -->
    <Teleport to="body">
      <Transition name="ctx">
        <div
          v-if="ctxOpen"
          class="ctx-menu"
          :style="ctxStyle"
          v-click-outside="closeCtx"
        >
          <button class="ctx-item" @click="onEdit">
            <PencilIcon /> Editar lista
          </button>
          <button class="ctx-item danger" @click="onDelete">
            <TrashIcon /> Eliminar lista
          </button>
        </div>
      </Transition>
    </Teleport>

    <!-- Tarjeta principal -->
    <div
      class="lcard"
      :class="{ closed: list.status === 'paid' }"
      :id="`lc-${list.id}`"
      @click="onClick"
      @touchstart="onTouchStart($event, `lc-${list.id}`)"
      @touchmove="onTouchMove($event, `lc-${list.id}`)"
      @touchend="onTouchEnd($event, `lc-${list.id}`)"
    >
      <!-- Barra izquierda de color (via ::before en CSS) -->
      <div class="lc-top">
        <div>
          <div class="lc-name">{{ list.name }}</div>
          <div class="lc-meta">
            <span class="lc-date">{{ formatDate(list.date) }}</span>
            <span v-if="list.shared" class="badge badge-orange">Compartida</span>
            <span v-if="list.status === 'paid'" class="badge badge-green">✓ Pagada</span>
          </div>
        </div>
        <div class="lc-right">
          <div class="lc-items" v-if="list.item_count > 0">{{ list.item_count }} prods.</div>
          <div class="lc-budget" :style="list.budget ? '' : 'color:var(--t3)'">
            {{ list.budget ? formatMoney(list.budget) : 'Sin presup.' }}
          </div>
        </div>
      </div>

      <!-- Barra de progreso -->
      <div class="lc-progress" v-if="list.item_count > 0">
        <div class="lc-pr-row">
          <!-- <span class="lc-pr-label">{{ list.checked_count }} de {{ list.item_count }} en carrito</span> -->
          <span class="lc-pr-val" :class="{ done: progress === 100 }">
            {{ progress }}%{{ progress === 100 ? ' ✓' : '' }}
          </span>
        </div>
        <div class="prog-track">
          <div
            class="prog-fill"
            :style="{
              width: `${progress}%`,
              background: progress === 100 ? 'var(--green-grad)' : 'var(--blue-grad)'
            }"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { h, ref, computed, reactive } from 'vue'
import { useSwipe } from '../composables/useSwipe'

const p = (d, size=14) => h('svg', { width:size, height:size, fill:'none', stroke:'currentColor', 'stroke-width':2, viewBox:'0 0 24 24' }, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d })])
const TrashIcon        = () => p('M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16')
const PencilIcon       = () => p('M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z')
const DotsVerticalIcon = () => h('svg', { width:16, height:16, fill:'currentColor', viewBox:'0 0 24 24' }, ['5','12','19'].map(cy => h('circle', { cx:12, cy, r:1.5 })))

const props = defineProps({
  list: { type: Object, required: true }
})

const emit = defineEmits(['click', 'edit', 'delete'])

// ── Context menu ──
const ctxOpen = ref(false)
const optsBtn = ref(null)
const ctxStyle = reactive({ position: 'fixed', top: '0px', right: '0px', zIndex: 9999 })

function toggleCtx() {
  if (!ctxOpen.value && optsBtn.value) {
    const r = optsBtn.value.getBoundingClientRect()
    ctxStyle.top   = (r.bottom + 6) + 'px'
    ctxStyle.right = (window.innerWidth - r.right) + 'px'
  }
  ctxOpen.value = !ctxOpen.value
}
function closeCtx() { ctxOpen.value = false }
function onEdit() { closeCtx(); emit('edit', props.list) }
function onDelete() { closeCtx(); emit('delete', props.list) }

// ── Swipe ──
const { onTouchStart, onTouchMove, onTouchEnd } = useSwipe((id) => {
  emit('delete', props.list)
})

// ── Click: solo navegar si no hubo swipe ──
function onClick() {
  const el = document.getElementById(`lc-${props.list.id}`)
  if (el?.style.transform && el.style.transform !== 'translateX(0px)') {
    el.style.transform = ''
    return
  }
  emit('click', props.list)
}

// ── Helpers ──
const progress = computed(() => {
  if (!props.list.item_count) return 0
  return Math.round((props.list.checked_count / props.list.item_count) * 100)
})

function formatDate(dateStr) {
  if(!dateStr) return;
  return new Date(dateStr).toLocaleDateString('es-CL', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatMoney(amount) {
  return '$' + Number(amount).toLocaleString('es-CL')
}
</script>

<style scoped>
.lcw {
  position: relative;
  overflow: hidden;
  border-radius: 18px;
  margin: 0 16px 10px;
}

.ldel-bg {
  position: absolute;
  inset: 0;
  background: #FFEBEE;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 22px;
  gap: 6px;
  color: #E53935;
  font-size: 12px;
  font-weight: 700;
}

.opts-btn {
  position: absolute;
  right: 10px;
  top: 10px;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  border: none;
  background: #F4F6FA;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 5;
  color: #9AABBB;
}

.ctx-menu {
  background: white;
  border: 1px solid #E8ECF2;
  border-radius: 14px;
  box-shadow: 0 8px 32px rgba(0,0,0,.14);
  z-index: 10;
  overflow: hidden;
  min-width: 164px;
}

.ctx-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  font-size: 13px;
  font-weight: 600;
  color: #1A2332;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}
.ctx-item:hover { background: #F4F6FA; }
.ctx-item.danger { color: #E53935; }
.ctx-item + .ctx-item { border-top: 1px solid #E8ECF2; }

.lcard {
  background: #fff;
  border: 1px solid #E8ECF2;
  border-radius: 18px;
  padding: 14px 16px;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
  transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  z-index: 1;
  overflow: hidden;
}
.lcard::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 4px;
  background: var(--blue-grad);
  border-radius: 18px 0 0 18px;
}
.lcard.closed::before { background: var(--green-grad); }
.lcard.closed { opacity: 0.7; }

.lc-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
  padding-left: 8px;
  padding-right: 30px;
}
.lc-name {
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: 15px;
  color: #1A2332;
  margin-bottom: 4px;
}
.lc-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.lc-date { font-size: 11px; color: #9AABBB; }
.lc-right { text-align: right; flex-shrink: 0; margin-left: 12px; }
.lc-items { font-size: 11px; color: #9AABBB; margin-bottom: 2px; }
.lc-budget { font-family: 'Nunito', sans-serif; font-weight: 700; font-size: 14px; color: #1A2332; }

.lc-progress { padding-left: 8px; }
.lc-pr-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
.lc-pr-label { font-size: 11px; color: #9AABBB; }
.lc-pr-val { font-size: 11px; font-weight: 700; color: #1565C0; }
.lc-pr-val.done { color: #43A047; }

.prog-track { height: 5px; background: #E8ECF2; border-radius: 99px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 99px; }

.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 99px;
  font-size: 10px;
  font-weight: 700;
}
.badge-orange { background: #FFF3E0; color: #E65100; }
.badge-green  { background: #E8F5E9; color: #2E7D32; }

/* Ctx menu transition */
.ctx-enter-active, .ctx-leave-active { transition: opacity .15s, transform .15s; }
.ctx-enter-from, .ctx-leave-to { opacity: 0; transform: scale(.95) translateY(-6px); }
</style>
