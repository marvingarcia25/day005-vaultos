<template>
  <div class="tenants-tab" style="animation: fadeUp 0.3s ease both">

    <!-- TOOLBAR -->
    <div class="tenants-toolbar">
      <div class="search-wrap">
        <span class="search-icon">&#9906;</span>
        <input
          class="search-input"
          type="text"
          placeholder="Search name or email…"
          v-model="searchQuery"
        />
      </div>
      <div class="toolbar-right">
        <div class="filter-group">
          <button v-for="f in statusFilters" :key="f" class="filter-btn" :class="{ active: statusFilter === f }" @click="statusFilter = f">{{ f }}</button>
        </div>
        <button class="btn btn-primary" @click="showAddModal = true">+ ADD TENANT</button>
      </div>
    </div>

    <!-- TENANT TABLE -->
    <div class="section-label">{{ filteredTenants.length }} TENANT{{ filteredTenants.length !== 1 ? 'S' : '' }}</div>
    <div class="panel tenant-table">
      <div class="table-head">
        <span>TENANT</span>
        <span>UNIT</span>
        <span>LEASE END</span>
        <span>STATUS</span>
        <span>MONTHLY</span>
        <span></span>
      </div>
      <div v-if="filteredTenants.length === 0" class="empty-state">No tenants match your filters.</div>
      <div
        v-for="(t, i) in filteredTenants"
        :key="t.id"
        class="table-row"
        :class="{ active: selectedTenant?.id === t.id }"
        :style="{ animationDelay: (i * 0.04) + 's' }"
        @click="openTenant(t)"
      >
        <div class="cell-tenant">
          <div class="cell-name">{{ t.first_name }} {{ t.last_name }}</div>
          <div class="cell-email">{{ t.email }}</div>
        </div>
        <div class="cell-unit">
          <span v-if="t.unit" class="unit-tag">{{ t.unit.unit_number }}</span>
          <span v-else class="cell-empty">—</span>
        </div>
        <div class="cell-lease" :style="{ color: leaseColor(t.lease_end) }">
          {{ t.lease_end || '—' }}
        </div>
        <div class="cell-status"><span class="badge" :class="'badge-' + t.status">{{ t.status }}</span></div>
        <div class="cell-rate" style="color:var(--primary)">
          {{ t.unit ? '$' + fmt(t.unit.monthly_rate) : '—' }}
        </div>
        <div class="cell-action"><button class="table-btn" @click.stop="openTenant(t)">VIEW</button></div>
      </div>
    </div>

    <!-- TENANT DETAIL MODAL -->
    <div class="modal-overlay" v-if="selectedTenant" @click.self="selectedTenant = null">
      <div class="modal" style="max-width:580px">
        <div class="modal-header">
          <div>
            <div class="modal-title">{{ selectedTenant.first_name }} {{ selectedTenant.last_name }}</div>
            <div style="font-size:0.62rem;color:var(--text-dim);letter-spacing:0.1em;margin-top:0.15rem">{{ selectedTenant.email }}</div>
          </div>
          <button class="modal-close" @click="selectedTenant = null">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Info grid -->
          <div class="detail-grid">
            <div class="spec-item"><span class="spec-label">PHONE</span><span class="spec-val">{{ selectedTenant.phone }}</span></div>
            <div class="spec-item"><span class="spec-label">STATUS</span><span class="spec-val"><span class="badge" :class="'badge-' + selectedTenant.status">{{ selectedTenant.status }}</span></span></div>
            <div class="spec-item"><span class="spec-label">UNIT</span><span class="spec-val" style="color:var(--primary)">{{ selectedTenant.unit?.unit_number || '—' }}</span></div>
            <div class="spec-item"><span class="spec-label">LEASE START</span><span class="spec-val">{{ selectedTenant.lease_start || '—' }}</span></div>
            <div class="spec-item"><span class="spec-label">LEASE END</span><span class="spec-val" :style="{ color: leaseColor(selectedTenant.lease_end) }">{{ selectedTenant.lease_end || '—' }}</span></div>
            <div class="spec-item"><span class="spec-label">EMERGENCY</span><span class="spec-val">{{ selectedTenant.emergency_contact || '—' }}</span></div>
          </div>

          <!-- Payments section -->
          <div class="section-label" style="margin-top:1.25rem">PAYMENT HISTORY</div>
          <div class="payments-list">
            <div v-if="!selectedTenant.payments?.length" class="empty-state-sm">No payments recorded.</div>
            <div v-for="p in selectedTenant.payments" :key="p.id" class="pay-row">
              <div class="pay-period">{{ monthName(p.period_month) }} {{ p.period_year }}</div>
              <div><span class="badge" :class="methodBadge(p.method)">{{ p.method }}</span></div>
              <div class="pay-date-cell" style="color:var(--text-dim)">{{ p.payment_date }}</div>
              <div class="pay-amt" style="color:var(--green)">${{ fmt(p.amount) }}</div>
            </div>
          </div>

          <!-- Log payment -->
          <div class="log-payment-section">
            <div class="section-label" style="margin-top:1.25rem">LOG PAYMENT</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">AMOUNT ($)</label>
                <input class="form-input" type="number" step="0.01" v-model="newPayment.amount" placeholder="0.00" />
              </div>
              <div class="form-group">
                <label class="form-label">DATE</label>
                <input class="form-input" type="date" v-model="newPayment.payment_date" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">PERIOD</label>
                <div style="display:flex;gap:0.5rem">
                  <select class="form-select" v-model="newPayment.period_month" style="flex:1">
                    <option v-for="(m, idx) in months" :key="idx" :value="idx+1">{{ m }}</option>
                  </select>
                  <input class="form-input" type="number" v-model="newPayment.period_year" style="width:80px" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">METHOD</label>
                <select class="form-select" v-model="newPayment.method">
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="bank_transfer">Bank Transfer</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn" @click="selectedTenant = null">CLOSE</button>
          <button class="btn btn-primary" :disabled="savingPayment" @click="logPayment">
            {{ savingPayment ? 'SAVING…' : 'LOG PAYMENT' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ADD TENANT MODAL -->
    <div class="modal-overlay" v-if="showAddModal" @click.self="showAddModal = false">
      <div class="modal">
        <div class="modal-header">
          <span class="modal-title">ADD TENANT</span>
          <button class="modal-close" @click="showAddModal = false">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">FIRST NAME *</label>
              <input class="form-input" v-model="newTenant.first_name" />
            </div>
            <div class="form-group">
              <label class="form-label">LAST NAME *</label>
              <input class="form-input" v-model="newTenant.last_name" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">EMAIL *</label>
            <input class="form-input" type="email" v-model="newTenant.email" />
          </div>
          <div class="form-group">
            <label class="form-label">PHONE *</label>
            <input class="form-input" v-model="newTenant.phone" />
          </div>
          <div class="form-group">
            <label class="form-label">ASSIGN UNIT</label>
            <select class="form-select" v-model="newTenant.unit_id">
              <option value="">— No unit —</option>
              <option v-for="u in availableUnits" :key="u.id" :value="u.id">
                {{ u.unit_number }} ({{ u.size_label }} · ${{ fmt(u.monthly_rate) }}/mo)
              </option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">LEASE START</label>
              <input class="form-input" type="date" v-model="newTenant.lease_start" />
            </div>
            <div class="form-group">
              <label class="form-label">LEASE END</label>
              <input class="form-input" type="date" v-model="newTenant.lease_end" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">EMERGENCY CONTACT</label>
            <input class="form-input" v-model="newTenant.emergency_contact" />
          </div>
          <div v-if="addError" class="error-msg">{{ addError }}</div>
        </div>
        <div class="modal-footer">
          <button class="btn" @click="showAddModal = false">CANCEL</button>
          <button class="btn btn-primary" :disabled="savingTenant" @click="addTenant">
            {{ savingTenant ? 'SAVING…' : 'ADD TENANT' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const tenants      = ref([]);
const allUnits     = ref([]);
const selectedTenant = ref(null);
const showAddModal = ref(false);
const searchQuery  = ref('');
const statusFilter = ref('ALL');
const statusFilters = ['ALL', 'ACTIVE', 'INACTIVE'];
const savingPayment = ref(false);
const savingTenant  = ref(false);
const addError      = ref('');

const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

const now = new Date();
const newPayment = ref({
  amount: '',
  payment_date: now.toISOString().split('T')[0],
  period_month: now.getMonth() + 1,
  period_year: now.getFullYear(),
  method: 'card',
});

const newTenant = ref({ first_name:'', last_name:'', email:'', phone:'', unit_id:'', lease_start:'', lease_end:'', emergency_contact:'' });

const availableUnits = computed(() => allUnits.value.filter(u => u.status === 'available'));

const filteredTenants = computed(() => {
  let list = tenants.value;
  if (statusFilter.value !== 'ALL') list = list.filter(t => t.status === statusFilter.value.toLowerCase());
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(t =>
      (t.first_name + ' ' + t.last_name).toLowerCase().includes(q) ||
      t.email.toLowerCase().includes(q)
    );
  }
  return list;
});

function fmt(n) { return Number(n).toLocaleString('en-NZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
function monthName(m) { return months[m-1] || ''; }

function leaseColor(dateStr) {
  if (!dateStr) return 'var(--text)';
  const days = Math.floor((new Date(dateStr) - new Date()) / 86400000);
  return days <= 0 ? 'var(--red)' : days <= 7 ? 'var(--red)' : days <= 14 ? 'var(--orange)' : 'var(--text)';
}

function methodBadge(m) {
  if (m === 'cash') return 'badge-available';
  if (m === 'card') return 'badge-reserved';
  return 'badge-active';
}

async function openTenant(t) {
  const r = await fetch('/api/tenants/' + t.id);
  selectedTenant.value = await r.json();
  newPayment.value = {
    amount: selectedTenant.value.unit?.monthly_rate || '',
    payment_date: now.toISOString().split('T')[0],
    period_month: now.getMonth() + 1,
    period_year: now.getFullYear(),
    method: 'card',
  };
}

async function logPayment() {
  if (!selectedTenant.value) return;
  savingPayment.value = true;
  try {
    const r = await fetch('/api/tenants/' + selectedTenant.value.id + '/payments', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(newPayment.value),
    });
    const p = await r.json();
    selectedTenant.value.payments = [p, ...(selectedTenant.value.payments || [])];
  } finally { savingPayment.value = false; }
}

async function addTenant() {
  addError.value = '';
  savingTenant.value = true;
  try {
    const body = { ...newTenant.value };
    if (!body.unit_id)        delete body.unit_id;
    if (!body.lease_start)    delete body.lease_start;
    if (!body.lease_end)      delete body.lease_end;
    if (!body.emergency_contact) delete body.emergency_contact;

    const r = await fetch('/api/tenants', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(body),
    });
    if (!r.ok) {
      const err = await r.json();
      addError.value = Object.values(err.errors || {}).flat().join(' ');
      return;
    }
    const t = await r.json();
    tenants.value.unshift(t);
    showAddModal.value = false;
    newTenant.value = { first_name:'', last_name:'', email:'', phone:'', unit_id:'', lease_start:'', lease_end:'', emergency_contact:'' };
  } finally { savingTenant.value = false; }
}

onMounted(async () => {
  const [tr, ur] = await Promise.all([fetch('/api/tenants'), fetch('/api/units')]);
  tenants.value  = await tr.json();
  allUnits.value = await ur.json();
});
</script>

<style scoped>
.tenants-tab { display: flex; flex-direction: column; gap: 1rem; }

.tenants-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 0.25rem;
}

.search-wrap {
  position: relative;
  flex: 1;
  max-width: 320px;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-dim);
  font-size: 0.8rem;
  pointer-events: none;
}

.search-input {
  width: 100%;
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text);
  font-family: var(--sans);
  font-size: 0.875rem;
  padding: 0.55rem 0.75rem 0.55rem 2.25rem;
  border-radius: 6px;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(43,141,200,0.12); }
