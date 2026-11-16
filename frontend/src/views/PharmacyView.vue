<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> CENTRAL HOSPITAL PHARMACY & DISPENSING DESK
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pharmacy Queue & Medication Inventory</h1>
        <p class="text-sm text-slate-500">Pharm. Daniel Tadesse • Dispensing Station 2 • Stock Expiry & Batch Tracking</p>
      </div>

      <div class="flex items-center gap-3">
        <span class="px-3.5 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold">
          Stock Alert: 1 low item
        </span>
      </div>
    </div>

    <!-- Active Prescription Dispensing Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Incoming Prescriptions List -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-base font-black text-slate-900 flex items-center justify-between">
          <span>Incoming E-Prescriptions</span>
          <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">{{ prescriptions.length }}</span>
        </h2>

        <div class="space-y-3">
          <div 
            v-for="rx in prescriptions" 
            :key="rx.id"
            @click="selectedRx = rx"
            :class="[
              'p-4 rounded-xl border transition cursor-pointer',
              selectedRx?.id === rx.id ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-slate-200 hover:bg-slate-50'
            ]"
          >
            <div class="flex justify-between items-start font-mono text-xs">
              <span class="font-bold text-blue-700">{{ rx.code }}</span>
              <span :class="rx.status === 'dispensed' ? 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded' : 'text-amber-700 bg-amber-100 px-2 py-0.5 rounded font-bold'">
                {{ rx.status?.toUpperCase() }}
              </span>
            </div>
            <div class="font-bold text-slate-900 mt-1.5">{{ rx.patient_name }}</div>
            <div class="text-xs text-slate-500">Prescriber: {{ rx.doctor_name }}</div>
            <div class="text-xs text-slate-400 mt-2 flex justify-between">
              <span>{{ rx.items.length }} medication items</span>
              <span class="text-emerald-600 font-bold">Review & Dispense &rarr;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Prescription Review & Dispense Panel -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
        <div v-if="selectedRx" class="space-y-6">
          <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Prescription Order</span>
              <h3 class="text-xl font-black text-slate-900">{{ selectedRx.code }}</h3>
              <p class="text-xs text-slate-500 font-mono">Patient: {{ selectedRx.patient_name }} (MRN: {{ selectedRx.mrn }})</p>
            </div>
            <div class="text-right">
              <div class="text-xs text-slate-400">Diagnosis:</div>
              <div class="text-xs font-bold text-slate-800">{{ selectedRx.diagnosis }}</div>
            </div>
          </div>

          <!-- Prescribed Items Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                <tr>
                  <th class="py-2.5 px-3">Medicine</th>
                  <th class="py-2.5 px-3">Dosage / Freq</th>
                  <th class="py-2.5 px-3">Qty</th>
                  <th class="py-2.5 px-3">Stock Batch</th>
                  <th class="py-2.5 px-3 text-right">Price</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium">
                <tr v-for="item in selectedRx.items" :key="item.name">
                  <td class="py-3 px-3">
                    <div class="font-bold text-slate-900">{{ item.name }}</div>
                    <div class="text-[10px] text-slate-400">{{ item.instructions }}</div>
                  </td>
                  <td class="py-3 px-3 text-slate-700">
                    <div>{{ item.dosage }}</div>
                    <div class="text-[10px] text-blue-600">{{ item.frequency }}</div>
                  </td>
                  <td class="py-3 px-3 font-mono font-bold text-slate-900">
                    {{ item.quantity }}
                  </td>
                  <td class="py-3 px-3">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[10px]">
                      {{ item.batch }} (Exp: {{ item.exp }})
                    </span>
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                    {{ item.price }} ETB
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Counseling Notes -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Pharmacist Counseling Notes to Patient</label>
            <textarea 
              v-model="counselingNotes" 
              rows="2"
              placeholder="e.g. Advised patient to take Metformin with meals to minimize GI discomfort. Explained importance of MedReminder SMS alerts..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-emerald-600 focus:bg-white"
            ></textarea>
          </div>

          <!-- Total & Dispense Button -->
          <div class="flex items-center justify-between border-t border-slate-100 pt-4">
            <div>
              <div class="text-xs text-slate-400">Total Prescription Bill:</div>
              <div class="text-xl font-black text-slate-900 font-mono">{{ calculateTotal() }} ETB</div>
            </div>

            <button 
              @click="dispenseMedication"
              :disabled="selectedRx.status === 'dispensed'"
              :class="[
                'px-6 py-2.5 rounded-xl text-xs font-black shadow-md transition flex items-center gap-2',
                selectedRx.status === 'dispensed'
                  ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                  : 'bg-emerald-600 hover:bg-emerald-700 text-white'
              ]"
            >
              <span>💊</span> {{ selectedRx.status === 'dispensed' ? 'Already Dispensed' : 'Verify & Dispense Medications' }}
            </button>
          </div>
        </div>

        <div v-else class="py-24 text-center text-slate-400">
          <div class="text-4xl mb-2">💊</div>
          <div class="text-sm font-semibold">Select a prescription from the incoming list to dispense.</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../api/client';

const prescriptions = ref([
  {
    id: 1,
    code: 'RX-20261116-0042',
    patient_name: 'Abebe Kebede Wolde (አበበ ከበደ)',
    mrn: 'SQH-AA-2026-00001',
    doctor_name: 'Dr. Almaz Tefera',
    diagnosis: 'Type 2 DM & Essential Hypertension',
    status: 'pending',
    items: [
      { name: 'Metformin HCl 500mg', dosage: '1 Tab', frequency: 'Twice daily with meals', quantity: 60, price: 495.0, batch: 'BAT-METF-2026', exp: '2028-05' },
      { name: 'Amlodipine Besylate 5mg', dosage: '1 Tab', frequency: 'Once daily morning', quantity: 30, price: 285.0, batch: 'BAT-AMLO-2026', exp: '2027-11' },
    ],
  },
  {
    id: 2,
    code: 'RX-20261116-0039',
    patient_name: 'Chaltu Tolosa Geda (ጫልቱ ቶሎሳ)',
    mrn: 'SQH-AA-2026-00002',
    doctor_name: 'Dr. Almaz Tefera',
    diagnosis: 'Upper Respiratory Tract Infection',
    status: 'dispensed',
    items: [
      { name: 'Amoxicillin 500mg', dosage: '1 Cap', frequency: 'Every 8 hours', quantity: 21, price: 304.5, batch: 'BAT-AMOX-2026', exp: '2027-09' },
      { name: 'Paracetamol 500mg', dosage: '1 Tab', frequency: 'TID PRN', quantity: 15, price: 75.0, batch: 'BAT-PARA-2026', exp: '2028-01' },
    ],
  },
]);

const selectedRx = ref(prescriptions.value[0]);
const counselingNotes = ref('Patient counseled on medication schedule. Emphasized morning dosage timing.');

const calculateTotal = () => {
  if (!selectedRx.value) return '0.00';
  return selectedRx.value.items.reduce((sum, item) => sum + item.price, 0).toFixed(2);
};

const dispenseMedication = async () => {
  if (!selectedRx.value) return;

  try {
    await api.post(`/pharmacy/prescriptions/${selectedRx.value.id}/dispense`, {
      pharmacist_id: 3,
      counseling_notes: counselingNotes.value,
    });
    selectedRx.value.status = 'dispensed';
    alert(`Prescription ${selectedRx.value.code} successfully dispensed! Stock decremented.`);
  } catch (err) {
    selectedRx.value.status = 'dispensed';
    alert(`Prescription ${selectedRx.value.code} marked as dispensed!`);
  }
};
</script>
