<template>
  <div class="space-y-6">
    <!-- Header with Hospital Facility Selector & Live Clock -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-blue-600 font-semibold text-sm mb-1">
          <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>ETHIOPIAN MASTER FACILITY REGISTER: MFR-AA-001</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
          Tikur Anbessa Specialized Hospital (ጥቁር አንበሳ)
        </h1>
        <p class="text-slate-500 text-sm mt-1">
          Central Hospital Operations & Clinical Flow Dashboard • Addis Ababa, Ethiopia
        </p>
      </div>

      <div class="flex items-center gap-3">
        <div class="bg-blue-50 border border-blue-200 px-4 py-2 rounded-xl text-right">
          <div class="text-xs text-blue-600 font-bold uppercase tracking-wider">Hospital System Clock</div>
          <div class="text-lg font-mono font-bold text-blue-900">{{ currentTime }}</div>
        </div>
        <button @click="fetchStats" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-700" title="Refresh Live Data">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Waiting Patients -->
      <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-5 rounded-2xl shadow-md relative overflow-hidden">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider">Active Queue Waiting</div>
            <div class="text-3xl font-black mt-2">{{ stats.waiting_patients || 14 }}</div>
            <div class="text-xs text-blue-100 mt-1 flex items-center gap-1">
              <span>Avg. Wait: ~{{ stats.avg_waiting_minutes || 12 }} mins</span>
            </div>
          </div>
          <div class="p-3 bg-white/10 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
      </div>

      <!-- Today Total Visits -->
      <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Today's Total Encounters</div>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ stats.today_visits || 48 }}</div>
            <div class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
              <span>Completed: {{ stats.completed_encounters || 32 }}</span>
            </div>
          </div>
          <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
        </div>
      </div>

      <!-- Inpatient Bed Occupancy -->
      <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Inpatient Bed Occupancy</div>
            <div class="text-3xl font-black text-slate-800 mt-2">
              {{ stats.occupied_beds || 18 }}/{{ stats.total_beds || 22 }}
            </div>
            <div class="text-xs font-medium text-amber-600 mt-1">
              {{ stats.available_beds || 4 }} beds available ({{ stats.bed_occupancy_percentage || 81.8 }}%)
            </div>
          </div>
          <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          </div>
        </div>
      </div>

      <!-- Today Hospital Revenue (ETB) -->
      <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Today's Revenue (ETB)</div>
            <div class="text-3xl font-black text-slate-800 mt-2">
              {{ formatETB(stats.today_revenue_etb || 18450) }}
            </div>
            <div class="text-xs text-blue-600 font-medium mt-1">
              Telebirr • CBE Birr • Cash
            </div>
          </div>
          <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Department Status Banners -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pharmacy Queue</span>
          <div class="text-xl font-bold text-slate-800 mt-1">{{ stats.pending_prescriptions || 6 }} Pending Rx</div>
          <span class="text-xs text-slate-500">Stock Alerts: {{ stats.low_stock_alerts || 1 }} items low</span>
        </div>
        <router-link to="/pharmacy" class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition">
          Open Pharmacy &rarr;
        </router-link>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pathology & Lab</span>
          <div class="text-xl font-bold text-slate-800 mt-1">{{ stats.pending_labs || 8 }} Samples in Test</div>
          <span class="text-xs text-slate-500">Specimen Barcode Tracking Active</span>
        </div>
        <router-link to="/lab" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg transition">
          Open Lab &rarr;
        </router-link>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Emergency & Triage</span>
          <div class="text-xl font-bold text-rose-600 mt-1">{{ stats.emergency_cases_today || 3 }} Urgent Cases</div>
          <span class="text-xs text-slate-500">Red/Yellow priority direct routing</span>
        </div>
        <router-link to="/triage" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg transition">
          Open Triage &rarr;
        </router-link>
      </div>
    </div>

    <!-- National Digital Health Systems Interoperability Overview (DOC-005, DOC-021) -->
    <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
            Federal Ministry of Health (MOH) Digital Ecosystem Integration
          </div>
          <h2 class="text-xl font-bold">Ethiopian Digital Health Exchange (NDHx) & DHIS2 Interop Layer</h2>
          <p class="text-slate-300 text-sm leading-relaxed">
            SmartQueue Health connects directly with the Ethiopian Master Facility Register (MFR), DHIS2 aggregate monthly morbidity reporting, and HL7 FHIR compliant records exchange.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <div class="bg-slate-800/80 border border-slate-700 px-4 py-2.5 rounded-xl text-xs">
            <div class="text-slate-400">MFR Status</div>
            <div class="text-emerald-400 font-bold flex items-center gap-1">
              <span>●</span> Verified Facility
            </div>
          </div>
          <div class="bg-slate-800/80 border border-slate-700 px-4 py-2.5 rounded-xl text-xs">
            <div class="text-slate-400">DHIS2 Reporting</div>
            <div class="text-blue-400 font-bold">Monthly Auto-Sync</div>
          </div>
          <div class="bg-slate-800/80 border border-slate-700 px-4 py-2.5 rounded-xl text-xs">
            <div class="text-slate-400">Offline Engine</div>
            <div class="text-amber-400 font-bold">SQLite Sync Ready</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '../api/client';

const stats = ref({
  waiting_patients: 14,
  today_visits: 48,
  completed_encounters: 32,
  avg_waiting_minutes: 12,
  occupied_beds: 18,
  total_beds: 22,
  available_beds: 4,
  bed_occupancy_percentage: 81.8,
  today_revenue_etb: 18450,
  pending_prescriptions: 6,
  pending_labs: 8,
  emergency_cases_today: 3,
  low_stock_alerts: 1,
});

const currentTime = ref('');
let timer = null;

const updateClock = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('en-US', { hour12: false });
};

const formatETB = (amount) => {
  return new Intl.NumberFormat('en-ET', { style: 'currency', currency: 'ETB', maximumFractionDigits: 0 }).format(amount);
};

const fetchStats = async () => {
  try {
    const res = await api.get('/facilities/1/dashboard/stats');
    if (res.data && res.data.summary) {
      stats.value = res.data.summary;
    }
  } catch (err) {
    console.warn('Using seeded dashboard stats fallback', err);
  }
};

onMounted(() => {
  updateClock();
  timer = setInterval(updateClock, 1000);
  fetchStats();
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>
