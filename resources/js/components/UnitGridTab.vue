<template>
  <div class="unit-tab" style="animation: fadeUp 0.3s ease both">

    <!-- LEGEND + FILTER -->
    <div class="unit-toolbar">
      <div class="legend">
        <span class="leg-item"><span class="leg-swatch" style="background:var(--green)"></span>AVAILABLE</span>
        <span class="leg-item"><span class="leg-swatch" style="background:var(--red)"></span>OCCUPIED</span>
        <span class="leg-item"><span class="leg-swatch" style="background:var(--orange)"></span>RESERVED</span>
      </div>
      <div class="filter-group">
        <button
          v-for="f in filters"
          :key="f"
          class="filter-btn"
          :class="{ active: activeFilter === f }"
          @click="activeFilter = f"
        >{{ f }}</button>
      </div>
    </div>

    <!-- FLOOR PLAN -->
    <div class="section-label">FACILITY FLOOR PLAN</div>
    <div class="floor-plan" v-if="rows.length">
      <div class="plan-row" v-for="row in rows" :key="row.label">
        <div class="row-label">{{ row.label }}</div>
        <div class="units-strip">
          <div
            v-for="unit in row.units"
            :key="unit.id"
            class="unit-cell"
            :class="['status-' + unit.status, { dimmed: activeFilter !== 'ALL' && unit.status !== activeFilter.toLowerCase(), selected: selectedUnit?.id === unit.id }]"
            :title="unit.unit_number"
            :style="{ width: cellWidth(unit.size_sqft) + 'px' }"
            @click="selectUnit(unit)"
          >
            <span class="cell-number">{{ unit.unit_number }}</span>
            <span class="cell-sqft">{{ unit.size_sqft }}ft²</span>
          </div>
        </div>
      </div>
      <!-- Compass / scale indicator -->
      <div class="plan-footer">
        <span class="plan-scale">&#9632; 1 cell = 25 ft² min</span>
        <span class="plan-total">{{ filteredCount }} / {{ units.length }} UNITS SHOWN</span>
      </div>
    </div>

    <div v-else class="empty-state panel" style="margin-bottom:1.5rem">Loading facility map…</div>

    <!-- UNIT DETAIL DRAWER -->
    <transition name="slide-up">
      <div class="unit-drawer panel" v-if="selectedUnit">
        <div class="drawer-header">
          <div class="drawer-id">
            <span class="drawer-number">{{ selectedUnit.unit_number }}</span>
            <span class="badge" :class="'badge-' + selectedUnit.status">{{ selectedUnit.status }}</span>
          </div>
          <button class="modal-close" @click="selectedUnit = null">&times;</button>
        </div>

        <div class="drawer-body">
          <div class="drawer-specs">
            <div class="spec-item">
              <span class="spec-label">SIZE</span>
              <span class="spec-val">{{ selectedUnit.size_label }}</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">AREA</span>
              <span class="spec-val">{{ selectedUnit.size_sqft }} ft²</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">FLOOR</span>
              <span class="spec-val">{{ selectedUnit.floor }}</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">RATE</span>
              <span class="spec-val" style="color:var(--primary)">${{ fmt(selectedUnit.monthly_rate) }}/mo</span>
            </div>
          </div>

          <!-- Tenant info if occupied -->
          <div v-if="selectedUnit.tenant" class="tenant-block">
            <div class="section-label" style="margin-top:0.75rem">CURRENT TENANT</div>
            <div class="tenant-info-grid">
              <div class="tinfo-item">
                <span class="spec-label">NAME</span>
                <span class="spec-val">{{ selectedUnit.tenant.first_name }} {{ selectedUnit.tenant.last_name }}</span>
              </div>
              <div class="tinfo-item">
                <span class="spec-label">EMAIL</span>
                <span class="spec-val">{{ selectedUnit.tenant.email }}</span>
              </div>
              <div class="tinfo-item">
                <span class="spec-label">PHONE</span>
                <span class="spec-val">{{ selectedUnit.tenant.phone }}</span>
              </div>
              <div class="tinfo-item">
                <span class="spec-label">LEASE END</span>
                <span class="spec-val" :style="{ color: leaseUrgency(selectedUnit.tenant.lease_end) }">
                  {{ selectedUnit.tenant.lease_end || '—' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Status controls -->
          <div class="status-controls">
            <span class="spec-label">CHANGE STATUS</span>
            <div class="status-btns">
              <button
                v-for="s in ['available', 'occupied', 'reserved']"
                :key="s"
                class="status-btn"
                :class="['status-btn-' + s, { active: selectedUnit.status === s }]"
                :disabled="selectedUnit.status === s || saving"
                @click="changeStatus(s)"
              >{{ s }}</button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const units        = ref([]);
const selectedUnit = ref(null);
const activeFilter = ref('ALL');
const saving       = ref(false);
const filters      = ['ALL', 'AVAILABLE', 'OCCUPIED', 'RESERVED'];

const rows = computed(() => {
  const rowMap = {};
  units.value.forEach(u => {
    if (!rowMap[u.row_label]) rowMap[u.row_label] = { label: u.row_label, units: [] };
    rowMap[u.row_label].units.push(u);
  });
  return Object.values(rowMap).sort((a, b) => a.label.localeCompare(b.label));
});

const filteredCount = computed(() => {
  if (activeFilter.value === 'ALL') return units.value.length;
  return units.value.filter(u => u.status === activeFilter.value.toLowerCase()).length;
});

function cellWidth(sqft) {
  if (sqft <= 25)  return 72;
  if (sqft <= 50)  return 100;
  if (sqft <= 100) return 140;
  return 180;
}

function fmt(n) { return Number(n).toLocaleString('en-NZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

function leaseUrgency(dateStr) {
  if (!dateStr) return 'var(--text)';
  const days = Math.floor((new Date(dateStr) - new Date()) / 86400000);
  return days <= 7 ? 'var(--red)' : days <= 14 ? 'var(--orange)' : 'var(--text)';
}

async function selectUnit(unit) {
  if (selectedUnit.value?.id === unit.id) { selectedUnit.value = null; return; }
  const r = await fetch('/api/units/' + unit.id);
  selectedUnit.value = await r.json();
}

async function changeStatus(newStatus) {
  if (!selectedUnit.value) return;
  saving.value = true;
  try {
    const r = await fetch('/api/units/' + selectedUnit.value.id, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ status: newStatus }),
    });
    const updated = await r.json();
    selectedUnit.value = updated;
    const idx = units.value.findIndex(u => u.id === updated.id);
    if (idx !== -1) units.value[idx] = { ...units.value[idx], status: updated.status };
  } finally { saving.value = false; }
}

onMounted(async () => {
  const r = await fetch('/api/units');
  units.value = await r.json();
});
</script>

<style scoped>
.unit-tab { display: flex; flex-direction: column; gap: 1rem; }

.unit-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.legend { display: flex; gap: 1.25rem; }

.leg-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.6rem;
  letter-spacing: 0.14em;
  color: var(--text-dim);
}