.search-input::placeholder { color: var(--text-dim); }

.toolbar-right { display: flex; align-items: center; gap: 0.75rem; }

.filter-group { display: flex; gap: 4px; }

.filter-btn {
  font-family: var(--sans);
  font-size: 0.72rem;
  font-weight: 500;
  padding: 0.35rem 0.75rem;
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.1s;
}

.filter-btn:hover  { border-color: var(--primary); color: var(--primary); }
.filter-btn.active { background: var(--primary-dim); border-color: var(--primary); color: var(--primary); font-weight: 600; }

/* table */
.tenant-table { overflow: hidden; }

.table-head {
  display: grid;
  grid-template-columns: 2fr 80px 110px 90px 90px 60px;
  padding: 0.6rem 1rem;
  background: var(--surface-2);
  border-bottom: 1px solid var(--border);
  font-size: 0.56rem;
  letter-spacing: 0.2em;
  color: var(--text-dim);
}

.table-row {
  display: grid;
  grid-template-columns: 2fr 80px 110px 90px 90px 60px;
  align-items: center;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--border);
  cursor: pointer;
  transition: background 0.1s;
  animation: fadeUp 0.2s ease both;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover  { background: var(--surface-2); }
.table-row:hover  { background: var(--surface-2); }
.table-row.active { background: var(--primary-dim); }

