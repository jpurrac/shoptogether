<template>
  <div class="screen">
    <!-- ── Navbar ── -->
    <header class="navbar">
      <div class="logo">
        <img src="../assets/logo-icon.png" alt="ShopTogether" class="logo-icon" />
        <div class="logo-text">
          <span class="s1">Shop</span><span class="s2">Together</span>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <button class="stats-btn" @click="router.push({ name: 'stats' })" title="Estadísticas">
          <StatsIcon />
        </button>
        <div class="av" :style="{ background: user.color || 'var(--blue-grad)' }" @click="showUser = true">
          {{ user.initials }}
        </div>
      </div>
    </header>

    <!-- ── Saludo ── -->
    <div class="greeting">
      <div class="greeting-sub">Hola, {{ user.name }} 👋</div>
      <div class="greeting-title">Mis listas</div>
    </div>

    <!-- ── Sección Activas ── -->
    <div class="sh2">
      <span class="st">Activas</span>
      <span class="sc">{{ activeLists.length }}</span>
    </div>

    <ListCard
      v-for="list in activeLists"
      :key="list.id"
      :list="list"
      @click="goToDetail(list)"
      @edit="openEdit(list)"
      @delete="openDelete(list)"
    />

    <div v-if="activeLists.length === 0" class="empty-state">
      <div class="empty-icon">🛒</div>
      <div class="empty-text">No tienes listas activas</div>
      <div class="empty-sub">Crea una nueva o únete a una existente</div>
    </div>

    <!-- ── Sección Historial ── -->
    <div v-if="historyLists.length > 0" class="sh2" style="margin-top:6px;">
      <span class="st">Historial</span>
      <span class="sc">{{ historyLists.length }} pagadas</span>
    </div>

    <ListCard
      v-for="list in historyLists"
      :key="list.id"
      :list="list"
    />

    <div style="height:16px;" />

    <!-- ── FAB area ── -->
    <div class="fab-wrap">
      <div class="fab-row">
        <button class="fab-ghost" @click="showJoin = true">
          <LinkIcon /> Unirme
        </button>
        <button class="fab" @click="showNew = true">
          <PlusIcon /> Nueva lista
        </button>
      </div>
    </div>

    <!-- ── Modales ── -->
    <NewListSheet    v-model="showNew"    @submit="onCreateList" />
    <EditListSheet   v-model="showEdit"   :list="editingList"   @submit="onEditList" />
    <DeleteListSheet v-model="showDelete" :list="deletingList"  @confirm="onDeleteList" />
    <JoinListSheet   v-model="showJoin"   @submit="onJoinList" />

    <!-- Modal usuario -->
    <BottomSheet v-model="showUser">
      <div class="user-header">
        <div class="av lg" :style="{ background: user.color || 'var(--blue-grad)' }">{{ user.initials }}</div>
        <div>
          <div class="user-name">{{ user.name }}</div>
          <div class="user-email">{{ user.email }}</div>
        </div>
      </div>
      <button class="btn btn-danger" @click="logout">Cerrar sesión</button>
    </BottomSheet>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ListCard from '../components/ListCard.vue'
import BottomSheet from '../components/BottomSheet.vue'
import NewListSheet from '../components/modals/NewListSheet.vue'
import EditListSheet from '../components/modals/EditListSheet.vue'
import DeleteListSheet from '../components/modals/DeleteListSheet.vue'
import JoinListSheet from '../components/modals/JoinListSheet.vue'
import api, { ClearAuthToken } from '../service/axiosInstance'
import user from '../stores/user.js'
import { Alert } from '../stores/alert.js'

