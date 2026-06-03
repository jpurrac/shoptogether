<template>
  <div class="screen">
    <!-- ── Navbar ── -->
    <header class="navbar" style="position:relative;">
      <div class="nav-left">
        <button class="back-btn" @click="router.back()">
          <ChevronLeftIcon />
        </button>
        <span class="nav-title">{{ list?.name }}</span>
      </div>
      <div class="nav-right">
        <!-- Avatares colaboradores -->
        <div class="avatars" @click="showCollab = true">
          <div
            v-for="member in list?.members?.slice(0, 3)"
            :key="member.id"
            class="av"
            :style="{ background: member.color }"
          >{{ member.initials }}</div>
        </div>
        <!-- Menú ⋯ -->
        <button class="opts-btn" @click.stop="showDetailCtx = !showDetailCtx">
          <DotsVerticalIcon />
        </button>
        <Transition name="ctx">
          <div v-if="showDetailCtx" class="ctx-menu" v-click-outside="() => showDetailCtx = false">
            <button class="ctx-item" @click="showDetailCtx = false; showCollab = true">
              <UsersIcon /> Colaboradores
            </button>
            <button class="ctx-item" @click="showDetailCtx = false; showEditList = true">
              <PencilIcon /> Editar lista
            </button>
            <button class="ctx-item" @click="showDetailCtx = false; showClose = true">
              <CheckCircleIcon /> Cerrar lista
            </button>
            <button class="ctx-item danger" @click="showDetailCtx = false; showDeleteList = true">
              <TrashIcon /> Eliminar lista
            </button>
          </div>
        </Transition>
      </div>
    </header>

    <!-- ── Barra de presupuesto ── -->
    <div class="detail-header">
      <div class="bb">
        <div class="bb-top">
          <div>
            <span class="bb-cur">{{ formatMoney(spent) }}</span>
            <span class="bb-of" v-if="list?.budget">de {{ formatMoney(list.budget) }}</span>
            <span class="bb-of" v-else>en carrito</span>
          </div>
          <div class="bb-right" v-if="list?.budget">
            <div class="bb-avail-label">Disponible</div>
            <div class="bb-avail">{{ formatMoney(list.budget - spent) }}</div>
          </div>
        </div>
        <div class="prog-track" style="height:8px;" v-if="list?.budget">
          <div class="prog-fill" :style="{ width: budgetPct + '%', background: 'var(--blue-grad)' }" />
        </div>
        <div class="bb-status" v-if="list?.budget" :style="{ color: budgetPct <= 100 ? '#43A047' : '#E53935' }">
          {{ budgetPct <= 100 ? '✓ Dentro del presupuesto' : '⚠ Sobre el presupuesto' }}
        </div>
      </div>
    </div>

    <div style="height:8px;" />


    <!-- ── PENDIENTES ── -->
    <div class="ish">
      <span class="ishl">📋 Pendientes</span>
      <span class="ishc">{{ pendingItems.length }} productos</span>
    </div>

    <ItemCard
      v-for="item in pendingItems"
      :key="item.id"
      :item="item"
      @toggle="onToggle"
      @edit="openEditItem"
      @delete="openDeleteItem"
    />

    <div v-if="pendingItems.length === 0" class="empty-state">
      <div style="font-size:13px;color:#9AABBB;padding:16px 18px;">
        Todos los productos están en el carrito 🎉
      </div>
    </div>

    <!-- ── EN CARRITO ── -->
    <div v-if="checkedItems.length > 0" class="ish">
      <span class="ishl" style="color:#43A047;">🛒 En carrito</span>
      <div style="display:flex;align-items:center;gap:8px;">
        <span class="ishc">{{ checkedItems.length }} productos</span>
        <button class="isht" @click="cartVisible = !cartVisible">
          {{ cartVisible ? 'Ocultar' : 'Mostrar' }}
        </button>
      </div>
    </div>

    <template v-if="cartVisible">
      <ItemCard
        v-for="item in checkedItems"
        :key="item.id"
        :item="item"
        @toggle="onToggle"
        @edit="openEditItem"
        @delete="openDeleteItem"
      />
    </template>

    <div style="height:130px;" />

    <!-- ── Banner "¡Todo en carrito!" ── -->
    <Transition name="banner">
      <div v-if="allChecked" class="fin-banner">
        <div class="fb-icon">✅</div>
        <div class="fb-text">
          <div class="fb-title">¡Todo en el carrito!</div>
          <div class="fb-sub">Listo para pagar</div>
        </div>
        <button class="fb-btn" @click="showClose = true">Pagar →</button>
      </div>
    </Transition>

    <!-- ── FAB agregar (siempre visible) ── -->
    <div class="float-actions">
      <button class="btn btn-primary" @click="showAddItem = true">
        <PlusIcon /> Agregar producto
      </button>
    </div>
    <div style="height:80px;" />

    <!-- ── Modales ── -->
    <EditListSheet   v-model="showEditList"   :list="list"          @submit="onEditList" />
    <EditItemSheet   v-model="showEditItem"   :item="editingItem"   @submit="onEditItem" />
    <DeleteItemSheet v-model="showDeleteItem" :item="deletingItem"  @confirm="onDeleteItem" />
    <CloseListSheet  v-model="showClose"      :list="list"          :items="items"  @submit="onCloseList" />
    <DeleteListSheet v-model="showDeleteList" :list="list"          @confirm="onDeleteList" />

    <AddItemSheet v-model="showAddItem" @submit="onAddItem" />

    <!-- Modal colaboradores -->
    <BottomSheet v-model="showCollab">
      <h2 class="sh-title">Colaboradores</h2>
      <div class="members-list">
        <div v-for="member in list?.members" :key="member.id" class="member-row">
          <div class="av lg" :style="{ background: member.color }">{{ member.initials }}</div>
          <div style="flex:1;">
            <div class="member-name">{{ member.name }}</div>
            <div class="member-email">{{ member.email }}</div>
          </div>
          <span class="badge" :class="member.role === 'owner' ? 'badge-blue' : 'badge-orange'">
            {{ member.role === 'owner' ? '★ Propietario' : 'Editor' }}
          </span>
        </div>
      </div>
      <div class="code-box">
        <div>
          <div style="font-size:11px;color:#9AABBB;margin-bottom:3px;">Código de acceso</div>
          <div class="access-code">{{ list?.code }}</div>
        </div>
        <button class="copy-btn" @click="copyCode">Copiar</button>
      </div>
    </BottomSheet>
  </div>
