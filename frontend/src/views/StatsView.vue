<template>
  <div class="screen">
    <!-- Navbar -->
    <header class="navbar">
      <button class="back-btn" @click="router.push({ name: 'lists' })">
        <ChevronIcon />
      </button>
      <span class="nav-title">Estadísticas</span>
      <div style="width:32px" />
    </header>

    <!-- Sin datos -->
    <div v-if="!loading && !stats.total_lists" class="empty">
      <div class="empty-icon">📊</div>
      <div class="empty-title">Sin historial aún</div>
      <div class="empty-sub">Cierra tu primera lista para ver estadísticas</div>
    </div>

    <template v-else-if="stats.total_lists">

      <!-- ── Resumen ── -->
      <div class="section-title">Resumen</div>
      <div class="cards">
        <div class="card card-blue">
          <div class="card-label">Total gastado</div>
          <div class="card-value">{{ fmt(stats.total_spent) }}</div>
          <div class="card-sub">en {{ stats.total_lists }} lista{{ stats.total_lists !== 1 ? 's' : '' }}</div>
        </div>
        <div class="card card-green">
          <div class="card-label">Promedio por lista</div>
          <div class="card-value">{{ fmt(stats.average_spent) }}</div>
          <div class="card-sub">por compra</div>
        </div>
      </div>
      <div class="cards" style="margin-top:0">
        <div class="card card-orange">
          <div class="card-label">Mayor gasto</div>
          <div class="card-value">{{ fmt(stats.max_spent) }}</div>
        </div>
        <div class="card card-teal">
          <div class="card-label">Menor gasto</div>
          <div class="card-value">{{ fmt(stats.min_spent) }}</div>
        </div>
      </div>

      <!-- ── Evolución ── -->
      <div class="section-title" style="margin-top:8px">Evolución del gasto</div>
      <div class="chart-wrap" v-if="stats.evolution?.length > 1">
        <svg :viewBox="`0 0 ${W} ${H}`" class="chart-svg" preserveAspectRatio="none">
          <!-- Grid lines -->
          <line v-for="y in gridYs" :key="y"
            :x1="PAD_L" :y1="y" :x2="W - PAD_R" :y2="y"
            stroke="#E8ECF2" stroke-width="1" />

          <!-- Area fill -->
          <path :d="areaPath" fill="url(#grad)" opacity="0.25" />

          <!-- Line -->
          <path :d="linePath" fill="none" stroke="url(#lineGrad)" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round" />

          <!-- Dots -->
          <circle v-for="(pt, i) in points" :key="i"
            :cx="pt.x" :cy="pt.y" r="4"
            fill="white" stroke="#1565C0" stroke-width="2" />

          <!-- Y labels -->
          <text v-for="(val, i) in yLabels" :key="'y'+i"
            :x="PAD_L - 6" :y="yLabelPos[i]"
            text-anchor="end" dominant-baseline="middle"
            font-size="9" fill="#9AABBB">{{ fmtK(val) }}</text>

          <!-- X labels -->
          <text v-for="(pt, i) in xLabelPts" :key="'x'+i"
            :x="pt.x" :y="H - 4"
            text-anchor="middle" font-size="8" fill="#9AABBB">{{ pt.label }}</text>

          <defs>
            <linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#1565C0" />
              <stop offset="100%" stop-color="#1565C0" stop-opacity="0" />
            </linearGradient>
            <linearGradient id="lineGrad" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#1565C0" />
              <stop offset="100%" stop-color="#00ACC1" />
            </linearGradient>
          </defs>
        </svg>
      </div>
      <div v-else-if="stats.evolution?.length === 1" class="chart-single">
        Solo hay una lista cerrada. Necesitas al menos dos para ver la evolución.
      </div>

      <!-- ── Gasto mensual ── -->
      <div class="section-title" style="margin-top:8px">Gasto por mes</div>

      <!-- Filtro de meses -->
      <div class="month-filters" v-if="stats.monthly?.length">
        <button
          class="mf-btn"
          :class="{ active: selectedMonth === null }"
          @click="selectedMonth = null"
        >Todos</button>
        <button
          v-for="m in stats.monthly"
          :key="m.key"
          class="mf-btn"
          :class="{ active: selectedMonth === m.key }"
          @click="selectedMonth = m.key"
        >{{ m.label }}</button>
      </div>

      <!-- Barras mensuales -->
      <div class="monthly-bars" v-if="filteredMonthly.length">
        <div v-for="m in filteredMonthly" :key="m.key" class="month-block">
          <!-- Header del mes -->
          <div class="month-header" @click="toggleMonth(m.key)">
            <div>
              <div class="month-name">{{ m.label }}</div>
              <div class="month-meta">{{ m.count }} lista{{ m.count !== 1 ? 's' : '' }} · promedio {{ fmt(m.average) }}</div>
            </div>
            <div style="text-align:right;">
              <div class="month-total">{{ fmt(m.total) }}</div>
              <div class="month-chevron" :class="{ open: expandedMonths.includes(m.key) }">▾</div>
            </div>
          </div>

          <!-- Barra del mes vs máximo -->
          <div class="month-bar-track">
            <div class="month-bar-fill" :style="{ width: monthBarW(m) + '%' }" />
            <span class="month-bar-pct">{{ monthBarW(m) }}%</span>
          </div>

          <!-- Listas del mes (expandible) -->
          <Transition name="expand">
            <div v-if="expandedMonths.includes(m.key)" class="month-lists">
              <div v-for="l in m.lists" :key="l.id" class="month-list-row">
                <div class="mlr-left">
                  <div class="mlr-name">{{ l.name }}</div>
                  <div class="mlr-date">{{ fmtDate(l.date) }}</div>
                </div>
                <div class="mlr-right">
                  <div class="mlr-total">{{ fmt(l.total_real) }}</div>
                  <div v-if="l.diff !== null" class="mlr-diff" :class="l.diff > 0 ? 'over' : 'under'">
                    {{ l.diff > 0 ? '+' : '' }}{{ fmt(l.diff) }}
                  </div>
                  <span class="badge-method" v-if="l.method">{{ l.method }}</span>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>

      <div v-else class="chart-single">No hay datos para el mes seleccionado.</div>

      <!-- ── Comparativa ── -->
      <div class="section-title" style="margin-top:8px">Presupuesto vs Real</div>
      <div class="comp-list">
        <div v-for="item in stats.comparisons" :key="item.id" class="comp-card">
          <div class="comp-header">
            <span class="comp-name">{{ item.name }}</span>
            <span class="comp-date">{{ fmtDate(item.date) }}</span>
          </div>

          <!-- Sin presupuesto -->
          <div v-if="!item.budget" class="comp-nobudget">
            <span>Real:</span> <strong>{{ fmt(item.total_real) }}</strong>
            <span class="badge-method" v-if="item.method">{{ item.method }}</span>
          </div>

          <!-- Con presupuesto -->
          <template v-else>
            <div class="comp-row">
              <span class="comp-lbl">Presupuesto</span>
              <span class="comp-val">{{ fmt(item.budget) }}</span>
            </div>
            <div class="comp-bars">
              <div class="bar-track">
                <div class="bar-fill bar-blue" :style="{ width: budgetBarW(item) + '%' }" />
              </div>
              <div class="bar-track">
                <div class="bar-fill"
                  :style="{ width: realBarW(item) + '%', background: item.diff > 0 ? '#E53935' : '#43A047' }" />
              </div>
            </div>
            <div class="comp-row">
              <span class="comp-lbl">Real pagado</span>
              <span class="comp-val">{{ fmt(item.total_real) }}</span>
            </div>
            <div class="comp-diff" :class="item.diff > 0 ? 'over' : 'under'">
              {{ item.diff > 0 ? '⚠ Sobrepasó' : '✓ Ahorró' }}
              {{ fmt(Math.abs(item.diff)) }}
              ({{ Math.abs(Math.round((item.diff / item.budget) * 100)) }}%)
              <span class="badge-method" v-if="item.method">{{ item.method }}</span>
            </div>
          </template>
        </div>
      </div>

      <div style="height:32px" />
    </template>
  </div>
