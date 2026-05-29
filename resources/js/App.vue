<template>
  <div class="os-shell">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <span class="brand-icon">&#9632;</span>
        <span class="brand-name">VAULT<em>OS</em></span>
      </div>
      <nav class="sidebar-nav">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="nav-item"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          <span class="nav-icon" v-html="tab.icon"></span>
          <span class="nav-label">{{ tab.label }}</span>
        </button>
      </nav>
      <div class="sidebar-footer">
        <span class="sys-status">
          <span class="status-dot"></span>SYS ONLINE
        </span>
        <span class="day-badge">DAY 005 &mdash; 1000 DAYS</span>
      </div>
    </aside>

    <main class="main-area">
      <header class="topbar">
        <div class="topbar-title">{{ currentTab.label }}</div>
        <div class="topbar-right">
          <span class="clock">{{ clock }}</span>
        </div>
      </header>
      <div class="tab-content">
        <DashboardTab v-if="activeTab === 'dashboard'" />
        <UnitGridTab  v-if="activeTab === 'units'" />
        <TenantsTab   v-if="activeTab === 'tenants'" />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import DashboardTab from './components/DashboardTab.vue';
import UnitGridTab  from './components/UnitGridTab.vue';
import TenantsTab   from './components/TenantsTab.vue';

const tabs = [
  { id: 'dashboard', label: 'DASHBOARD',  icon: '&#9641;' },
  { id: 'units',     label: 'UNIT GRID',  icon: '&#9638;' },
  { id: 'tenants',   label: 'TENANTS',    icon: '&#9632;' },
];

const activeTab   = ref('dashboard');
const currentTab  = computed(() => tabs.find(t => t.id === activeTab.value));

const clock = ref('');
let clockTimer;
function tick() {
  clock.value = new Date().toLocaleTimeString('en-NZ', { hour12: false });
}
onMounted(() => { tick(); clockTimer = setInterval(tick, 1000); });
onUnmounted(() => clearInterval(clockTimer));
</script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:         #080808;
  --surface:    #101010;
  --surface-2:  #181818;
  --surface-3:  #202020;
  --border:     #252525;
  --border-hi:  #333;
  --yellow:     #FFD600;
  --yellow-dim: rgba(255, 214, 0, 0.09);
  --yellow-glow:rgba(255, 214, 0, 0.22);
  --blue:       #4A9EFF;
  --blue-dim:   rgba(74, 158, 255, 0.12);
  --red:        #FF3B30;
  --red-dim:    rgba(255, 59, 48, 0.15);
  --orange:     #FF9500;
  --orange-dim: rgba(255, 149, 0, 0.15);
  --green:      #30D158;
  --green-dim:  rgba(48, 209, 88, 0.12);
  --text:       #E0E0E0;
  --text-dim:   #555;
  --text-muted: #2a2a2a;
  --mono:       'IBM Plex Mono', monospace;
  --display:    'Bebas Neue', sans-serif;
}

html, body {
  height: 100%;
  background: var(--bg);
  color: var(--text);
  font-family: var(--mono);
  font-size: 13px;
  -webkit-font-smoothing: antialiased;
}

body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: radial-gradient(circle, rgba(255,214,0,0.04) 1px, transparent 1px);
  background-size: 28px 28px;
  pointer-events: none;
  z-index: 0;
}

#app { height: 100vh; position: relative; z-index: 1; }

.os-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* ── SIDEBAR ─────────────────────────────── */
.sidebar {
  width: 200px;
  flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
}

.sidebar-brand {
  padding: 1.5rem 1.25rem 1rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.brand-icon {
  color: var(--yellow);
  font-size: 1.1rem;
  line-height: 1;
}

.brand-name {
  font-family: var(--display);
  font-size: 1.55rem;
  letter-spacing: 0.06em;
  color: var(--text);
  line-height: 1;
}

.brand-name em {
  font-style: normal;
  color: var(--yellow);
}

.sidebar-nav {
  flex: 1;
  padding: 0.75rem 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.75rem 1.25rem;
  background: transparent;
  border: none;
  color: var(--text-dim);
  font-family: var(--mono);
  font-size: 0.72rem;
  letter-spacing: 0.14em;
  cursor: pointer;
  text-align: left;
  transition: all 0.1s;
  position: relative;
}

.nav-item::before {
  content: '';
  position: absolute;
  left: 0; top: 4px; bottom: 4px;
  width: 2px;
  background: var(--yellow);
  opacity: 0;
  transition: opacity 0.1s;
}

.nav-item:hover { color: var(--text); background: var(--surface-2); }
.nav-item.active { color: var(--yellow); background: var(--yellow-dim); }
.nav-item.active::before { opacity: 1; }

.nav-icon { font-size: 0.9rem; width: 16px; text-align: center; flex-shrink: 0; }
.nav-label { font-weight: 500; }

.sidebar-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.sys-status {
  font-size: 0.6rem;
  letter-spacing: 0.2em;
  color: var(--green);
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.status-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 6px var(--green);
  animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.4; }
}

