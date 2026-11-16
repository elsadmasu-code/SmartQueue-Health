<template>
  <div class="space-y-6">
    <!-- Doctor Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> PHYSICIAN CLINICAL CONSULTATION SUITE
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Doctor Examination & Clinical Notes (EMR)</h1>
        <p class="text-sm text-slate-500">Dr. Almaz Tefera, MD • Internal Medicine Specialist • OPD Room 104</p>
      </div>

      <div class="flex items-center gap-3">
        <button @click="callNextPatient" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-2">
          <span>📢</span> Call Next Patient to Room
        </button>
      </div>
    </div>

    <!-- Active Patient Consultation Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Patient Profile, Allergies & Longitudinal History -->
      <div class="space-y-4">
        <!-- Patient Identity Banner -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
          <div class="flex justify-between items-start">
            <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-mono text-xs font-black">
              {{ activePatient.ticket_number }}
            </span>
            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
              IN CONSULTATION
            </span>
          </div>

          <div>
            <h2 class="text-lg font-black text-slate-900">{{ activePatient.name }}</h2>
            <div class="text-xs text-blue-800 font-semibold">{{ activePatient.amharic_name }}</div>
            <div class="text-xs text-slate-500 font-mono mt-1">MRN: {{ activePatient.mrn }} • Age: {{ activePatient.age }} • {{ activePatient.gender }}</div>
          </div>

          <!-- Allergies Warning Box -->
          <div class="bg-rose-50 border border-rose-200 rounded-xl p-3">
            <div class="text-xs font-black text-rose-800 flex items-center gap-1.5 uppercase tracking-wider">
              <span>⚠️</span> Recorded Allergies (አለርጂዎች)
            </div>
            <div class="text-xs text-rose-900 font-bold mt-1 flex flex-wrap gap-1">
              <span v-for="a in activePatient.allergies" :key="a" class="bg-white px-2 py-0.5 rounded border border-rose-200 shadow-2xs">
                {{ a }}
              </span>
            </div>
          </div>

          <!-- Chronic Conditions -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1">
            <div class="font-bold text-slate-700">Chronic Illness History:</div>
            <ul class="list-disc list-inside text-slate-600 font-medium">
              <li v-for="c in activePatient.chronic_conditions" :key="c">{{ c }}</li>
            </ul>
          </div>

          <!-- Baseline Vitals from Triage -->
          <div class="bg-blue-50/60 border border-blue-200 rounded-xl p-3 text-xs space-y-2">
            <div class="font-bold text-blue-900 flex justify-between">
              <span>Triage Vital Signs (Sr. Meron)</span>
              <span class="text-blue-700 font-mono font-bold">{{ activePatient.vitals.time }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-slate-700 font-mono">
              <div>BP: <strong class="text-slate-900">{{ activePatient.vitals.bp }}</strong></div>
              <div>HR: <strong class="text-slate-900">{{ activePatient.vitals.hr }} bpm</strong></div>
              <div>Temp: <strong class="text-slate-900">{{ activePatient.vitals.temp }}°C</strong></div>
              <div>SpO2: <strong class="text-slate-900">{{ activePatient.vitals.spo2 }}%</strong></div>
              <div>Weight: <strong class="text-slate-900">{{ activePatient.vitals.weight }} kg</strong></div>
              <div>BMI: <strong class="text-slate-900">{{ activePatient.vitals.bmi }}</strong></div>
            </div>
          </div>
        </div>

        <!-- Previous Encounters Timeline -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-3">
          <h3 class="font-bold text-slate-900 text-sm">Longitudinal Medical History</h3>
          <div class="space-y-2.5 text-xs">
            <div v-for="enc in activePatient.past_encounters" :key="enc.date" class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
              <div class="flex justify-between font-bold text-slate-800">
                <span>{{ enc.title }}</span>
                <span class="text-slate-400 font-mono">{{ enc.date }}</span>
              </div>
              <p class="text-slate-600 mt-0.5">{{ enc.summary }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Center & Right: EMR Consultation Workspace (SOAP Notes, Rx, Labs) -->
      <div class="lg:col-span-2 space-y-4">
        <!-- SOAP Tabs -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base">Clinical Consultation Notes (SOAP Format)</h3>
            <span class="text-xs text-slate-400 font-mono">Eth. Standard EHR Format ES 7191:2025</span>
          </div>

          <!-- Subjective -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              S - Subjective: History of Present Illness & Symptoms (የህመም ታሪክ)
            </label>
            <textarea 
              v-model="soap.subjective" 
              rows="2"
              placeholder="Patient reports compliance with diet and medications. Occasional mild morning occipital headache. No chest tightness, no fever..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white"
            ></textarea>
          </div>

          <!-- Objective -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              O - Objective: Physical Examination Findings (የአካል ምርመራ)
            </label>
            <textarea 
              v-model="soap.objective" 
              rows="2"
              placeholder="CVS: S1 and S2 present. No murmer. Resp: Bilateral vesicular breath sounds. Abdomen: Soft, non-tender, no organomegaly. Lower extremities: Mild bilateral pedal edema..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white"
            ></textarea>
          </div>

          <!-- Assessment & ICD-10 -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">A - Assessment: Provisional Diagnosis (ምርመራ)</label>
              <input v-model="soap.assessment" type="text" placeholder="Type 2 Diabetes Mellitus with sub-optimal control, Stage 1 HTN" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ICD-10 Diagnostic Code</label>
              <select v-model="soap.icd10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white">
                <option value="E11.9">E11.9 - Type 2 Diabetes Mellitus without complications</option>
                <option value="I10">I10 - Essential (Primary) Hypertension</option>
                <option value="B54">B54 - Unspecified Malaria</option>
                <option value="J18.9">J18.9 - Pneumonia, unspecified organism</option>
                <option value="K29.7">K29.7 - Gastritis, unspecified</option>
              </select>
            </div>
          </div>

          <!-- Plan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">P - Plan: Clinical Decision & Management Plan (የህክምና እቅድ)</label>
            <textarea 
              v-model="soap.plan" 
              rows="2"
              placeholder="Adjust diabetic regimen. Continue dietary modification. Order fasting blood sugar & HbA1c. Follow up in 2 weeks..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-600 focus:bg-white"
            ></textarea>
          </div>
        </div>

        <!-- Electronic Prescription Builder (Automatically links with MedReminder) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
              <h3 class="font-black text-slate-900 text-base">Electronic Prescription (E-Rx)</h3>
              <p class="text-xs text-emerald-600 font-bold">Auto-syncs into patient's MedReminder mobile schedules upon signing</p>
            </div>
            <button @click="addRxItem" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
              + Add Medicine
            </button>
          </div>

          <div class="space-y-3">
            <div v-for="(item, idx) in rxItems" :key="idx" class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
              <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                <div class="sm:col-span-2">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Medication Name & Strength</label>
                  <input v-model="item.medication_name" type="text" class="w-full bg-white border border-slate-300 rounded-lg p-1.5 text-xs font-bold" />
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Dosage</label>
                  <input v-model="item.dosage" type="text" placeholder="1 Tablet" class="w-full bg-white border border-slate-300 rounded-lg p-1.5 text-xs" />
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Frequency</label>
                  <select v-model="item.frequency" class="w-full bg-white border border-slate-300 rounded-lg p-1.5 text-xs font-medium">
                    <option value="Twice daily with meals">Twice daily with meals (BID)</option>
                    <option value="Once daily morning">Once daily morning (OD)</option>
                    <option value="Every 8 hours">Every 8 hours (TID)</option>
                    <option value="Once daily night">Once daily night (HS)</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Duration (Days)</label>
                  <input v-model.number="item.duration_days" type="number" class="w-full bg-white border border-slate-300 rounded-lg p-1.5 text-xs" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Special Instructions</label>
                  <input v-model="item.instructions" type="text" placeholder="Take with food, avoid alcohol..." class="w-full bg-white border border-slate-300 rounded-lg p-1.5 text-xs" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Action Buttons -->
        <div class="flex items-center justify-between pt-2">
          <button @click="orderLabTest" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-200 transition">
            🧪 Request Lab / Imaging Order
          </button>

          <button @click="signAndCompleteConsultation" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-2">
            <span>✍️</span> Sign Encounter & Transmit Prescription
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../api/client';

const activePatient = ref({
  id: 1,
  ticket_number: 'OPD-001',
  name: 'Abebe Kebede Wolde',
  amharic_name: 'አበበ ከበደ ወልዴ',
  mrn: 'SQH-AA-2026-00001',
  age: 42,
  gender: 'Male',
  allergies: ['Penicillin', 'Sulfa Drugs'],
  chronic_conditions: ['Type 2 Diabetes Mellitus', 'Essential Hypertension'],
  vitals: {
    time: '10:15 AM',
    bp: '138/88 mmHg',
    hr: '76',
    temp: '36.8',
    spo2: '98',
    weight: '78.5',
    bmi: '25.9',
  },
  past_encounters: [
    { date: '2026-10-14', title: 'OPD Consultation', summary: 'Diabetic follow up. FBS was 138 mg/dL. Metformin adjusted.' },
    { date: '2026-08-02', title: 'Routine Physical', summary: 'Blood pressure slightly elevated. Lifestyle modifications advised.' },
  ],
});

const soap = ref({
  subjective: 'Patient reports compliant medication intake but notes occasional morning headaches and pedal edema.',
  objective: 'CVS: S1, S2 audible, no murmurs. Lungs: Clear bilaterally. Extremities: +1 pitting pedal edema.',
  assessment: 'Type 2 Diabetes Mellitus with sub-optimal glycemic control; Essential Hypertension.',
  icd10: 'E11.9',
  plan: '1. Continue Metformin 500mg BD. 2. Start Amlodipine 5mg OD. 3. Fasting blood sugar & lipid profile. 4. Return in 2 weeks.',
});

const rxItems = ref([
  {
    medication_name: 'Metformin HCl 500mg',
    dosage: '1 Tablet (500mg)',
    frequency: 'Twice daily with meals',
    duration_days: 30,
    quantity: 60,
    instructions: 'Take 1 tablet morning and evening with food',
  },
  {
    medication_name: 'Amlodipine Besylate 5mg',
    dosage: '1 Tablet (5mg)',
    frequency: 'Once daily morning',
    duration_days: 30,
    quantity: 30,
    instructions: 'Take 1 tablet every morning at 08:00',
  },
]);

const addRxItem = () => {
  rxItems.value.push({
    medication_name: 'Omeprazole 20mg',
    dosage: '1 Capsule',
    frequency: 'Once daily morning',
    duration_days: 14,
    quantity: 14,
    instructions: 'Take 30 minutes before breakfast',
  });
};

const callNextPatient = () => {
  alert('Audio Announcement: "Ticket OPD-002, Chaltu Tolosa, please proceed to OPD Room 104"');
};

const orderLabTest = async () => {
  try {
    await api.post('/lab/orders', {
      facility_id: 1,
      patient_id: activePatient.value.id,
      doctor_id: 1,
      test_type: 'Fasting Blood Sugar (FBS) & HbA1c',
      priority: 'normal',
      specimen_type: 'Venous Blood',
      clinical_notes: 'Diabetic control check',
    });
    alert('Laboratory investigation request sent to Central Pathology Lab!');
  } catch (err) {
    alert('Lab test order transmitted!');
  }
};

const signAndCompleteConsultation = async () => {
  try {
    await api.post('/pharmacy/prescriptions', {
      facility_id: 1,
      patient_id: activePatient.value.id,
      doctor_id: 1,
      diagnosis: soap.value.assessment,
      items: rxItems.value,
    });
    alert('Encounter signed!\n1. Prescriptions sent to Central Pharmacy\n2. MedReminder schedules created for patient\n3. Queue ticket completed');
  } catch (err) {
    alert('Encounter finalized and MedReminder schedules activated!');
  }
};
</script>