</template>

<script setup>
import { h, ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useListSocket } from '../composables/useListSocket.js'
import { usePolling }    from '../composables/usePolling.js'
import ItemCard from '../components/ItemCard.vue'
import BottomSheet from '../components/BottomSheet.vue'
import AddItemSheet from '../components/modals/AddItemSheet.vue'
import EditListSheet from '../components/modals/EditListSheet.vue'
import EditItemSheet from '../components/modals/EditItemSheet.vue'
import DeleteItemSheet from '../components/modals/DeleteItemSheet.vue'
import CloseListSheet from '../components/modals/CloseListSheet.vue'
import DeleteListSheet from '../components/modals/DeleteListSheet.vue'
import api from '../service/axiosInstance'
import user from '../stores/user.js'
import { Alert } from '../stores/alert.js'

const s = (d, extra={}) => h('svg', { width:16, height:16, fill:'none', stroke:'currentColor', 'stroke-width':2, viewBox:'0 0 24 24', ...extra }, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d })])
const ChevronLeftIcon  = () => s('M15 19l-7-7 7-7', { 'stroke-width': 2.5 })
const DotsVerticalIcon = () => h('svg', { width:16, height:16, fill:'currentColor', viewBox:'0 0 24 24' }, ['5','12','19'].map(cy => h('circle', { cx:12, cy, r:1.5 })))
const PlusIcon         = () => s('M12 4v16m8-8H4', { 'stroke-width': 2.5 })
const TrashIcon        = () => s('M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', { width:14, height:14 })
const PencilIcon       = () => s('M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', { width:14, height:14 })
const CheckCircleIcon  = () => s('M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', { width:14, height:14 })
const UsersIcon        = () => s('M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', { width:14, height:14 })

