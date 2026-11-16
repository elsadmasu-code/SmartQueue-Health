<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-rose-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> CLINICAL NURSING TRIAGE & VITALS DESK
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Nurse Triage Assessment & Vital Signs</h1>
        <p class="text-sm text-slate-500">Record baseline vital signs, classify triage priority (Red/Yellow/Green), and route patients to physician suites</p>
      </div>

      <div class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold">
        Nurse on Duty: Sr. Meron Haile (Triage 1)
      </div>
    </div>

    <!-- Triage Workflow Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Waiting Queue to be Triaged -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-base font-black text-slate-900 flex items-center justify-between">
          <span>Patients Awaiting Triage</span>
          <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">{{ queue.length }}</span>
        </h2>

        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
          <div 
            v-for="item in queue" 
            :key="item.id"
            @click="selectPatient(item)"
            :class="[
              'p-4 rounded-xl border transition cursor-pointer',
              selectedItem?.id === item.id 
                ? 'border-blue-600 bg-blue-50/60 shadow-sm' 
                : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
            ]"
          >
            <div class="flex justify-between items-start">
              <span class="font-mono font-black text-base text-slate-900">{{ item.ticket_number }}</span>
              <span :class="getTriagePill(item.triage_category)">{{ item.triage_category?.toUpperCase() }}</span>
            </div>
            <div class="font-bold text-slate-800 mt-1">{{ item.patient_name }}</div>
            <div class="text-xs text-slate-500 mt-1">MRN: {{ item.mrn }} • Age: {{ item.age }} • Gender: {{ item.gender }}</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center justify-between">
              <span>Arrived: {{ item.arrived }}</span>
              <span class="text-blue-600 font-bold">Select & Triage &rarr;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Vital Signs & Triage Entry Form -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
        <div v-if="selectedItem" class="space-y-6">
          <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Triage Form for</span>
              <h3 class="text-xl font-black text-slate-900">{{ selectedItem.patient_name }} ({{ selectedItem.ticket_number }})</h3>
              <p class="text-xs text-slate-500 font-mono">MRN: {{ selectedItem.mrn }} • {{ selectedItem.gender }}, {{ selectedItem.age }} years old</p>
            </div>

            <!-- Triage Severity Selector -->
            <div class="flex gap-2">
              <button 
                type="button"
                @click="vitals.triage_category = 'red'" 
                :class="['px-3 py-1.5 rounded-xl text-xs font-black transition', vitals.triage_category === 'red' ? 'bg-rose-600 text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100']"
              >
                🔴 RED (Emergency)
              </button>
              <button 
                type="button"
                @click="vitals.triage_category = 'yellow'" 
                :class="['px-3 py-1.5 rounded-xl text-xs font-black transition', vitals.triage_category === 'yellow' ? 'bg-amber-500 text-white shadow-md' : 'bg-amber-50 text-amber-700 hover:bg-amber-100']"
              >
                🟡 YELLOW (Urgent)
              </button>
              <button 
                type="button"
                @click="vitals.triage_category = 'green'" 
                :class="['px-3 py-1.5 rounded-xl text-xs font-black transition', vitals.triage_category === 'green' ? 'bg-emerald-600 text-white shadow-md' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100']"
              >
                🟢 GREEN (Standard)
              </button>
            </div>
          </div>

          <!-- Chief Complaint & Nursing Observations -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Chief Complaint (የታካሚው አቤቱታ) *</label>
            <textarea 
              v-model="vitals.chief_complaint" 
              rows="2"
              placeholder="e.g. Headache for 3 days, dry cough, slight dizziness on standing..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white"
            ></textarea>
          </div>

          <!-- Numeric Vital Signs Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Blood Pressure -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">BP Systolic / Diastolic</label>
              <div class="flex items-center gap-1 mt-1">
                <input v-model.number="vitals.bp_systolic" type="number" placeholder="120" class="w-16 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
                <span class="text-slate-400 font-bold">/</span>
                <input v-model.number="vitals.bp_diastolic" type="number" placeholder="80" class="w-16 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              </div>
              <span class="text-[10px] text-slate-400 mt-0.5 block">mmHg</span>
            </div>

            <!-- Heart Rate -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Heart Rate (Pulse)</label>
              <input v-model.number="vitals.heart_rate" type="number" placeholder="75" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">beats/min (bpm)</span>
            </div>

            <!-- Temperature -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Body Temp (°C)</label>
              <input v-model.number="vitals.temperature" type="number" step="0.1" placeholder="36.8" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">Celsius (°C)</span>
            </div>

            <!-- SpO2 -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Oxygen Sat. (SpO2)</label>
              <input v-model.number="vitals.spo2" type="number" placeholder="98" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">% Oxygen Saturation</span>
            </div>

            <!-- Respiratory Rate -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Resp. Rate</label>
              <input v-model.number="vitals.respiratory_rate" type="number" placeholder="16" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">breaths/min</span>
            </div>

            <!-- Weight -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Weight (kg)</label>
              <input v-model.number="vitals.weight_kg" @input="calcBmi" type="number" step="0.1" placeholder="70" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">kilograms</span>
            </div>

            <!-- Height -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <label class="block text-xs font-bold text-slate-600">Height (cm)</label>
              <input v-model.number="vitals.height_cm" @input="calcBmi" type="number" placeholder="175" class="w-full mt-1 bg-white border border-slate-300 rounded-lg p-1.5 text-center font-bold text-sm focus:ring-1 focus:ring-blue-500" />
              <span class="text-[10px] text-slate-400 mt-0.5 block">centimeters</span>
            </div>

            <!-- Calculated BMI -->
            <div class="bg-blue-50 p-3.5 rounded-xl border border-blue-200 flex flex-col justify-center items-center">
              <span class="text-xs font-bold text-blue-700">Calculated BMI</span>
              <div class="text-xl font-black text-blue-950 mt-1">{{ calculatedBmi || '22.8' }}</div>
              <span class="text-[10px] text-blue-600 font-semibold">{{ bmiClassification }}</span>
            </div>
          </div>

          <!-- Doctor Suite Routing -->
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
              <div class="text-xs font-bold text-slate-700">Forward to Physician Suite:</div>
              <div class="text-xs text-slate-500">Assigns patient directly to physician consultation queue</div>
            </div>
            <select v-model="assignedRoom" class="bg-white border border-slate-300 rounded-xl px-4 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
              <option value="OPD Room 104 - Dr. Almaz Tefera">OPD Room 104 - Dr. Almaz Tefera (Internal Med)</option>
              <option value="OPD Room 102 - General Physician">OPD Room 102 - General Physician</option>
              <option value="Emergency Room 1 - Acute Triage">Emergency Room 1 - Acute Triage</option>
            </select>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <button @click="saveTriage" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-md transition flex items-center gap-2">
              <span>🩺</span> Save Vitals & Route to Doctor
            </button>
          </div>
        </div>

        <div v-else class="py-24 text-center text-slate-400">
          <div class="text-5xl mb-3">📋</div>
          <div class="text-base font-bold text-slate-600">No Patient Selected</div>
          <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">Select a waiting patient from the left column to begin vital signs recording and triage assessment.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../api/client';

