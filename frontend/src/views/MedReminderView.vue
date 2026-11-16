<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> MEDREMINDER & PATIENT ADHERENCE ENGINE
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">MedReminder Medication Adherence</h1>
        <p class="text-sm text-slate-500">Automated daily medication timetables, SMS alerts & compliance rate tracking</p>
      </div>

      <!-- SMS Alert Trigger Simulation Button -->
      <button @click="dispatchTestSms" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-2">
        <span>📲</span> Send Ethio Telecom SMS Alert
      </button>
    </div>

    <!-- Adherence Metric & Schedule Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Adherence Rate Scorecard -->
      <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-2xl p-6 shadow-md space-y-4">
        <div class="text-xs font-bold uppercase tracking-wider text-blue-400">Monthly Adherence Score</div>
        
        <div class="flex items-baseline gap-2">
          <span class="text-5xl font-black font-mono">94.2%</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold">Excellent</span>
        </div>

        <p class="text-xs text-slate-300 leading-relaxed">
          Abebe Kebede has taken 32 of 34 scheduled doses on time this month. Consistent adherence significantly lowers diabetes & hypertension risks.
        </p>

        <div class="pt-2 border-t border-slate-800 grid grid-cols-2 gap-2 text-xs font-mono">
          <div>Doses Taken: <strong class="text-emerald-400">32</strong></div>
          <div>Doses Missed: <strong class="text-rose-400">2</strong></div>
        </div>

        <div class="bg-blue-900/40 border border-blue-700/50 rounded-xl p-3 text-xs text-blue-200">
          <div class="font-bold">Next Dose Alert in:</div>
          <div class="text-base font-bold text-white mt-0.5">2 hours 45 mins (20:00)</div>
        </div>
      </div>

      <!-- Daily Medication Schedule List -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-base font-black text-slate-900 flex items-center justify-between">
          <span>Today's Prescribed Medication Schedule</span>
          <span class="text-xs text-slate-400 font-mono">{{ todayDate }}</span>
        </h2>

        <div class="space-y-3">
          <div 
            v-for="med in schedules" 
            :key="med.id" 
            class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white transition flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-mono font-bold text-sm shadow-xs">
                {{ med.time }}
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">{{ med.medicine_name }}</h3>
                <div class="text-xs text-slate-600">Dose: <strong>{{ med.dosage }}</strong> • {{ med.frequency }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Instructions: {{ med.instructions }}</div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
              <button 
                v-if="!med.taken"
                @click="takeDose(med)" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-sm transition flex items-center gap-1.5"
              >
                <span>✓</span> Take Dose
              </button>
              <span v-else class="px-3 py-1.5 bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-1">
                <span>✓</span> Taken at {{ med.takenAt }}
              </span>

              <button 
                v-if="!med.taken"
                @click="skipDose(med)"
                class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition"
              >
                Skip
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const todayDate = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

const schedules = ref([
  {
    id: 1,
    time: '08:00',
    medicine_name: 'Metformin HCl 500mg',
    dosage: '1 Tablet (500mg)',
    frequency: 'Twice daily with meals',
    instructions: 'Take morning with breakfast',
    taken: true,
    takenAt: '08:12 AM',
  },
  {
    id: 2,
    time: '08:00',
    medicine_name: 'Amlodipine Besylate 5mg',
    dosage: '1 Tablet (5mg)',
    frequency: 'Once daily morning',
    instructions: 'Take every morning with glass of water',
    taken: true,
    takenAt: '08:12 AM',
  },
  {
    id: 3,
    time: '20:00',
    medicine_name: 'Metformin HCl 500mg',
    dosage: '1 Tablet (500mg)',
    frequency: 'Twice daily with meals',
    instructions: 'Take evening with dinner',
    taken: false,
    takenAt: null,
  },
]);

const takeDose = (med) => {
  med.taken = true;
  const now = new Date();
  med.takenAt = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  alert(`Dose logged for ${med.medicine_name}!\nAdherence recorded and synced.`);
};

const skipDose = (med) => {
  alert(`Dose for ${med.medicine_name} marked as skipped.`);
};

const dispatchTestSms = () => {
  alert('Ethio Telecom Gateway Alert Dispatched:\nTo: +251911223344\n"የመድኃኒት ማስታወሻ: የ Metformin HCl 500mg የሚወስዱበት ሰዓት ደርሷል:: መመሪያ: ከእራት ጋር ይውሰዱ::"');
};
</script>