const router = useRouter()
const route  = useRoute()

const list  = ref(null)
const items = ref([])

const pendingItems = computed(() => items.value.filter(i => !i.checked))
const checkedItems = computed(() => items.value.filter(i => i.checked))
const allChecked   = computed(() => items.value.length > 0 && items.value.every(i => i.checked))
const spent        = computed(() => items.value.filter(i => i.checked).reduce((s, i) => s + i.price * i.qty, 0))
const budgetPct    = computed(() => list.value?.budget ? Math.min(Math.round((spent.value / list.value.budget) * 100), 100) : 0)

const cartVisible    = ref(true)
const showDetailCtx  = ref(false)
const showCollab     = ref(false)
const showClose      = ref(false)
const showAddItem    = ref(false)
const showEditList   = ref(false)
const showEditItem   = ref(false)
const showDeleteItem = ref(false)
const showDeleteList = ref(false)

const editingItem  = ref(null)
const deletingItem = ref(null)

function handleSocketEvent(type, payload) {
  if (type === 'item_created') {
    if (!items.value.find(i => i.id === payload.id))
      items.value.push(payload)
  } else if (type === 'item_updated') {
    const idx = items.value.findIndex(i => i.id === payload.id)
    if (idx !== -1) items.value[idx] = { ...items.value[idx], ...payload }
  } else if (type === 'item_toggled') {
    const idx = items.value.findIndex(i => i.id === payload.id)
    if (idx !== -1) items.value[idx] = { ...items.value[idx], ...payload }
  } else if (type === 'item_deleted') {
    items.value = items.value.filter(i => i.id !== payload.id)
  }
}

// Polling silencioso (no activa loader, solo en listas compartidas)
async function refreshItems() {
  const { data } = await api.get(`/lists/${route.params.id}/items`, { _silent: true })
  items.value = data
}

// El stop del polling se guarda aquí para llamarlo en onUnmounted
let stopPolling = null

async function fetchList() {
  try {
    const { data } = await api.get(`/lists/${route.params.id}`)
    list.value  = data
    items.value = data.items ?? []

    const isShared      = (data.members?.length ?? 0) > 1
    const socketEnabled = import.meta.env.VITE_SOCKET_ENABLED !== 'false'

    if (isShared) {
      if (socketEnabled) {
        useListSocket(data.id, true, handleSocketEvent)
      } else {
        const { stop } = usePolling(refreshItems, 10_000)
        stopPolling = stop
      }
    }
  } catch {
    Alert({ msg: 'Error al cargar la lista', color: 'red' })
    router.push({ name: 'lists' })
  }
}

onMounted(fetchList)

onUnmounted(() => {
  stopPolling?.()
})

async function onToggle(item) {
  try {
    const { data } = await api.patch(`/items/${item.id}/toggle`)
    const idx = items.value.findIndex(i => i.id === item.id)
    if (idx !== -1) items.value[idx] = { ...items.value[idx], checked: data.checked }
  } catch {
    Alert({ msg: 'Error al actualizar el producto', color: 'red' })
  }
}

function openEditItem(item)   { editingItem.value = item;   showEditItem.value = true }
function openDeleteItem(item) { deletingItem.value = item;  showDeleteItem.value = true }

async function onEditList(data) {
  try {
    const { data: updated } = await api.put(`/lists/${list.value.id}`, data)
    list.value = { ...list.value, ...updated }
    Alert({ msg: 'Lista actualizada', color: 'green' })
  } catch {
    Alert({ msg: 'Error al actualizar la lista', color: 'red' })
  }
}

