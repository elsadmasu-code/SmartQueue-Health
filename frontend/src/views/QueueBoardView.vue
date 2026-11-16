<template>
  <div class="space-y-6">
    <!-- Top Display Header -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white p-6 rounded-3xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-2xl text-white shadow-lg">
          SQ
        </div>
        <div>
          <div class="text-xs uppercase font-extrabold tracking-widest text-blue-400">Public Waiting Area Display</div>
          <h1 class="text-2xl md:text-3xl font-black tracking-tight">Smart Queue Calling Screen</h1>
          <p class="text-slate-400 text-xs">Tikur Anbessa Specialized Hospital (ጥቁር አንበሳ ስፔሻላይዝድ ሆስፒታል)</p>
        </div>
      </div>

      <!-- Controls: Department Filter & Sound Toggle -->
      <div class="flex items-center gap-3">
        <select v-model="selectedDept" class="bg-slate-800 text-white text-sm border border-slate-700 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Hospital Departments</option>
          <option value="1">Outpatient (OPD) / የተመላላሽ</option>
          <option value="2">Emergency & Triage / ድንገተኛ</option>
          <option value="4">Main Pharmacy / ፋርማሲ</option>
          <option value="3">Pathology Lab / ላቦራቶሪ</option>
          <option value="7">Cashier & Billing / ካሼር</option>
        </select>

        <button @click="testAudioChime" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
          <span>🔔</span> Test Calling Chime
        </button>
      </div>
    </div>

    <!-- Active Calling Section (Big Television View) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full bg-rose-600 animate-ping"></span>
          <h2 class="text-lg font-black text-slate-900 tracking-wider uppercase">NOW SERVING / አሁን የተጠሩ ታካሚዎች</h2>
        </div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Live Updates Active</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="ticket in calledNowList" :key="ticket.id" 
             class="border-2 border-blue-500 bg-blue-50/50 rounded-2xl p-5 relative overflow-hidden shadow-md animate-pulse">
          <div class="flex justify-between items-start">
            <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-600 text-white tracking-wider">
              {{ ticket.department?.code || 'OPD' }}
            </span>
            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-lg">
              CALLED / ተጠርቷል
            </span>
          </div>

          <div class="mt-4 text-center">
            <div class="text-4xl md:text-5xl font-mono font-black text-slate-900 tracking-tight">
              {{ ticket.ticket_number }}
            </div>
            <div class="text-sm font-bold text-slate-600 mt-1">
              {{ ticket.patient?.amharic_full_name || ticket.patient?.first_name + ' ' + ticket.patient?.father_name }}
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-blue-200/60 flex items-center justify-between text-xs">
            <span class="text-slate-500 font-medium">Please Proceed To:</span>
            <span class="font-black text-blue-900 bg-white px-3 py-1 rounded-lg border border-blue-200 text-sm">
              {{ ticket.counter_or_room || 'OPD Room 104' }}
            </span>
          </div>
        </div>

        <div v-if="calledNowList.length === 0" class="col-span-full py-12 text-center text-slate-400">
          <div class="text-4xl mb-2">📢</div>
          <div class="text-sm font-semibold">No tickets currently called to counters. Next patient will appear here.</div>
        </div>
      </div>
    </div>

    <!-- Waiting Queue Table -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-lg font-black text-slate-900 tracking-tight">Waiting Queue / በሰልፍ ላይ ያሉ</h3>
          <p class="text-xs text-slate-400">Estimated wait times dynamically computed based on queue depth and physician velocity</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
          Total Waiting: {{ waitingList.length }}
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-bold border-b border-slate-100">
            <tr>
              <th class="py-3.5 px-4">Ticket #</th>
              <th class="py-3.5 px-4">Patient Name / የታካሚ ስም</th>
              <th class="py-3.5 px-4">Department</th>
              <th class="py-3.5 px-4">Triage Priority</th>
              <th class="py-3.5 px-4">Est. Wait</th>
              <th class="py-3.5 px-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            <tr v-for="t in waitingList" :key="t.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-base">
                {{ t.ticket_number }}
              </td>
              <td class="py-3.5 px-4 text-slate-800">
                <div>{{ t.patient?.amharic_full_name || t.patient?.first_name + ' ' + t.patient?.father_name }}</div>
                <div class="text-xs text-slate-400">MRN: {{ t.patient?.mrn }}</div>
              </td>
              <td class="py-3.5 px-4 text-slate-600">
                {{ t.department?.name || 'General OPD' }}
              </td>
              <td class="py-3.5 px-4">
                <span :class="getTriageBadge(t.triage_category, t.priority_level)">
                  {{ t.priority_level?.toUpperCase() }} ({{ t.triage_category }})
                </span>
              </td>
              <td class="py-3.5 px-4 font-mono text-slate-700">
                ~{{ t.estimated_wait_minutes }} min
              </td>
              <td class="py-3.5 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  Waiting
                </span>
              </td>
            </tr>

            <tr v-if="waitingList.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">
                Queue is clear. No waiting patients in this department.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import api from '../api/client';

