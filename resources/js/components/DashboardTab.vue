<template>
  <div class="dashboard" style="animation: fadeUp 0.3s ease both">

    <!-- STAT GRID -->
    <div class="section-label">OPERATIONAL SUMMARY</div>
    <div class="stats-grid">
      <div class="stat-card stat-primary" v-for="s in primaryStats" :key="s.label">
        <div class="stat-label">{{ s.label }}</div>
        <div class="stat-value" :style="{ color: s.color || 'var(--text)' }">{{ s.value }}</div>
        <div class="stat-sub" v-if="s.sub">{{ s.sub }}</div>
      </div>
    </div>

    <div class="dashboard-columns">

      <!-- OCCUPANCY BAR BREAKDOWN -->
      <div>
        <div class="section-label">OCCUPANCY BREAKDOWN</div>
        <div class="panel occ-panel">
          <div class="occ-bar-wrap">
            <div class="occ-segment" :style="{ width: occupiedPct + '%', background: 'var(--red)' }" title="Occupied"></div>
            <div class="occ-segment" :style="{ width: reservedPct + '%', background: 'var(--orange)' }" title="Reserved"></div>
            <div class="occ-segment" :style="{ width: availablePct + '%', background: 'var(--green)' }" title="Available"></div>
          </div>
          <div class="occ-legend">
            <span class="leg-item"><span class="leg-dot" style="background:var(--red)"></span>Occupied ({{ stats.occupied_units }})</span>
            <span class="leg-item"><span class="leg-dot" style="background:var(--orange)"></span>Reserved ({{ stats.reserved_units }})</span>
            <span class="leg-item"><span class="leg-dot" style="background:var(--green)"></span>Available ({{ stats.available_units }})</span>
          </div>
          <div class="revenue-block">
            <div class="rev-item">
              <span class="rev-label">THIS MONTH</span>
              <span class="rev-value" style="color:var(--yellow)">${{ fmt(stats.monthly_revenue) }}</span>
            </div>
            <div class="rev-item">
              <span class="rev-label">THIS YEAR</span>
              <span class="rev-value">${{ fmt(stats.annual_revenue) }}</span>
            </div>
          </div>
        </div>

        <!-- RECENT PAYMENTS -->
        <div class="section-label" style="margin-top:1.5rem">RECENT PAYMENTS</div>
        <div class="panel">
          <div v-if="recentPayments.length === 0" class="empty-state">No payments recorded.</div>
          <div v-for="(p, i) in recentPayments" :key="p.id" class="payment-row" :style="{ animationDelay: (i * 0.05) + 's' }">
            <div class="pay-tenant">{{ p.tenant || '—' }}</div>
            <div class="pay-method">
              <span class="badge" :class="methodClass(p.method)">{{ p.method }}</span>
            </div>
            <div class="pay-amount" style="color:var(--green)">${{ fmt(p.amount) }}</div>
            <div class="pay-date" style="color:var(--text-dim)">{{ p.date }}</div>
          </div>
        </div>
      </div>

      <!-- UPCOMING RENEWALS -->
      <div>
        <div class="section-label">UPCOMING RENEWALS <span style="color:var(--text-dim);font-size:0.6rem">(NEXT 30 DAYS)</span></div>
        <div class="panel">
          <div v-if="renewals.length === 0" class="empty-state">No renewals in the next 30 days.</div>
          <div v-for="(r, i) in renewals" :key="r.id" class="renewal-row" :style="{ animationDelay: (i * 0.06) + 's' }">
            <div class="ren-left">
              <div class="ren-name">{{ r.name }}</div>
              <div class="ren-unit" style="color:var(--text-dim)">UNIT {{ r.unit }}</div>
            </div>
            <div class="ren-right">
              <div class="ren-date" :style="{ color: urgentColor(r.days_left) }">{{ r.lease_end }}</div>
              <div class="ren-days" :style="{ color: urgentColor(r.days_left) }">
                {{ r.days_left <= 0 ? 'EXPIRED' : r.days_left + 'D LEFT' }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const stats          = ref({ total_units: 0, occupied_units: 0, reserved_units: 0, available_units: 0, occupancy_rate: 0, monthly_revenue: 0, annual_revenue: 0, active_tenants: 0 });
const renewals       = ref([]);
const recentPayments = ref([]);
const loading        = ref(true);

const occupiedPct  = computed(() => stats.value.total_units > 0 ? (stats.value.occupied_units  / stats.value.total_units * 100).toFixed(1) : 0);
const reservedPct  = computed(() => stats.value.total_units > 0 ? (stats.value.reserved_units  / stats.value.total_units * 100).toFixed(1) : 0);
const availablePct = computed(() => stats.value.total_units > 0 ? (stats.value.available_units / stats.value.total_units * 100).toFixed(1) : 0);

const primaryStats = computed(() => [
  { label: 'OCCUPANCY RATE',   value: stats.value.occupancy_rate + '%', color: 'var(--yellow)' },
  { label: 'TOTAL UNITS',      value: stats.value.total_units },
  { label: 'ACTIVE TENANTS',   value: stats.value.active_tenants, color: 'var(--blue)' },
  { label: 'AVAILABLE NOW',    value: stats.value.available_units, color: 'var(--green)' },
]);

function fmt(n) { return Number(n).toLocaleString('en-NZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
function urgentColor(days) { return days <= 7 ? 'var(--red)' : days <= 14 ? 'var(--orange)' : 'var(--text)'; }
function methodClass(m) {
  if (m === 'cash') return 'badge-available';
  if (m === 'card') return 'badge-reserved';
  return 'badge-active';
}

onMounted(async () => {
  try {
    const r = await fetch('/api/dashboard');
    const d = await r.json();
    stats.value          = d.stats;
    renewals.value       = d.upcoming_renewals;
    recentPayments.value = d.recent_payments;
  } catch { /* use empty defaults */ }
  finally { loading.value = false; }
});
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 0; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.6rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 2px;
  padding: 1rem 1.1rem;
  animation: fadeUp 0.3s ease both;
}

.stat-label {
  font-size: 0.58rem;
  letter-spacing: 0.2em;
  color: var(--text-dim);
  margin-bottom: 0.4rem;
}

.stat-value {
  font-family: var(--display);
  font-size: 2.4rem;
  line-height: 1;
  letter-spacing: 0.04em;
}

.stat-sub { font-size: 0.6rem; color: var(--text-dim); margin-top: 0.3rem; }

.dashboard-columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  align-items: start;
}

/* occupancy */
.occ-panel { padding: 1.1rem; }

.occ-bar-wrap {
  display: flex;
  height: 8px;
  border-radius: 2px;
  overflow: hidden;
  gap: 1px;
  margin-bottom: 0.85rem;
  background: var(--surface-3);
}

.occ-segment { transition: width 0.6s cubic-bezier(0.4,0,0.2,1); }

.occ-legend {
  display: flex;
  gap: 1.25rem;
  margin-bottom: 1.25rem;
}

.leg-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.62rem;
  letter-spacing: 0.08em;
  color: var(--text-dim);
}