async function onEditItem(updated) {
  try {
    const { data } = await api.put(`/items/${updated.id}`, updated)
    const idx = items.value.findIndex(i => i.id === data.id)
    if (idx !== -1) items.value[idx] = data
    Alert({ msg: 'Producto actualizado', color: 'green' })
  } catch {
    Alert({ msg: 'Error al actualizar el producto', color: 'red' })
  }
}

async function onDeleteItem(item) {
  try {
    await api.delete(`/items/${item.id}`)
    items.value = items.value.filter(i => i.id !== item.id)
    Alert({ msg: 'Producto eliminado', color: 'green' })
  } catch {
    Alert({ msg: 'Error al eliminar el producto', color: 'red' })
  }
}

async function onAddItem(item) {
  try {
    const { data } = await api.post(`/lists/${list.value.id}/items`, item)
    items.value.push(data)
    Alert({ msg: 'Producto agregado', color: 'green' })
  } catch {
    Alert({ msg: 'Error al agregar el producto', color: 'red' })
  }
}

async function onCloseList(data) {
  try {
    await api.put(`/lists/${list.value.id}/close`, data)
    Alert({ msg: 'Lista cerrada', color: 'green' })
    router.push({ name: 'lists' })
  } catch {
    Alert({ msg: 'Error al cerrar la lista', color: 'red' })
  }
}

async function onDeleteList() {
  try {
    await api.delete(`/lists/${list.value.id}`)
    Alert({ msg: 'Lista eliminada', color: 'green' })
    router.push({ name: 'lists' })
  } catch {
    Alert({ msg: 'Error al eliminar la lista', color: 'red' })
  }
}

function copyCode() {
  navigator.clipboard.writeText(list.value.code)
  Alert({ msg: 'Código copiado', color: 'blue' })
}

function formatMoney(v) {
  return v ? '$' + Number(v).toLocaleString('es-CL') : '-'
}
</script>