import { h } from 'vue'
const PlusIcon  = () => h('svg', { width:18, height:18, fill:'none', stroke:'currentColor', 'stroke-width':2.5, viewBox:'0 0 24 24' }, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d:'M12 4v16m8-8H4' })])
const LinkIcon  = () => h('svg', { width:16, height:16, fill:'none', stroke:'currentColor', 'stroke-width':2, viewBox:'0 0 24 24' }, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d:'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1' })])
const StatsIcon = () => h('svg', { width:17, height:17, fill:'none', stroke:'currentColor', 'stroke-width':2, viewBox:'0 0 24 24' }, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d:'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })])

const router = useRouter()

const lists   = ref([])
const loading = ref(false)

const activeLists  = computed(() => lists.value.filter(l => l.status === 'active'))
const historyLists = computed(() => lists.value.filter(l => l.status === 'paid'))

const showNew    = ref(false)
const showEdit   = ref(false)
const showDelete = ref(false)
const showJoin   = ref(false)
const showUser   = ref(false)

const editingList  = ref(null)
const deletingList = ref(null)

async function fetchLists() {
  loading.value = true
  try {
    const { data } = await api.get('/lists')
    lists.value = data
  } catch {
    Alert({ msg: 'Error al cargar las listas', color: 'red' })
  } finally {
    loading.value = false
  }
}

onMounted(fetchLists)

function openEdit(list)   { editingList.value = list;  showEdit.value = true }
function openDelete(list) { deletingList.value = list; showDelete.value = true }
function goToDetail(list) { router.push({ name: 'detail', params: { id: list.id } }) }

async function onCreateList(data) {
  try {
    const { data: newList } = await api.post('/lists', data)
    lists.value.unshift(newList)
    router.push({ name: 'detail', params: { id: newList.id } })
  } catch {
    Alert({ msg: 'Error al crear la lista', color: 'red' })
  }
}

async function onEditList(data) {
  try {
    const { data: updated } = await api.put(`/lists/${data.id}`, data)
    const idx = lists.value.findIndex(l => l.id === updated.id)
    if (idx !== -1) lists.value[idx] = updated
    Alert({ msg: 'Lista actualizada', color: 'green' })
  } catch {
    Alert({ msg: 'Error al actualizar la lista', color: 'red' })
  }
}

async function onDeleteList(list) {
  try {
    await api.delete(`/lists/${list.id}`)
    lists.value = lists.value.filter(l => l.id !== list.id)
    Alert({ msg: 'Lista eliminada', color: 'green' })
  } catch {
    Alert({ msg: 'Error al eliminar la lista', color: 'red' })
  }
}

async function onJoinList(code) {
  try {
    const { data: joined } = await api.post('/lists/join', { code })
    lists.value.unshift(joined)
    Alert({ msg: `Te uniste a "${joined.name}"`, color: 'green' })
  } catch (err) {
    Alert({ msg: err.response?.data?.message ?? 'Código inválido', color: 'red' })
  }
}

async function logout() {
  try { await api.post('/auth/logout') } catch {}
  ClearAuthToken()
  router.push({ name: 'login' })
}
</script>

<style scoped>
.btn {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 14px;
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-danger { background: #FFF1F0; color: #E53935; border: 1.5px solid #FFCDD2; }

.screen {
  min-height: 100vh;
  background: #F4F6FA;
  display: flex;
  flex-direction: column;
}

.navbar {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(255,255,255,.92);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid #E8ECF2;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo { display:flex; align-items:center; gap:8px; }
.logo-icon { width:34px; height:34px; border-radius:10px; object-fit:cover; }
.logo-text { font-family:'Nunito',sans-serif; font-weight:800; font-size:17px; line-height:1; }
.s1 { color:#1D3461; } .s2 { color:#43A047; }

.stats-btn {
  width: 32px; height: 32px; border-radius: 10px; border: 1.5px solid #E8ECF2;
  background: white; display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #5A6B82;
}
.stats-btn:hover { background: #F4F6FA; color: #1565C0; }

.av {
  width:32px; height:32px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-family:'Nunito',sans-serif; font-weight:800; font-size:11px;
  color:white; cursor:pointer;
}
.av.lg { width:48px; height:48px; font-size:16px; }

.greeting { padding:16px 18px 8px; }
.greeting-sub  { font-size:13px; color:#9AABBB; margin-bottom:2px; }
.greeting-title { font-family:'Nunito',sans-serif; font-weight:900; font-size:22px; color:#1A2332; }

.sh2 { padding:14px 18px 8px; display:flex; align-items:center; justify-content:space-between; }
.st  { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#9AABBB; }
.sc  { font-size:11px; font-weight:600; color:#9AABBB; }

.empty-state { text-align:center; padding:40px 24px; }
.empty-icon  { font-size:48px; margin-bottom:12px; }
.empty-text  { font-family:'Nunito',sans-serif; font-weight:700; font-size:16px; color:#5A6B82; margin-bottom:6px; }
.empty-sub   { font-size:13px; color:#9AABBB; }

.fab-wrap { position:sticky; bottom:0; padding:10px 16px 28px; background:linear-gradient(to top,#F4F6FA 65%,transparent); }
.fab-row  { display:flex; gap:8px; }

.fab {
  flex:1; height:52px;
  background: linear-gradient(135deg,#1565C0,#00ACC1);
  border:none; border-radius:18px; color:white;
  font-family:'Nunito',sans-serif; font-weight:800; font-size:15px;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 20px rgba(21,101,192,.35);
}
.fab-ghost {
  height:52px; padding:0 18px;
  background:white; border:1.5px solid #E8ECF2; border-radius:18px;
  font-family:'Nunito',sans-serif; font-weight:700; font-size:13px; color:#5A6B82;
  cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap;
  box-shadow:0 1px 3px rgba(0,0,0,.06);
}

.user-header { display:flex; align-items:center; gap:14px; padding-bottom:20px; border-bottom:1px solid #E8ECF2; margin-bottom:16px; }
.user-name  { font-family:'Nunito',sans-serif; font-weight:800; font-size:16px; color:#1A2332; }
.user-email { font-size:12px; color:#9AABBB; }
</style>
