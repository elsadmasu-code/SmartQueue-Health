<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-indigo-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> DIAGNOSTIC PATHOLOGY & MEDICAL IMAGING SUITE
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laboratory Orders & Radiology Results</h1>
        <p class="text-sm text-slate-500">Tech. Henok Girma • Barcode Specimen Tracking • Critical Value Alerts</p>
      </div>

      <div class="flex gap-2">
        <button 
          @click="activeTab = 'lab'" 
          :class="['px-4 py-2 rounded-xl text-xs font-bold transition', activeTab === 'lab' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600']"
        >
          Pathology Lab Orders ({{ labOrders.length }})
        </button>
        <button 
          @click="activeTab = 'rad'" 
          :class="['px-4 py-2 rounded-xl text-xs font-bold transition', activeTab === 'rad' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600']"
        >
          Radiology & Imaging ({{ imagingOrders.length }})
        </button>
      </div>
    </div>

    <!-- Pathology Lab Tab Content -->
    <div v-if="activeTab === 'lab'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Orders List -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-base font-black text-slate-900">Lab Test Requests</h2>
        <div class="space-y-3">
          <div 
            v-for="order in labOrders" 
            :key="order.id"
            @click="selectedLab = order"
            :class="[
              'p-4 rounded-xl border transition cursor-pointer',
              selectedLab?.id === order.id ? 'border-indigo-500 bg-indigo-50/50 shadow-sm' : 'border-slate-200 hover:bg-slate-50'
            ]"
          >
            <div class="flex justify-between items-start font-mono text-xs">
              <span class="font-bold text-indigo-700">{{ order.order_number }}</span>
              <span :class="order.status === 'completed' ? 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded' : 'text-blue-700 bg-blue-100 px-2 py-0.5 rounded font-bold'">
                {{ order.status?.toUpperCase() }}
              </span>
            </div>
            <div class="font-bold text-slate-900 mt-1">{{ order.patient_name }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ order.test_type }}</div>
            <div class="text-xs text-slate-400 mt-2 font-mono flex justify-between">
              <span>Barcode: {{ order.barcode }}</span>
              <span class="text-indigo-600 font-bold">View Results &rarr;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Lab Result Entry / Review Panel -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
        <div v-if="selectedLab" class="space-y-6">
          <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Test Order Details</span>
              <h3 class="text-xl font-black text-slate-900">{{ selectedLab.test_type }}</h3>
              <p class="text-xs text-slate-500 font-mono">Patient: {{ selectedLab.patient_name }} (MRN: {{ selectedLab.mrn }})</p>
            </div>
            <div class="bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-xl font-mono text-xs text-indigo-900">
              Specimen: <strong>{{ selectedLab.barcode }}</strong>
            </div>
          </div>

          <!-- Results Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                <tr>
                  <th class="py-2.5 px-3">Parameter Test</th>
                  <th class="py-2.5 px-3">Measured Result</th>
                  <th class="py-2.5 px-3">Unit</th>
                  <th class="py-2.5 px-3">Reference Range</th>
                  <th class="py-2.5 px-3">Flag</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium">
                <tr v-for="res in selectedLab.results" :key="res.test_name">
                  <td class="py-3 px-3 font-bold text-slate-900">{{ res.test_name }}</td>
                  <td class="py-3 px-3 font-mono font-bold text-slate-800">
                    <input v-if="selectedLab.status !== 'completed'" v-model="res.result_value" class="w-20 bg-slate-50 border border-slate-200 p-1 rounded font-bold" />
                    <span v-else>{{ res.result_value }}</span>
                  </td>
                  <td class="py-3 px-3 text-slate-500 font-mono">{{ res.unit }}</td>
                  <td class="py-3 px-3 text-slate-500 font-mono">{{ res.reference_range }}</td>
                  <td class="py-3 px-3">
                    <span :class="[
                      'px-2 py-0.5 rounded text-[10px] font-black',
                      res.flag === 'high' ? 'bg-rose-100 text-rose-800' : (res.flag === 'low' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800')
                    ]">
                      {{ res.flag?.toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Verified By Signature -->
          <div class="flex items-center justify-between border-t border-slate-100 pt-4">
            <div class="text-xs text-slate-400">
              Validated by: <strong class="text-slate-800">Henok Girma, Medical Laboratory Technologist</strong>
            </div>

            <button 
              @click="verifyResults"
              class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-2"
            >
              <span>🔬</span> Transmit Verified Results to Doctor EMR
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Medical Imaging Tab Content -->
    <div v-if="activeTab === 'rad'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
      <h2 class="text-base font-black text-slate-900">Radiology & Medical Imaging Orders (X-Ray / Ultrasound / CT)</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="order in imagingOrders" :key="order.id" class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
          <div class="flex justify-between items-start font-mono text-xs">
            <span class="font-bold text-blue-700">{{ order.order_number }}</span>
            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">{{ order.modality }}</span>
          </div>
          <div class="font-bold text-slate-900">{{ order.patient_name }} ({{ order.body_part }})</div>
          <div class="text-xs text-slate-600"><strong>Indication:</strong> {{ order.clinical_indication }}</div>
          <div class="p-3 bg-white rounded-lg border border-slate-200 text-xs text-slate-700 mt-2">
            <strong>Findings:</strong> {{ order.findings || 'Pending radiologist examination' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../api/client';

const activeTab = ref('lab');

const labOrders = ref([
  {
    id: 1,
    order_number: 'LAB-20261116-0012',
    patient_name: 'Abebe Kebede Wolde (አበበ ከበደ)',
    mrn: 'SQH-AA-2026-00001',
    test_type: 'Fasting Blood Sugar & Lipid Profile',
    barcode: 'SPEC-882901',
    status: 'completed',
    results: [
      { test_name: 'Fasting Blood Sugar (FBS)', result_value: '142', unit: 'mg/dL', reference_range: '70 - 100 mg/dL', flag: 'high' },
      { test_name: 'Total Cholesterol', result_value: '186', unit: 'mg/dL', reference_range: '< 200 mg/dL', flag: 'normal' },
      { test_name: 'Triglycerides', result_value: '165', unit: 'mg/dL', reference_range: '< 150 mg/dL', flag: 'high' },
      { test_name: 'HDL Cholesterol', result_value: '44', unit: 'mg/dL', reference_range: '> 40 mg/dL', flag: 'normal' },
    ],
  },
  {
    id: 2,
    order_number: 'LAB-20261116-0015',
    patient_name: 'Chaltu Tolosa Geda (ጫልቱ ቶሎሳ)',
    mrn: 'SQH-AA-2026-00002',
    test_type: 'Complete Blood Count (CBC) & Malaria Rapid Test',
    barcode: 'SPEC-991823',
    status: 'ordered',
    results: [
      { test_name: 'Hemoglobin (Hgb)', result_value: '13.4', unit: 'g/dL', reference_range: '12.0 - 16.0 g/dL', flag: 'normal' },
      { test_name: 'White Blood Cell (WBC)', result_value: '7.8', unit: '10^3/uL', reference_range: '4.0 - 11.0 10^3/uL', flag: 'normal' },
      { test_name: 'Malaria Rapid Test (P. falciparum / P. vivax)', result_value: 'Negative', unit: '', reference_range: 'Negative', flag: 'normal' },
    ],
  },
]);

const imagingOrders = ref([
  {
    id: 1,
    order_number: 'RAD-20261116-0004',
    patient_name: 'Abebe Kebede Wolde',
    modality: 'Chest X-Ray (PA View)',
    body_part: 'Chest',
    clinical_indication: 'Check cardiothoracic ratio, evaluate for pulmonary congestion',
    findings: 'No active focal pulmonary consolidation. Mild cardiomegaly noted, compatible with hypertensive heart changes. Costophrenic angles are sharp.',
  },
  {
    id: 2,
    order_number: 'RAD-20261116-0007',
    patient_name: 'Yohannes Desta',
    modality: 'Abdominal Ultrasound',
    body_part: 'Abdomen',
    clinical_indication: 'Right upper quadrant pain, rule out cholelithiasis',
    findings: 'Liver normal size and echotexture. Gallbladder thin-walled with no intraluminal calculi.',
  },
]);

const selectedLab = ref(labOrders.value[0]);

const verifyResults = async () => {
  if (!selectedLab.value) return;
  selectedLab.value.status = 'completed';
  alert(`Lab order ${selectedLab.value.order_number} verified and released to patient longitudinal EMR!`);
};
</script>