</template>

<script setup>
import { h, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../service/axiosInstance'

const ChevronIcon = () => h('svg', { width:16, height:16, fill:'none', stroke:'currentColor', 'stroke-width':2.5, viewBox:'0 0 24 24' },
  [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', d:'M15 19l-7-7 7-7' })])

const router  = useRouter()
const loading        = ref(true)
const stats          = ref({})
const selectedMonth  = ref(null)
const expandedMonths = ref([])

const filteredMonthly = computed(() => {
  const all = stats.value.monthly ?? []
  if (!selectedMonth.value) return all
  return all.filter(m => m.key === selectedMonth.value)
})

const maxMonthTotal = computed(() =>
  Math.max(...(stats.value.monthly ?? []).map(m => m.total), 1)
)

function monthBarW(m) {
  return Math.round((m.total / maxMonthTotal.value) * 100)
}

function toggleMonth(key) {
  const idx = expandedMonths.value.indexOf(key)
  if (idx === -1) expandedMonths.value.push(key)
  else expandedMonths.value.splice(idx, 1)
}

// ── Chart config ──
const W = 320, H = 140, PAD_L = 42, PAD_R = 12, PAD_T = 14, PAD_B = 20

const points = computed(() => {
  const ev = stats.value.evolution ?? []
  if (ev.length < 2) return []
  const amounts = ev.map(e => e.amount)
  const minV = Math.min(...amounts)
  const maxV = Math.max(...amounts)
  const rangeV = maxV - minV || 1
  const rangeX = W - PAD_L - PAD_R
  const rangeY = H - PAD_T - PAD_B
  return ev.map((e, i) => ({
    x: PAD_L + (i / (ev.length - 1)) * rangeX,
    y: PAD_T + (1 - (e.amount - minV) / rangeV) * rangeY,
    label: shortName(e.label),
    amount: e.amount,
  }))
})

const linePath = computed(() =>
  points.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x},${p.y}`).join(' ')
)

const areaPath = computed(() => {
  if (!points.value.length) return ''
  const first = points.value[0]
  const last  = points.value[points.value.length - 1]
  const bottom = H - PAD_B
  return `${linePath.value} L${last.x},${bottom} L${first.x},${bottom} Z`
})

const gridYs = computed(() => {
  const n = 4
  return Array.from({ length: n }, (_, i) => PAD_T + (i / (n - 1)) * (H - PAD_T - PAD_B))
})

const yLabels = computed(() => {
  const ev = stats.value.evolution ?? []
  if (!ev.length) return []
  const amounts = ev.map(e => e.amount)
  const minV = Math.min(...amounts)
  const maxV = Math.max(...amounts)
  return gridYs.value.map((_, i) => maxV - (i / 3) * (maxV - minV))
})

const yLabelPos = computed(() => gridYs.value)

const xLabelPts = computed(() => {
  const pts = points.value
  if (!pts.length) return []
  if (pts.length <= 5) return pts
  // Mostrar solo algunos si hay muchos
  return pts.filter((_, i) => i === 0 || i === pts.length - 1 || i % Math.ceil(pts.length / 4) === 0)
})

// ── Comparativa bars ──
function barMax(item) {
  return Math.max(item.budget, item.total_real)
}
function budgetBarW(item) {
  return Math.round((item.budget / barMax(item)) * 100)
}
function realBarW(item) {
  return Math.round((item.total_real / barMax(item)) * 100)
}

// ── Helpers ──
function fmt(v)     { return v ? '$' + Number(v).toLocaleString('es-CL') : '-' }
function fmtK(v)    { return v >= 1000 ? '$' + Math.round(v / 1000) + 'k' : '$' + Math.round(v) }
function shortName(n) { return n?.length > 10 ? n.slice(0, 9) + '…' : n }
function fmtDate(ts) {
  if (!ts) return ''
  return new Date(ts * 1000).toLocaleDateString('es-CL', { day: 'numeric', month: 'short' })
}

onMounted(async () => {
  try {
    const { data } = await api.get('/stats')
    stats.value = data
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.screen { min-height: 100vh; background: #F4F6FA; }

.navbar {
  position: sticky; top: 0; z-index: 50;
  background: rgba(255,255,255,.92); backdrop-filter: blur(16px);
  border-bottom: 1px solid #E8ECF2; padding: 12px 18px;
  display: flex; align-items: center; justify-content: space-between;
}
.back-btn {
  width: 32px; height: 32px; border-radius: 10px; border: none;
  background: #F4F6FA; display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.nav-title { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 16px; color: #1A2332; }

.section-title {
  padding: 16px 18px 10px;
  font-size: 11px; font-weight: 700; letter-spacing: .08em;
  text-transform: uppercase; color: #9AABBB;
}

/* ── Cards resumen ── */
.cards { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 0 16px 10px; }
.card {
  border-radius: 18px; padding: 16px 14px;
  display: flex; flex-direction: column; gap: 4px;
}
.card-label { font-size: 11px; font-weight: 600; opacity: .8; }
.card-value { font-family: 'Nunito', sans-serif; font-weight: 900; font-size: 18px; }
.card-sub   { font-size: 10px; opacity: .7; }
.card-blue   { background: linear-gradient(135deg,#1565C0,#00ACC1); color: white; }
.card-green  { background: linear-gradient(135deg,#43A047,#66BB6A); color: white; }
.card-orange { background: #FFF3E0; color: #E65100; }
.card-teal   { background: #E0F7FA; color: #00838F; }

/* ── Chart ── */
.chart-wrap {
  margin: 0 16px 4px;
  background: white; border: 1px solid #E8ECF2; border-radius: 18px; padding: 12px;
}
.chart-svg { width: 100%; height: 140px; display: block; }
.chart-single {
  margin: 0 16px; padding: 16px; background: white; border: 1px solid #E8ECF2;
  border-radius: 18px; font-size: 12px; color: #9AABBB; text-align: center;
}

/* ── Comparativa ── */
.comp-list { display: flex; flex-direction: column; gap: 10px; padding: 0 16px; }
.comp-card {
  background: white; border: 1px solid #E8ECF2; border-radius: 18px; padding: 14px 16px;
}
.comp-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 10px; }
.comp-name   { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 14px; color: #1A2332; }
.comp-date   { font-size: 11px; color: #9AABBB; }
.comp-row    { display: flex; justify-content: space-between; margin-bottom: 4px; }
.comp-lbl    { font-size: 12px; color: #5A6B82; }
.comp-val    { font-family: 'Nunito', sans-serif; font-weight: 700; font-size: 13px; color: #1A2332; }
.comp-bars   { display: flex; flex-direction: column; gap: 4px; margin: 6px 0 8px; }
.bar-track   { height: 8px; background: #F4F6FA; border-radius: 99px; overflow: hidden; }
.bar-fill    { height: 100%; border-radius: 99px; transition: width .5s ease; }
.bar-blue    { background: linear-gradient(90deg,#1565C0,#00ACC1); }
.comp-diff   { font-size: 11px; font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 8px; }
.comp-diff.over  { color: #E53935; }
.comp-diff.under { color: #43A047; }
.comp-nobudget { font-size: 13px; color: #5A6B82; display: flex; align-items: center; gap: 8px; }
.badge-method {
  display: inline-flex; padding: 2px 8px; border-radius: 99px;
  background: #F4F6FA; color: #5A6B82; font-size: 10px; font-weight: 600; text-transform: capitalize;
}

/* ── Filtro meses ── */
.month-filters {
  display: flex; gap: 8px; overflow-x: auto; padding: 0 16px 12px;
  scrollbar-width: none;
}
.month-filters::-webkit-scrollbar { display: none; }
.mf-btn {
  flex-shrink: 0; padding: 6px 14px; border-radius: 99px;
  border: 1.5px solid #E8ECF2; background: white;
  font-family: 'Nunito', sans-serif; font-weight: 700; font-size: 12px;
  color: #5A6B82; cursor: pointer; white-space: nowrap;
  transition: all .15s;
}
.mf-btn.active { background: #1565C0; border-color: #1565C0; color: white; }

/* ── Barras mensuales ── */
.monthly-bars { display: flex; flex-direction: column; gap: 10px; padding: 0 16px; }

.month-block {
  background: white; border: 1px solid #E8ECF2; border-radius: 18px;
  padding: 14px 16px; overflow: hidden;
}

.month-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  cursor: pointer; margin-bottom: 10px;
}
.month-name  { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 15px; color: #1A2332; margin-bottom: 2px; }
.month-meta  { font-size: 11px; color: #9AABBB; }
.month-total { font-family: 'Nunito', sans-serif; font-weight: 900; font-size: 16px; color: #1565C0; }
.month-chevron { font-size: 14px; color: #9AABBB; text-align: right; transition: transform .2s; }
.month-chevron.open { transform: rotate(180deg); }

.month-bar-track {
  height: 10px; background: #F4F6FA; border-radius: 99px;
  overflow: visible; position: relative; margin-bottom: 4px;
}
.month-bar-fill {
  height: 100%; border-radius: 99px;
  background: linear-gradient(90deg, #1565C0, #00ACC1);
  transition: width .6s cubic-bezier(.4,0,.2,1);
}
.month-bar-pct {
  position: absolute; right: 0; top: -16px;
  font-size: 10px; font-weight: 700; color: #9AABBB;
}

/* Listas del mes */
.month-lists { margin-top: 12px; border-top: 1px solid #F4F6FA; padding-top: 10px; display: flex; flex-direction: column; gap: 8px; }
.month-list-row { display: flex; justify-content: space-between; align-items: center; }
.mlr-left  { flex: 1; min-width: 0; }
.mlr-name  { font-size: 13px; font-weight: 600; color: #1A2332; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mlr-date  { font-size: 10px; color: #9AABBB; }
.mlr-right { text-align: right; flex-shrink: 0; margin-left: 12px; display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
.mlr-total { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 14px; color: #1A2332; }
.mlr-diff  { font-size: 10px; font-weight: 700; }
.mlr-diff.over  { color: #E53935; }
.mlr-diff.under { color: #43A047; }

/* Expand transition */
.expand-enter-active, .expand-leave-active { transition: opacity .2s, transform .2s; }
.expand-enter-from, .expand-leave-to { opacity: 0; transform: translateY(-6px); }

/* ── Empty ── */
.empty { text-align: center; padding: 80px 24px; }
.empty-icon  { font-size: 56px; margin-bottom: 16px; }
.empty-title { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 18px; color: #1A2332; margin-bottom: 8px; }
.empty-sub   { font-size: 13px; color: #9AABBB; }
</style>