.cell-name { font-size: 0.8rem; font-weight: 500; }
.cell-email { font-size: 0.62rem; color: var(--text-dim); margin-top: 0.15rem; }

.unit-tag {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: var(--primary);
}

.cell-empty { color: var(--text-dim); }
.cell-lease { font-size: 0.72rem; }
.cell-rate  { font-size: 0.78rem; font-weight: 700; }

.table-btn {
  font-family: var(--sans);
  font-size: 0.72rem;
  font-weight: 500;
  padding: 0.35rem 0.65rem;
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.1s;
}
.table-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-dim); }

/* detail modal */
.detail-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.85rem;
  margin-bottom: 0.5rem;
}

.spec-item { display: flex; flex-direction: column; gap: 0.25rem; }
.spec-label { font-size: 0.56rem; letter-spacing: 0.2em; color: var(--text-dim); }
.spec-val   { font-size: 0.8rem; font-weight: 500; }

.payments-list {
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: 6px;
  margin-bottom: 0.5rem;
  max-height: 160px;
  overflow-y: auto;
}

.pay-row {
  display: grid;
  grid-template-columns: 1fr auto 110px 80px;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.85rem;
  border-bottom: 1px solid var(--border);
  font-size: 0.72rem;
}

.pay-row:last-child { border-bottom: none; }
.pay-period { font-weight: 500; }
.pay-amt    { font-weight: 700; text-align: right; }
.pay-date-cell { font-size: 0.65rem; }

