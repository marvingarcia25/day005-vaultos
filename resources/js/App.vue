<template>
  <div class="os-shell">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <img src="https://storman.com/wp-content/uploads/2023/03/storman-logo.svg" alt="VaultOS" class="brand-logo" onerror="this.style.display='none'" />
        <span class="brand-name">Vault<em>OS</em></span>
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
          <span class="status-dot"></span>System Online
        </span>
        <span class="day-badge">Day 005 · 1000 Days</span>
      </div>
    </aside>

    <main class="main-area">
      <header class="topbar">
        <div class="topbar-left">
          <div class="topbar-title">{{ currentTab.label }}</div>
        </div>
        <div class="topbar-right">
          <span class="clock">{{ clock }}</span>
          <div class="topbar-avatar">MG</div>
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
  { id: 'dashboard', label: 'Dashboard',  icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>' },
  { id: 'units',     label: 'Unit Grid',  icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="9" height="9"/><rect x="13" y="2" width="9" height="9"/><rect x="2" y="13" width="9" height="9"/><rect x="13" y="13" width="9" height="9"/></svg>' },
  { id: 'tenants',   label: 'Tenants',    icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>' },
];

const activeTab  = ref('dashboard');
const currentTab = computed(() => tabs.find(t => t.id === activeTab.value));

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
  --bg:           #F4F6F9;
  --surface:      #FFFFFF;
  --surface-2:    #F8F9FB;
  --surface-3:    #EEF1F5;
  --border:       #E2E6EA;
  --border-hi:    #C8D2DC;
  --primary:      #2B8DC8;
  --primary-dim:  rgba(43, 141, 200, 0.10);
  --primary-dark: #1E6FA0;
  --blue:         #2B8DC8;
  --blue-dim:     rgba(43, 141, 200, 0.10);
  --red:          #E53E3E;
  --red-dim:      rgba(229, 62, 62, 0.10);
  --orange:       #DD6B20;
  --orange-dim:   rgba(221, 107, 32, 0.10);
  --green:        #38A169;
  --green-dim:    rgba(56, 161, 105, 0.10);
  --text:         #2D3748;
  --text-dim:     #718096;
  --text-muted:   #CBD5E0;
  --sans:         'Plus Jakarta Sans', sans-serif;
  --shadow-sm:    0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
  --shadow-md:    0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
}

html, body {
  height: 100%;
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 14px;
  -webkit-font-smoothing: antialiased;
}

#app { height: 100vh; }

.os-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* ── SIDEBAR ─────────────────────────────── */
.sidebar {
  width: 220px;
  flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-sm);
  z-index: 10;
}

.sidebar-brand {
  padding: 1.25rem 1.25rem 1rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.brand-logo { height: 22px; }

.brand-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  letter-spacing: -0.01em;
}

.brand-name em {
  font-style: normal;
  color: var(--primary);
}

.sidebar-nav {
  flex: 1;
  padding: 0.6rem 0.6rem;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.6rem 0.75rem;
  background: transparent;
  border: none;
  color: var(--text-dim);
  font-family: var(--sans);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
  border-radius: 6px;
  transition: all 0.15s;
  position: relative;
}

.nav-item:hover { color: var(--text); background: var(--surface-2); }
.nav-item.active { color: var(--primary); background: var(--primary-dim); font-weight: 600; }

.nav-icon { display: flex; align-items: center; flex-shrink: 0; }
.nav-label {}

.sidebar-footer {
  padding: 0.9rem 1.25rem;
  border-top: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.sys-status {
  font-size: 0.72rem;
  color: var(--green);
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-weight: 500;
}

.status-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 0 2px rgba(56,161,105,0.2);
  animation: pulse-dot 2.5s infinite;
}

@keyframes pulse-dot {
  0%, 100% { box-shadow: 0 0 0 2px rgba(56,161,105,0.2); }
  50%       { box-shadow: 0 0 0 4px rgba(56,161,105,0.1); }
}

.day-badge {
  font-size: 0.68rem;
  color: var(--text-muted);
}

/* ── MAIN AREA ───────────────────────────── */
.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--bg);
}

.topbar {
  height: 56px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.75rem;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  z-index: 5;
}

.topbar-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text);
}

.topbar-right { display: flex; align-items: center; gap: 1rem; }

.clock {
  font-size: 0.8rem;
  color: var(--text-dim);
  font-variant-numeric: tabular-nums;
}

.topbar-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--primary);
  color: #fff;
  font-size: 0.68rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  letter-spacing: 0.03em;
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
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-dim);
  margin-bottom: 0.65rem;
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
  border-radius: 8px;
  box-shadow: var(--shadow-sm);
}

.btn {
  font-family: var(--sans);
  font-size: 0.8rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-dim);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.15s;
}

.btn:hover { border-color: var(--primary); color: var(--primary); }
.btn.btn-primary { background: var(--primary); border-color: var(--primary); color: #fff; font-weight: 600; }
.btn.btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

.badge {
  display: inline-block;
  font-size: 0.68rem;
  font-weight: 600;
  padding: 0.2rem 0.55rem;
  border-radius: 20px;
  text-transform: capitalize;
}

.badge-available { background: rgba(56,161,105,0.12); color: #276749; }
.badge-occupied  { background: rgba(229,62,62,0.10);  color: #C53030; }
.badge-reserved  { background: rgba(221,107,32,0.12); color: #9C4221; }
.badge-active    { background: rgba(56,161,105,0.12); color: #276749; }
.badge-inactive  { background: var(--surface-3);      color: var(--text-dim); }

/* ── MODAL ───────────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.15s ease;
  backdrop-filter: blur(2px);
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  width: 100%;
  max-width: 480px;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: var(--shadow-md);
  animation: slideUp 0.2s ease;
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
  font-size: 1rem;
  font-weight: 600;
  color: var(--text);
}

.modal-close {
  background: none;
  border: none;
  color: var(--text-dim);
  font-size: 1.3rem;
  cursor: pointer;
  padding: 0.15rem 0.4rem;
  line-height: 1;
  border-radius: 4px;
  transition: all 0.1s;
}
.modal-close:hover { background: var(--surface-2); color: var(--text); }

.modal-body { padding: 1.25rem 1.4rem; }

.form-group { margin-bottom: 0.85rem; }

.form-label {
  display: block;
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--text-dim);
  margin-bottom: 0.3rem;
  letter-spacing: 0.03em;
}

.form-input, .form-select {
  width: 100%;
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text);
  font-family: var(--sans);
  font-size: 0.875rem;
  padding: 0.55rem 0.75rem;
  border-radius: 6px;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.form-input:focus, .form-select:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(43,141,200,0.12);
}

.form-select option { background: var(--surface); }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

.modal-footer {
  padding: 0.9rem 1.4rem;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  background: var(--surface-2);
  border-radius: 0 0 12px 12px;
}

/* ── ANIMATIONS ──────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