<style scoped>
.screen { min-height:100vh; background:#F4F6FA; }

.navbar {
  position:sticky; top:0; z-index:50;
  background:rgba(255,255,255,.92); backdrop-filter:blur(16px);
  border-bottom:1px solid #E8ECF2; padding:12px 18px;
  display:flex; align-items:center; justify-content:space-between;
}
.nav-left  { display:flex; align-items:center; gap:10px; }
.nav-right { display:flex; align-items:center; gap:6px; position:relative; }
.nav-title { font-family:'Nunito',sans-serif; font-weight:800; font-size:16px; color:#1A2332; }

.back-btn {
  width:32px; height:32px; border-radius:10px; border:none;
  background:#F4F6FA; display:flex; align-items:center; justify-content:center; cursor:pointer;
}

.avatars { display:flex; }
.av {
  width:32px; height:32px; border-radius:50%; border:2px solid white;
  display:flex; align-items:center; justify-content:center;
  font-family:'Nunito',sans-serif; font-weight:800; font-size:11px; color:white;
  cursor:pointer;
}
.av + .av { margin-left:-8px; }
.av.lg { width:48px; height:48px; font-size:16px; margin:0; border:none; }

.opts-btn {
  width:28px; height:28px; border-radius:8px; border:none;
  background:#F4F6FA; display:flex; align-items:center; justify-content:center;
  cursor:pointer; color:#9AABBB;
}
.ctx-menu {
  position:absolute; right:4px; top:36px;
  background:white; border:1px solid #E8ECF2; border-radius:14px;
  box-shadow:0 8px 32px rgba(0,0,0,.14); z-index:10; overflow:hidden; min-width:164px;
}
.ctx-item { display:flex; align-items:center; gap:10px; padding:11px 14px; font-size:13px; font-weight:600; color:#1A2332; cursor:pointer; border:none; background:none; width:100%; text-align:left; }
.ctx-item:hover { background:#F4F6FA; }
.ctx-item.danger { color:#E53935; }
.ctx-item + .ctx-item { border-top:1px solid #E8ECF2; }

.detail-header { background:white; border-bottom:1px solid #E8ECF2; padding:14px 18px 16px; }
.bb { background:#F4F6FA; border:1px solid #E8ECF2; border-radius:14px; padding:12px 14px; }
.bb-top { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:8px; }
.bb-cur { font-family:'Nunito',sans-serif; font-weight:900; font-size:24px; color:#1A2332; line-height:1; }
.bb-of  { font-size:13px; color:#9AABBB; margin-left:4px; }
.bb-right { text-align:right; }
.bb-avail-label { font-size:11px; color:#9AABBB; margin-bottom:2px; }
.bb-avail { font-family:'Nunito',sans-serif; font-weight:800; font-size:14px; color:#1A2332; }
.bb-status { font-size:12px; font-weight:600; margin-top:5px; }

.prog-track { height:5px; background:#E8ECF2; border-radius:99px; overflow:hidden; }
.prog-fill  { height:100%; border-radius:99px; }


.ish { display:flex; align-items:center; justify-content:space-between; padding:10px 18px 8px; }
.ishl { font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#9AABBB; display:flex; align-items:center; gap:6px; }
.ishc { font-size:11px; font-weight:600; color:#9AABBB; }
.isht { font-size:11px; font-weight:600; color:#1565C0; border:none; background:none; cursor:pointer; }

.empty-state { text-align:center; color:#9AABBB; }

.fin-banner {
  margin:0 16px 8px; background:linear-gradient(135deg,#43A047,#66BB6A);
  border-radius:18px; padding:16px; display:flex; align-items:center; gap:12px;
  box-shadow:0 4px 20px rgba(67,160,71,.3);
}
.fb-icon { width:40px; height:40px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; }
.fb-text { flex:1; }
.fb-title { font-family:'Nunito',sans-serif; font-weight:800; font-size:14px; color:white; }
.fb-sub   { font-size:11px; color:rgba(255,255,255,.8); }
.fb-btn   { background:white; border:none; border-radius:10px; padding:8px 14px; font-family:'Nunito',sans-serif; font-weight:800; font-size:12px; color:#43A047; cursor:pointer; }

.float-actions { position:sticky; bottom:0; padding:10px 16px 28px; background:linear-gradient(to top,#F4F6FA 60%,transparent); }
.btn { width:100%; padding:14px; border:none; border-radius:14px; font-family:'Nunito',sans-serif; font-weight:800; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-primary { background:linear-gradient(135deg,#1565C0,#00ACC1); color:white; box-shadow:0 4px 16px rgba(21,101,192,.3); }

.members-list { display:flex; flex-direction:column; gap:8px; margin-bottom:20px; }
.member-row { display:flex; align-items:center; gap:12px; padding:12px 14px; border:1px solid #E8ECF2; border-radius:16px; background:#F4F6FA; }
.member-name  { font-family:'Nunito',sans-serif; font-weight:700; font-size:14px; color:#1A2332; }
.member-email { font-size:11px; color:#9AABBB; }
.badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:700; }
.badge-blue   { background:#E3F2FD; color:#1565C0; }
.badge-orange { background:#FFF3E0; color:#E65100; }

.code-box { background:#F4F6FA; border:1px solid #E8ECF2; border-radius:14px; padding:14px; display:flex; align-items:center; justify-content:space-between; }
.access-code { font-family:'Nunito',sans-serif; font-weight:800; font-size:22px; color:#1A2332; letter-spacing:.15em; }
.copy-btn { padding:8px 14px; border:1.5px solid #E8ECF2; border-radius:10px; background:white; font-weight:600; font-size:12px; color:#5A6B82; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; }

/* Ctx menu transition */
.ctx-enter-active, .ctx-leave-active { transition:opacity .15s, transform .15s; }
.ctx-enter-from, .ctx-leave-to { opacity:0; transform:scale(.95) translateY(-6px); }

/* Banner transition */
.banner-enter-active, .banner-leave-active { transition:opacity .3s, transform .3s; }
.banner-enter-from, .banner-leave-to { opacity:0; transform:translateY(10px); }
</style>