.empty-state-sm {
  padding: 1.25rem;
  text-align: center;
  color: var(--text-dim);
  font-size: 0.68rem;
  letter-spacing: 0.08em;
}

.error-msg {
  color: var(--red);
  font-size: 0.72rem;
  margin-top: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: var(--red-dim);
  border: 1px solid rgba(229,62,62,0.2);
  border-radius: 6px;
}

.empty-state {
  padding: 3rem;
  text-align: center;
  color: var(--text-dim);
  font-size: 0.72rem;
  letter-spacing: 0.1em;
}

@media (max-width: 768px) {
  .tenants-toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: 0.6rem;
  }

  .search-wrap { max-width: 100%; }

  .toolbar-right {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  /* Hide table header; rows become cards */
  .table-head { display: none; }

  .table-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.3rem 0.75rem;
    padding: 0.85rem 1rem;
  }

  .cell-tenant  { flex: 1 1 100%; }
  .cell-unit    { flex: 0 0 auto; font-size: 0.68rem; }
  .cell-lease   { display: none; }
  .cell-status  { flex: 0 0 auto; margin-left: auto; }
  .cell-rate    { flex: 0 0 auto; font-size: 0.72rem; font-weight: 700; color: var(--primary); }
  .cell-action  { display: none; }

  /* Payment log rows in modal */
  .pay-row {
    grid-template-columns: 1fr auto;
    grid-template-rows: auto auto;
    gap: 0.15rem 0.5rem;
    padding: 0.65rem 0.75rem;
  }

  .pay-period     { grid-column: 1; grid-row: 1; }
  .pay-row > div:nth-child(2) { grid-column: 2; grid-row: 1; }
  .pay-date-cell  { grid-column: 1; grid-row: 2; }
  .pay-amt        { grid-column: 2; grid-row: 2; }

  .detail-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