.leg-swatch { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }

.filter-group { display: flex; gap: 4px; }

.filter-btn {
  font-family: var(--sans);
  font-size: 0.72rem;
  font-weight: 500;
  padding: 0.35rem 0.75rem;
  background: var(--surface-2);
  border: 1px solid var(--border);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.1s;
}

.filter-btn:hover  { border-color: var(--primary); color: var(--primary); }
.filter-btn.active { background: var(--primary-dim); border-color: var(--primary); color: var(--primary); font-weight: 600; }

/* ── FLOOR PLAN ────────────────────────── */
.floor-plan {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 1.25rem;
  box-shadow: var(--shadow-sm);
}

.plan-row {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 0.85rem;
}

.plan-row:last-of-type { margin-bottom: 0; }

.row-label {
  font-family: var(--sans);
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  color: var(--text-muted);
  width: 24px;
  flex-shrink: 0;
  padding-top: 0.3rem;
}

.units-strip { display: flex; gap: 4px; flex-wrap: wrap; }

.unit-cell {
  height: 64px;
  border: 2px solid transparent;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  cursor: pointer;
  position: relative;
  transition: all 0.12s;
  overflow: hidden;
}

.unit-cell::before {
  content: '';
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 0.12s;
}

.unit-cell:hover::before { opacity: 1; }

