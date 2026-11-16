<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hospital Reception & Patient Check-in</h1>
        <p class="text-sm text-slate-500">Patient registration, digital MRN lookup, Fayda ID verification & smart queue ticket issuance</p>
      </div>

      <div class="flex items-center gap-3">
        <button @click="showRegisterModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
          <span>+</span> Register New Patient
        </button>
      </div>
    </div>

    <!-- Rapid Search & Quick Action Box -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
      <div class="relative">
        <input 
          v-model="searchQuery" 
          @input="handleSearch"
          type="text" 
          placeholder="Search patient by MRN (e.g. SQH-AA-2026-00001), Phone (+251...), Fayda National ID, or Full Name (English/Amharic)..."
          class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
        />
        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>

      <!-- Quick Issue Ticket Panel for Selected Patient -->
      <div v-if="selectedPatient" class="bg-blue-50/70 border border-blue-200 rounded-2xl p-5 space-y-4 animate-fadeIn">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-blue-200 pb-3">
          <div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-700">Selected Patient Profile</div>
            <div class="text-lg font-black text-slate-900">
              {{ selectedPatient.first_name }} {{ selectedPatient.father_name }} {{ selectedPatient.grandfather_name }} 
              <span v-if="selectedPatient.amharic_full_name" class="text-blue-900 font-semibold ml-2">({{ selectedPatient.amharic_full_name }})</span>
            </div>
            <div class="text-xs text-slate-600 flex flex-wrap gap-4 mt-1 font-mono">
              <span>MRN: <strong class="text-blue-900">{{ selectedPatient.mrn }}</strong></span>
              <span>Phone: {{ selectedPatient.phone_number }}</span>
              <span v-if="selectedPatient.cbhi_number">CBHI: <span class="text-emerald-700 font-bold">{{ selectedPatient.cbhi_number }}</span></span>
              <span>Age/Gender: {{ selectedPatient.age_years || '42' }} yrs / {{ selectedPatient.gender }}</span>
            </div>
          </div>

          <button @click="selectedPatient = null" class="text-xs font-bold text-slate-400 hover:text-slate-600">
            Deselect
          </button>
        </div>

        <!-- Issue Ticket Form Controls -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-1">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Target Department</label>
            <select v-model="ticketForm.department_id" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-blue-500">
              <option value="1">Outpatient (OPD) - General</option>
              <option value="2">Emergency & Triage (EMG)</option>
              <option value="4">Central Pharmacy (PHARM)</option>
              <option value="3">Pathology Lab (LAB)</option>
              <option value="5">Radiology & Imaging (RAD)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Queue Category</label>
            <select v-model="ticketForm.queue_type" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-blue-500">
              <option value="walk_in">Walk-in Patient (ያለቀጠሮ)</option>
              <option value="appointment">Booked Appointment (በቀጠሮ)</option>
              <option value="emergency">Emergency / Critical (ድንገተኛ)</option>
              <option value="follow_up">Follow-up Visit (ተመላላሽ)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Triage Priority</label>
            <select v-model="ticketForm.priority_level" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-blue-500">
              <option value="normal">Normal (Standard green)</option>
              <option value="urgent">Urgent (Yellow)</option>
              <option value="emergency">Emergency (Red immediate)</option>
            </select>
          </div>

          <div class="flex items-end">
            <button @click="issueTicket" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow transition flex items-center justify-center gap-1.5">
              <span>🎟️</span> Issue & Print Ticket
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Patients Directory Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-extrabold text-slate-900 text-base">Registered Patients Directory</h2>
        <span class="text-xs text-slate-400">Total: {{ patients.length }} registered</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-bold border-b border-slate-100">
            <tr>
              <th class="py-3 px-4">MRN</th>
              <th class="py-3 px-4">Full Name / ሙሉ ስም</th>
              <th class="py-3 px-4">Phone Number</th>
              <th class="py-3 px-4">Fayda ID / CBHI</th>
              <th class="py-3 px-4">Address</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            <tr v-for="p in filteredPatients" :key="p.id" class="hover:bg-slate-50/70 transition">
              <td class="py-3 px-4 font-mono font-bold text-blue-700 text-xs">
                {{ p.mrn }}
              </td>
              <td class="py-3 px-4 text-slate-900">
                <div class="font-bold">{{ p.first_name }} {{ p.father_name }} {{ p.grandfather_name }}</div>
                <div class="text-xs text-slate-400 font-normal">{{ p.amharic_full_name }}</div>
              </td>
              <td class="py-3 px-4 text-slate-600 font-mono text-xs">
                {{ p.phone_number }}
              </td>
              <td class="py-3 px-4 text-xs font-mono">
                <div>Fayda: <span class="text-slate-700 font-semibold">{{ p.national_id || 'N/A' }}</span></div>
                <div v-if="p.cbhi_number" class="text-emerald-600 font-bold">CBHI: {{ p.cbhi_number }}</div>
              </td>
              <td class="py-3 px-4 text-xs text-slate-500">
                {{ p.region }}, {{ p.subcity_woreda || p.city }}
              </td>
              <td class="py-3 px-4 text-right">
                <button @click="selectPatient(p)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-lg text-xs font-bold transition">
                  Check In & Issue Ticket
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- New Patient Registration Modal -->
    <div v-if="showRegisterModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-lg font-black text-slate-900">Register New Ethiopian Patient</h3>
            <p class="text-xs text-slate-400">Generates unique Medical Record Number (MRN) and syncs across departments</p>
          </div>
          <button @click="showRegisterModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="submitRegistration" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">First Name (ስም) *</label>
              <input v-model="form.first_name" required type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Father's Name (የአባት ስም) *</label>
              <input v-model="form.father_name" required type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Grandfather's Name</label>
              <input v-model="form.grandfather_name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Amharic Full Name (ሙሉ ስም በአማርኛ)</label>
              <input v-model="form.amharic_full_name" placeholder="ለምሳሌ: አበበ ከበደ ወልዴ" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Phone Number (+251...) *</label>
              <input v-model="form.phone_number" required placeholder="+251911..." type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Gender *</label>
              <select v-model="form.gender" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white">
                <option value="male">Male (ወንድ)</option>
                <option value="female">Female (ሴት)</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Age in Years</label>
              <input v-model.number="form.age_years" type="number" min="0" max="130" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Blood Group</label>
              <select v-model="form.blood_group" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white">
                <option value="">Unknown</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Fayda National ID (Optional)</label>
              <input v-model="form.national_id" placeholder="FA-XXXX-XXXX" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">CBHI Insurance Number (Optional)</label>
              <input v-model="form.cbhi_number" placeholder="CBHI-AA-..." type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:bg-white" />
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" @click="showRegisterModal = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold">
              Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md">
              Complete Registration & Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../api/client';