const selectedDept = ref('');
const allTickets = ref([
  {
    id: 1,
    ticket_number: 'OPD-001',
    status: 'called',
    counter_or_room: 'OPD Room 104',
    department: { id: 1, name: 'Outpatient Department (OPD)', code: 'OPD' },
    patient: { first_name: 'Abebe', father_name: 'Kebede', amharic_full_name: 'አበበ ከበደ ወልዴ', mrn: 'SQH-AA-2026-00001' },
    priority_level: 'normal',
    triage_category: 'green',
    estimated_wait_minutes: 0,
  },
  {
    id: 2,
    ticket_number: 'OPD-002',
    status: 'waiting',
    counter_or_room: null,
    department: { id: 1, name: 'Outpatient Department (OPD)', code: 'OPD' },
    patient: { first_name: 'Chaltu', father_name: 'Tolosa', amharic_full_name: 'ጫልቱ ቶሎሳ ገዳ', mrn: 'SQH-AA-2026-00002' },
    priority_level: 'normal',
    triage_category: 'green',
    estimated_wait_minutes: 12,
  },
  {
    id: 3,
    ticket_number: 'EMG-001',
    status: 'waiting',
    counter_or_room: null,
    department: { id: 2, name: 'Emergency & Critical Care', code: 'EMG' },
    patient: { first_name: 'Yohannes', father_name: 'Desta', amharic_full_name: 'ዮሐንስ ደስታ በየነ', mrn: 'SQH-AA-2026-00003' },
    priority_level: 'urgent',
    triage_category: 'yellow',
    estimated_wait_minutes: 4,
  },
]);

const calledNowList = computed(() => {
  return allTickets.value.filter(t => {
    const matchStatus = t.status === 'called';
    const matchDept = !selectedDept.value || String(t.department?.id) === selectedDept.value;
    return matchStatus && matchDept;
  });
});

const waitingList = computed(() => {
  return allTickets.value.filter(t => {
    const matchStatus = t.status === 'waiting';
    const matchDept = !selectedDept.value || String(t.department?.id) === selectedDept.value;
    return matchStatus && matchDept;
  });
});

const getTriageBadge = (triage, priority) => {
  if (priority === 'emergency' || triage === 'red') {
    return 'px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300';
  }
  if (priority === 'urgent' || triage === 'yellow') {
    return 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300';
  }
  return 'px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
};

const testAudioChime = () => {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
    osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.15); // A5
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.6);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + 0.6);
  } catch (e) {
    console.log('Audio chime simulated');
  }
};

const pollQueue = async () => {
  try {
    const res = await api.get('/facilities/1/queue/board');
    if (res.data) {
      const combined = [...(res.data.called_now || []), ...(res.data.waiting || [])];
      if (combined.length > 0) {
        allTickets.value = combined;
      }
    }
  } catch (err) {
    // Keep local seeded queue
  }
};

let pollInterval = null;
onMounted(() => {
  pollQueue();
  pollInterval = setInterval(pollQueue, 5000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});
</script>