.day-badge {
  font-size: 0.55rem;
  letter-spacing: 0.12em;
  color: var(--text-muted);
}

/* ── MAIN AREA ───────────────────────────── */
.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.topbar {
  height: 52px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.75rem;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
}

.topbar-title {
  font-family: var(--display);
  font-size: 1.4rem;
  letter-spacing: 0.08em;
  color: var(--yellow);
}

.topbar-right { display: flex; align-items: center; gap: 1.5rem; }

.clock {
  font-size: 0.72rem;
  letter-spacing: 0.14em;
  color: var(--text-dim);
  font-variant-numeric: tabular-nums;
}

.tab-content {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem 1.75rem;
  scrollbar-width: thin;
  scrollbar-color: var(--border) transparent;
}

/* ── SHARED COMPONENTS ───────────────────── */
.section-label {
  font-size: 0.6rem;
  letter-spacing: 0.24em;
  text-transform: uppercase;
  color: var(--text-dim);
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}

.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 2px;
}

.btn {
  font-family: var(--mono);
  font-size: 0.7rem;
  letter-spacing: 0.14em;
  padding: 0.55rem 1rem;
  border: 1px solid var(--border);
  background: var(--surface-2);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 2px;
  transition: all 0.1s;
}

.btn:hover { border-color: var(--yellow); color: var(--text); }
.btn.btn-primary { background: var(--yellow); border-color: var(--yellow); color: #000; font-weight: 700; }
.btn.btn-primary:hover { background: #ffe033; }

.badge {
  display: inline-block;
  font-size: 0.58rem;
  letter-spacing: 0.14em;
  padding: 0.18rem 0.5rem;
  border-radius: 2px;
  text-transform: uppercase;
  font-weight: 700;
}

.badge-available { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(48,209,88,0.3); }
.badge-occupied  { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(255,59,48,0.3); }
.badge-reserved  { background: var(--orange-dim); color: var(--orange); border: 1px solid rgba(255,149,0,0.3); }
.badge-active    { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(48,209,88,0.3); }
.badge-inactive  { background: var(--surface-3);  color: var(--text-dim); border: 1px solid var(--border); }

/* ── MODAL ───────────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.15s ease;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal {
  background: var(--surface);
  border: 1px solid var(--border-hi);
  border-radius: 2px;
  width: 100%;
  max-width: 480px;
  max-height: 85vh;
  overflow-y: auto;
  animation: slideUp 0.18s ease;
}

@keyframes slideUp {
  from { transform: translateY(16px); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}

.modal-header {
  padding: 1.1rem 1.4rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-family: var(--display);
  font-size: 1.3rem;
  letter-spacing: 0.06em;
  color: var(--yellow);
}

.modal-close {
  background: none;
  border: none;
  color: var(--text-dim);
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0.2rem;
  line-height: 1;
}
.modal-close:hover { color: var(--text); }

.modal-body { padding: 1.4rem; }

.form-group { margin-bottom: 1rem; }

.form-label {
  display: block;
  font-size: 0.6rem;
  letter-spacing: 0.18em;
  color: var(--text-dim);
  margin-bottom: 0.35rem;
}

.form-input, .form-select {
  width: 100%;
  background: var(--surface-2);
  border: 1px solid var(--border);
  color: var(--text);
  font-family: var(--mono);
  font-size: 0.82rem;
  padding: 0.6rem 0.75rem;
  border-radius: 2px;
  outline: none;
  transition: border-color 0.1s;
}

.form-input:focus, .form-select:focus { border-color: var(--yellow); }
.form-select option { background: var(--surface-2); }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

.modal-footer {
  padding: 1rem 1.4rem;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

/* ── ANIMATIONS ──────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