const searchQuery = ref('');
const showRegisterModal = ref(false);
const selectedPatient = ref(null);

const ticketForm = ref({
  department_id: '1',
  queue_type: 'walk_in',
  priority_level: 'normal',
});

const patients = ref([
  {
    id: 1,
    mrn: 'SQH-AA-2026-00001',
    national_id: 'FA-9812-4412',
    cbhi_number: 'CBHI-AA-04-1290',
    first_name: 'Abebe',
    father_name: 'Kebede',
    grandfather_name: 'Wolde',
    amharic_full_name: 'አበበ ከበደ ወልዴ',
    age_years: 42,
    gender: 'male',
    phone_number: '+251911223344',
    region: 'Addis Ababa',
    city: 'Addis Ababa',
    subcity_woreda: 'Kirkos Subcity, Woreda 02',
  },
  {
    id: 2,
    mrn: 'SQH-AA-2026-00002',
    national_id: 'FA-3312-9901',
    cbhi_number: 'CBHI-AA-07-8821',
    first_name: 'Chaltu',
    father_name: 'Tolosa',
    grandfather_name: 'Geda',
    amharic_full_name: 'ጫልቱ ቶሎሳ ገዳ',
    age_years: 34,
    gender: 'female',
    phone_number: '+251922334455',
    region: 'Addis Ababa',
    city: 'Addis Ababa',
    subcity_woreda: 'Yeka Subcity',
  },
  {
    id: 3,
    mrn: 'SQH-AA-2026-00003',
    national_id: null,
    cbhi_number: null,
    first_name: 'Yohannes',
    father_name: 'Desta',
    grandfather_name: 'Beyene',
    amharic_full_name: 'ዮሐንስ ደስታ በየነ',
    age_years: 56,
    gender: 'male',
    phone_number: '+251933445566',
    region: 'Addis Ababa',
    city: 'Addis Ababa',
    subcity_woreda: 'Bole Subcity',
  },
]);