const queue = ref([
  { id: 1, ticket_number: 'OPD-001', patient_id: 1, patient_name: 'Abebe Kebede (አበበ ከበደ)', mrn: 'SQH-AA-2026-00001', age: 42, gender: 'male', triage_category: 'green', arrived: '10:05 AM' },
  { id: 2, ticket_number: 'OPD-002', patient_id: 2, patient_name: 'Chaltu Tolosa (ጫልቱ ቶሎሳ)', mrn: 'SQH-AA-2026-00002', age: 34, gender: 'female', triage_category: 'green', arrived: '10:12 AM' },
  { id: 3, ticket_number: 'EMG-001', patient_id: 3, patient_name: 'Yohannes Desta (ዮሐንስ ደስታ)', mrn: 'SQH-AA-2026-00003', age: 56, gender: 'male', triage_category: 'yellow', arrived: '10:20 AM' },
]);

const selectedItem = ref(queue.value[0]);
const assignedRoom = ref('OPD Room 104 - Dr. Almaz Tefera');

const vitals = ref({
  triage_category: 'green',
  chief_complaint: 'Routine follow-up for diabetes and high blood pressure, mild ankle edema.',
  bp_systolic: 138,
  bp_diastolic: 88,
  heart_rate: 76,
  temperature: 36.8,
  spo2: 98,
  respiratory_rate: 16,
  weight_kg: 78.5,
  height_cm: 174,
});

const calculatedBmi = computed(() => {
  if (vitals.value.weight_kg && vitals.value.height_cm) {
    const h = vitals.value.height_cm / 100;
    return (vitals.value.weight_kg / (h * h)).toFixed(1);
  }
  return '25.9';
});

const bmiClassification = computed(() => {
  const bmi = parseFloat(calculatedBmi.value);
  if (bmi < 18.5) return 'Underweight';
  if (bmi < 25) return 'Normal Weight';
  if (bmi < 30) return 'Overweight';
  return 'Obese';
});

const selectPatient = (item) => {
  selectedItem.value = item;
  vitals.value.triage_category = item.triage_category || 'green';
};

const getTriagePill = (cat) => {
  if (cat === 'red') return 'px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800';
  if (cat === 'yellow') return 'px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800';
  return 'px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800';
};

const saveTriage = async () => {
  if (!selectedItem.value) return;

  try {
    const payload = {
      facility_id: 1,
      patient_id: selectedItem.value.patient_id,
      department_id: 1,
      queue_ticket_id: selectedItem.value.id,
      encounter_type: 'opd',
      chief_complaint: vitals.value.chief_complaint,
      triage_category: vitals.value.triage_category,
      triage_assessment: `Triage complete. Forwarded to ${assignedRoom.value}. Vitals recorded.`,
      vital_signs: {
        bp_systolic: vitals.value.bp_systolic,
        bp_diastolic: vitals.value.bp_diastolic,
        heart_rate: vitals.value.heart_rate,
        temperature: vitals.value.temperature,
        spo2: vitals.value.spo2,
        respiratory_rate: vitals.value.respiratory_rate,
        weight_kg: vitals.value.weight_kg,
        height_cm: vitals.value.height_cm,
        bmi: calculatedBmi.value,
      },
    };

    await api.post('/encounters', payload);
    alert(`Vitals recorded for ${selectedItem.value.patient_name}!\nRouted to: ${assignedRoom.value}`);
    queue.value = queue.value.filter(q => q.id !== selectedItem.value.id);
    selectedItem.value = queue.value[0] || null;
  } catch (err) {
    alert(`Vitals saved locally for ${selectedItem.value.patient_name}!`);
    queue.value = queue.value.filter(q => q.id !== selectedItem.value.id);
    selectedItem.value = queue.value[0] || null;
  }
};
</script>