.leg-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

.revenue-block {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.6rem;
  border-top: 1px solid var(--border);
  padding-top: 1rem;
}

.rev-item { display: flex; flex-direction: column; gap: 0.25rem; }

.rev-label { font-size: 0.56rem; letter-spacing: 0.18em; color: var(--text-dim); }
.rev-value { font-family: var(--display); font-size: 1.6rem; letter-spacing: 0.04em; }

/* payment rows */
.payment-row {
  display: grid;
  grid-template-columns: 1fr auto auto auto;
  align-items: center;
  gap: 1rem;
  padding: 0.7rem 1rem;
  border-bottom: 1px solid var(--border);
  font-size: 0.75rem;
  animation: fadeUp 0.25s ease both;
}

.payment-row:last-child { border-bottom: none; }
.pay-tenant { font-weight: 500; }
.pay-amount { font-weight: 700; font-variant-numeric: tabular-nums; }
.pay-date   { font-size: 0.65rem; }

/* renewal rows */
.renewal-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--border);
  animation: fadeUp 0.25s ease both;
}

.renewal-row:last-child { border-bottom: none; }
.ren-name { font-size: 0.8rem; font-weight: 500; }
.ren-unit { font-size: 0.62rem; letter-spacing: 0.1em; margin-top: 0.15rem; }
.ren-date { font-size: 0.72rem; text-align: right; }
.ren-days { font-size: 0.6rem; letter-spacing: 0.14em; text-align: right; margin-top: 0.1rem; }

.empty-state {
  padding: 2rem;
  text-align: center;
  color: var(--text-dim);
  font-size: 0.72rem;
  letter-spacing: 0.1em;
}
</style>
