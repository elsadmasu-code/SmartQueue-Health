<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-amber-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> INPATIENT WARDS & BED OCCUPANCY MANAGEMENT
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ward Matrix & Bed Availability</h1>
        <p class="text-sm text-slate-500">Real-time bed allocation, clinical admissions, ward transfers and discharge summaries</p>
      </div>

      <div class="flex items-center gap-3">
        <span class="px-3.5 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold">
          Occupancy Rate: 81.8%
        </span>
      </div>
    </div>

    <!-- Ward Matrix Selector Tabs -->
    <div class="flex flex-wrap gap-3">
      <button 
        v-for="ward in wards" 
        :key="ward.id"
        @click="selectedWardId = ward.id"
        :class="[
          'px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs',
          selectedWardId === ward.id ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'
        ]"
      >
        <span>🏥</span> {{ ward.name }} ({{ ward.beds.length }} Beds)
      </button>
    </div>

    <!-- Visual Bed Grid for Selected Ward -->
    <div v-if="currentWard" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-6">
      <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
          <h2 class="text-lg font-black text-slate-900">{{ currentWard.name }} ({{ currentWard.code }})</h2>
          <p class="text-xs text-slate-400">Location: {{ currentWard.floor_location }} • Rate: 250.00 ETB / night</p>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-4 text-xs font-semibold">
          <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-500"></span> Available</span>
          <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-500"></span> Occupied</span>
          <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-500"></span> Maintenance</span>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        <div 
          v-for="bed in currentWard.beds" 
          :key="bed.bed_number"
          @click="selectBed(bed)"
          :class="[
            'p-4 rounded-2xl border-2 transition cursor-pointer relative overflow-hidden',
            bed.status === 'occupied' 
              ? 'border-rose-300 bg-rose-50/50 hover:bg-rose-50' 
              : 'border-emerald-300 bg-emerald-50/40 hover:bg-emerald-50'
          ]"
        >
          <div class="flex justify-between items-center text-xs">
            <span class="font-mono font-black text-slate-800">{{ bed.bed_number }}</span>
            <span :class="[
              'px-2 py-0.5 rounded text-[10px] font-black',
              bed.status === 'occupied' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800'
            ]">
              {{ bed.status === 'occupied' ? 'OCCUPIED' : 'FREE' }}
            </span>
          </div>

          <div class="mt-4 text-center">
            <div class="text-2xl mb-1">{{ bed.status === 'occupied' ? '🛌' : '🛏️' }}</div>
            <div v-if="bed.patient" class="text-xs font-bold text-slate-800 truncate">
              {{ bed.patient }}
            </div>
            <div v-else class="text-xs text-slate-400 font-semibold">
              Ready for Admission
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const selectedWardId = ref(1);

const wards = ref([
  {
    id: 1,
    name: 'Male Medical Ward',
    code: 'MMW',
    floor_location: 'Block B, 2nd Floor',
    beds: [
      { bed_number: 'MMW-B01', status: 'occupied', patient: 'Abebe Kebede (አበበ)' },
      { bed_number: 'MMW-B02', status: 'available', patient: null },
      { bed_number: 'MMW-B03', status: 'available', patient: null },
      { bed_number: 'MMW-B04', status: 'available', patient: null },
      { bed_number: 'MMW-B05', status: 'available', patient: null },
      { bed_number: 'MMW-B06', status: 'available', patient: null },
      { bed_number: 'MMW-B07', status: 'available', patient: null },
      { bed_number: 'MMW-B08', status: 'available', patient: null },
      { bed_number: 'MMW-B09', status: 'available', patient: null },
      { bed_number: 'MMW-B10', status: 'available', patient: null },
    ],
  },
  {
    id: 2,
    name: 'Female Surgical Ward',
    code: 'FSW',
    floor_location: 'Block B, 3rd Floor',
    beds: [
      { bed_number: 'FSW-B01', status: 'available', patient: null },
      { bed_number: 'FSW-B02', status: 'available', patient: null },
      { bed_number: 'FSW-B03', status: 'available', patient: null },
      { bed_number: 'FSW-B04', status: 'available', patient: null },
      { bed_number: 'FSW-B05', status: 'available', patient: null },
      { bed_number: 'FSW-B06', status: 'available', patient: null },
      { bed_number: 'FSW-B07', status: 'available', patient: null },
      { bed_number: 'FSW-B08', status: 'available', patient: null },
    ],
  },
  {
    id: 3,
    name: 'Intensive Care Unit (ICU)',
    code: 'ICU',
    floor_location: 'Block A, 1st Floor',
    beds: [
      { bed_number: 'ICU-B01', status: 'available', patient: null },
      { bed_number: 'ICU-B02', status: 'available', patient: null },
      { bed_number: 'ICU-B03', status: 'available', patient: null },
      { bed_number: 'ICU-B04', status: 'available', patient: null },
    ],
  },
]);

const currentWard = computed(() => {
  return wards.value.find(w => w.id === selectedWardId.value) || wards.value[0];
});

const selectBed = (bed) => {
  if (bed.status === 'occupied') {
    alert(`Bed ${bed.bed_number} is occupied by ${bed.patient}. You can issue discharge summary or ward transfer.`);
  } else {
    alert(`Bed ${bed.bed_number} is available. Select patient to admit to ${currentWard.value.name}.`);
  }
};
</script>
