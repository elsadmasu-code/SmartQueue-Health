<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> PATIENT ACCOUNTS, CASHIER & BILLING DESK
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Billing & Payment Settlements</h1>
        <p class="text-sm text-slate-500">Telebirr • CBE Birr • Cash • Ethiopian Community Based Health Insurance (CBHI)</p>
      </div>

      <div class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold">
        Cashier: Tigist Alemu (Counter 1)
      </div>
    </div>

    <!-- Billing Invoices Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Invoices List -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-base font-black text-slate-900 flex items-center justify-between">
          <span>Hospital Invoices</span>
          <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">{{ invoices.length }}</span>
        </h2>

        <div class="space-y-3">
          <div 
            v-for="inv in invoices" 
            :key="inv.id"
            @click="selectedInv = inv"
            :class="[
              'p-4 rounded-xl border transition cursor-pointer',
              selectedInv?.id === inv.id ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-slate-200 hover:bg-slate-50'
            ]"
          >
            <div class="flex justify-between items-start font-mono text-xs">
              <span class="font-bold text-blue-700">{{ inv.invoice_number }}</span>
              <span :class="inv.payment_status === 'paid' ? 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded font-bold' : 'text-rose-700 bg-rose-100 px-2 py-0.5 rounded font-bold'">
                {{ inv.payment_status?.toUpperCase() }}
              </span>
            </div>
            <div class="font-bold text-slate-900 mt-1.5">{{ inv.patient_name }}</div>
            <div class="text-xs text-slate-500">MRN: {{ inv.mrn }}</div>
            <div class="text-xs text-slate-700 font-mono font-bold mt-2 flex justify-between">
              <span>Total: {{ inv.total_amount }} ETB</span>
              <span class="text-emerald-600">Settle &rarr;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Settlement & Receipt Panel -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
        <div v-if="selectedInv" class="space-y-6">
          <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Hospital Invoice Settlement</span>
              <h3 class="text-xl font-black text-slate-900">{{ selectedInv.invoice_number }}</h3>
              <p class="text-xs text-slate-500 font-mono">Patient: {{ selectedInv.patient_name }} (MRN: {{ selectedInv.mrn }})</p>
            </div>
            <div class="text-right">
              <div class="text-xs text-slate-400">Balance Due:</div>
              <div class="text-xl font-black text-slate-900 font-mono">{{ selectedInv.balance }} ETB</div>
            </div>
          </div>

          <!-- Invoice Line Items Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                <tr>
                  <th class="py-2.5 px-3">Service Description</th>
                  <th class="py-2.5 px-3">Type</th>
                  <th class="py-2.5 px-3">Qty</th>
                  <th class="py-2.5 px-3 text-right">Unit Price</th>
                  <th class="py-2.5 px-3 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium">
                <tr v-for="item in selectedInv.items" :key="item.description">
                  <td class="py-3 px-3 font-bold text-slate-900">{{ item.description }}</td>
                  <td class="py-3 px-3 uppercase text-slate-500 text-[10px]">{{ item.service_type }}</td>
                  <td class="py-3 px-3 font-mono">{{ item.quantity }}</td>
                  <td class="py-3 px-3 text-right font-mono text-slate-600">{{ item.unit_price }} ETB</td>
                  <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">{{ item.total_price }} ETB</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Payment Provider Selection -->
          <div class="space-y-3">
            <label class="block text-xs font-bold text-slate-700">Select Ethiopian Payment Channel</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <button 
                type="button" 
                @click="paymentMethod = 'telebirr'"
                :class="['p-3 rounded-xl border text-center transition font-bold text-xs', paymentMethod === 'telebirr' ? 'border-blue-600 bg-blue-50 text-blue-900 shadow-sm' : 'border-slate-200 hover:bg-slate-50']"
              >
                <div class="text-lg mb-1">📱</div>
                Telebirr (ቴሌብር)
              </button>
              <button 
                type="button" 
                @click="paymentMethod = 'cbe_birr'"
                :class="['p-3 rounded-xl border text-center transition font-bold text-xs', paymentMethod === 'cbe_birr' ? 'border-amber-600 bg-amber-50 text-amber-900 shadow-sm' : 'border-slate-200 hover:bg-slate-50']"
              >
                <div class="text-lg mb-1">🏦</div>
                CBE Birr (ሲቢኢ)
              </button>
              <button 
                type="button" 
                @click="paymentMethod = 'cash'"
                :class="['p-3 rounded-xl border text-center transition font-bold text-xs', paymentMethod === 'cash' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-sm' : 'border-slate-200 hover:bg-slate-50']"
              >
                <div class="text-lg mb-1">💵</div>
                Cash (ጥሬ ገንዘብ)
              </button>
              <button 
                type="button" 
                @click="paymentMethod = 'insurance'"
                :class="['p-3 rounded-xl border text-center transition font-bold text-xs', paymentMethod === 'insurance' ? 'border-indigo-600 bg-indigo-50 text-indigo-900 shadow-sm' : 'border-slate-200 hover:bg-slate-50']"
              >
                <div class="text-lg mb-1">📑</div>
                CBHI Insurance
              </button>
            </div>
          </div>

          <!-- Pay & Receipt Action -->
          <div class="flex items-center justify-between border-t border-slate-100 pt-4">
            <div>
              <div class="text-xs text-slate-400">Total Settlement Amount:</div>
              <div class="text-2xl font-black text-slate-900 font-mono">{{ selectedInv.total_amount }} ETB</div>
            </div>

            <button 
              @click="recordSettlement"
              :disabled="selectedInv.payment_status === 'paid'"
              :class="[
                'px-6 py-2.5 rounded-xl text-xs font-black shadow-md transition flex items-center gap-2',
                selectedInv.payment_status === 'paid'
                  ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                  : 'bg-emerald-600 hover:bg-emerald-700 text-white'
              ]"
            >
              <span>🧾</span> {{ selectedInv.payment_status === 'paid' ? 'Paid & Settled' : 'Confirm Payment & Issue Receipt' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const invoices = ref([
  {
    id: 1,
    invoice_number: 'INV-20261116-0091',
    patient_name: 'Abebe Kebede Wolde (አበበ ከበደ)',
    mrn: 'SQH-AA-2026-00001',
    subtotal: 280.0,
    total_amount: 280.0,
    paid_amount: 280.0,
    balance: 0.0,
    payment_status: 'paid',
    items: [
      { description: 'Outpatient Specialist Consultation (Internal Med)', service_type: 'consultation', quantity: 1, unit_price: 150.0, total_price: 150.0 },
      { description: 'Fasting Blood Sugar & Lipid Profile Examination', service_type: 'lab', quantity: 1, unit_price: 130.0, total_price: 130.0 },
    ],
  },
  {
    id: 2,
    invoice_number: 'INV-20261116-0094',
    patient_name: 'Chaltu Tolosa Geda (ጫልቱ ቶሎሳ)',
    mrn: 'SQH-AA-2026-00002',
    subtotal: 379.5,
    total_amount: 379.5,
    paid_amount: 0.0,
    balance: 379.5,
    payment_status: 'unpaid',
    items: [
      { description: 'Amoxicillin 500mg (21 caps) & Paracetamol', service_type: 'pharmacy', quantity: 1, unit_price: 379.5, total_price: 379.5 },
    ],
  },
]);

const selectedInv = ref(invoices.value[1]);
const paymentMethod = ref('telebirr');

const recordSettlement = () => {
  if (!selectedInv.value) return;
  selectedInv.value.payment_status = 'paid';
  selectedInv.value.paid_amount = selectedInv.value.total_amount;
  selectedInv.value.balance = 0.0;

  const txn = paymentMethod.value === 'telebirr' ? 'TB-884920182' : (paymentMethod.value === 'cbe_birr' ? 'CBE-110928374' : 'CSH-9928172');
  alert(`Payment Confirmed!\nMethod: ${paymentMethod.value.toUpperCase()}\nTransaction Ref: ${txn}\nOfficial electronic receipt generated and logged in ledger.`);
};
</script>