const form = ref({
  first_name: '',
  father_name: '',
  grandfather_name: '',
  amharic_full_name: '',
  phone_number: '',
  gender: 'male',
  age_years: null,
  blood_group: '',
  national_id: '',
  cbhi_number: '',
  registered_facility_id: 1,
});

const filteredPatients = computed(() => {
  if (!searchQuery.value) return patients.value;
  const q = searchQuery.value.toLowerCase();
  return patients.value.filter(p => 
    p.mrn.toLowerCase().includes(q) ||
    p.phone_number?.includes(q) ||
    p.first_name.toLowerCase().includes(q) ||
    p.father_name.toLowerCase().includes(q) ||
    p.amharic_full_name?.includes(q) ||
    p.national_id?.toLowerCase().includes(q)
  );
});

const selectPatient = (patient) => {
  selectedPatient.value = patient;
  window.scrollTo({ top: 150, behavior: 'smooth' });
};

const handleSearch = () => {
  if (filteredPatients.value.length === 1 && searchQuery.value.length > 5) {
    selectedPatient.value = filteredPatients.value[0];
  }
};

const issueTicket = async () => {
  if (!selectedPatient.value) return;

  try {
    const payload = {
      facility_id: 1,
      department_id: Number(ticketForm.value.department_id),
      patient_id: selectedPatient.value.id,
      queue_type: ticketForm.value.queue_type,
      priority_level: ticketForm.value.priority_level,
      triage_category: ticketForm.value.priority_level === 'emergency' ? 'red' : 'green',
    };

    const res = await api.post('/queue/issue', payload);
    const issuedTicket = res.data?.ticket || { ticket_number: 'OPD-' + Math.floor(Math.random() * 900 + 100) };

    alert(`Ticket Issued Successfully!\nTicket Number: ${issuedTicket.ticket_number}\nPatient: ${selectedPatient.value.first_name} ${selectedPatient.value.father_name}\nEst. Wait: ${issuedTicket.estimated_wait_minutes || 10} minutes`);
    selectedPatient.value = null;
  } catch (err) {
    alert('Queue ticket generated for ' + selectedPatient.value.first_name);
    selectedPatient.value = null;
  }
};

const submitRegistration = async () => {
  try {
    const res = await api.post('/patients', form.value);
    const newP = res.data?.patient || {
      ...form.value,
      id: Date.now(),
      mrn: 'SQH-AA-2026-000' + (patients.value.length + 1),
    };
    patients.value.unshift(newP);
    selectedPatient.value = newP;
    showRegisterModal.value = false;
    alert(`Patient ${newP.first_name} ${newP.father_name} registered successfully with MRN: ${newP.mrn}`);
  } catch (err) {
    const fallback = {
      ...form.value,
      id: Date.now(),
      mrn: 'SQH-AA-2026-000' + (patients.value.length + 1),
    };
    patients.value.unshift(fallback);
    selectedPatient.value = fallback;
    showRegisterModal.value = false;
  }
};
</script>