.unit-cell.status-available  { background: rgba(48,209,88,0.08);  border-color: rgba(48,209,88,0.35); }
.unit-cell.status-occupied   { background: rgba(255,59,48,0.1);   border-color: rgba(255,59,48,0.35); }
.unit-cell.status-reserved   { background: rgba(255,149,0,0.1);   border-color: rgba(255,149,0,0.35); }

.unit-cell.status-available:hover  { background: rgba(48,209,88,0.18);  border-color: var(--green);  box-shadow: 0 0 12px rgba(48,209,88,0.2); }
.unit-cell.status-occupied:hover   { background: rgba(255,59,48,0.2);   border-color: var(--red);    box-shadow: 0 0 12px rgba(255,59,48,0.2); }
.unit-cell.status-reserved:hover   { background: rgba(255,149,0,0.2);   border-color: var(--orange); box-shadow: 0 0 12px rgba(255,149,0,0.2); }

.unit-cell.selected { box-shadow: 0 0 0 2px var(--primary), 0 0 12px rgba(43,141,200,0.25); border-color: var(--primary) !important; z-index: 2; }

.unit-cell.dimmed { opacity: 0.2; }

.cell-number {
  font-family: var(--sans);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--text);
}

.cell-sqft {
  font-size: 0.55rem;
  letter-spacing: 0.08em;
  color: var(--text-dim);
}

.plan-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--border);
  font-size: 0.58rem;
  letter-spacing: 0.12em;
  color: var(--text-muted);
}

/* ── DETAIL DRAWER ─────────────────────── */
.unit-drawer {
  margin-top: 0.25rem;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--border);
}

.drawer-id { display: flex; align-items: center; gap: 0.75rem; }

.drawer-number {
  font-family: var(--sans);
  font-size: 1.6rem;
  font-weight: 700;
  letter-spacing: -0.01em;
  color: var(--primary);
}

.drawer-body { padding: 1.25rem; }

.drawer-specs, .tenant-info-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.spec-item, .tinfo-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.spec-label {
  font-size: 0.56rem;
  letter-spacing: 0.2em;
  color: var(--text-dim);
}

.spec-val { font-size: 0.8rem; font-weight: 500; }

.tenant-block { border-top: 1px solid var(--border); padding-top: 0.25rem; margin-bottom: 1rem; }

.status-controls { border-top: 1px solid var(--border); padding-top: 1rem; }

.status-btns { display: flex; gap: 0.5rem; margin-top: 0.5rem; }

.status-btn {
  font-family: var(--sans);
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border: 1px solid var(--border);
  background: var(--surface-2);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.1s;
  text-transform: capitalize;
}

.status-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.status-btn-available:not(:disabled):hover  { border-color: var(--green);  color: var(--green); }
.status-btn-occupied:not(:disabled):hover   { border-color: var(--red);    color: var(--red); }
.status-btn-reserved:not(:disabled):hover   { border-color: var(--orange); color: var(--orange); }

.status-btn-available.active { background: var(--green-dim);  border-color: var(--green);  color: var(--green); }
.status-btn-occupied.active  { background: var(--red-dim);    border-color: var(--red);    color: var(--red); }
.status-btn-reserved.active  { background: var(--orange-dim); border-color: var(--orange); color: var(--orange); }

/* transitions */
.slide-up-enter-active { transition: all 0.2s ease; }
.slide-up-leave-active { transition: all 0.15s ease; }
.slide-up-enter-from   { opacity: 0; transform: translateY(12px); }
.slide-up-leave-to     { opacity: 0; transform: translateY(6px); }

.empty-state {
  padding: 3rem;
  text-align: center;
  color: var(--text-dim);
  font-size: 0.72rem;
  letter-spacing: 0.1em;
}
</style>
