<template>
  <div class="min-h-screen bg-slate-50 flex flex-col font-sans">
    <!-- Top Government & Hospital Identity Accent Bar (Ethiopian Flag Colors) -->
    <div class="h-1.5 w-full flex">
      <div class="h-full w-1/3 bg-[#009b48]"></div>
      <div class="h-full w-1/3 bg-[#fed100]"></div>
      <div class="h-full w-1/3 bg-[#e4002b]"></div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
          <!-- Logo & Platform Name -->
          <router-link to="/" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-700 flex items-center justify-center text-white shadow-md font-black text-xl">
              +
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="font-extrabold text-slate-900 text-lg tracking-tight">SmartQueue</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold uppercase tracking-wider">Health & MedReminder</span>
              </div>
              <p class="text-[11px] text-slate-400 font-medium hidden sm:block">Integrated Digital Healthcare & Hospital Operations (Ethiopia)</p>
            </div>
          </router-link>

          <!-- Facility Indicator & Role Switcher -->
          <div class="flex items-center gap-3">
            <!-- Public Queue Monitor Direct Link -->
            <router-link 
              to="/queue-board" 
              class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition"
            >
              <span>📺</span> Public TV Screen
            </router-link>

            <!-- Language Switcher -->
            <button 
              @click="toggleLang" 
              class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition"
            >
              {{ currentLang === 'en' ? 'አማርኛ' : 'English' }}
            </button>

            <!-- Current User Badge -->
            <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
              <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs">
                AT
              </div>
              <div class="text-left hidden lg:block">
                <div class="text-xs font-bold text-slate-900 leading-none">Dr. Almaz Tefera</div>
                <div class="text-[10px] text-slate-400">Internal Medicine • TASH</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Horizontal Module Navigation Scroll Bar -->
      <nav class="border-t border-slate-100 bg-slate-50/70 overflow-x-auto">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-1 py-1.5 text-xs font-bold whitespace-nowrap">
          <router-link 
            v-for="item in navItems" 
            :key="item.path"
            :to="item.path"
            :class="[
              'px-3.5 py-2 rounded-xl transition flex items-center gap-1.5',
              $route.path === item.path 
                ? 'bg-blue-700 text-white shadow-xs font-extrabold' 
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
          >
            <span>{{ item.icon }}</span>
            <span>{{ currentLang === 'am' ? item.nameAm : item.name }}</span>
          </router-link>
        </div>
      </nav>
    </header>

    <!-- Main Workspace Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-xs text-slate-400 text-center">
      <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
          SmartQueue Health & MedReminder (SQHM) • Built for Ethiopian Healthcare Systems
        </div>
        <div class="flex items-center gap-4 text-slate-500 font-medium">
          <span>Master Facility Register (MFR)</span>
          <span>•</span>
          <span>DHIS2 Interop</span>
          <span>•</span>
          <span>Ethio Telecom SMS</span>
          <span>•</span>
          <span>Telebirr</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const currentLang = ref('en');

const toggleLang = () => {
  currentLang.value = currentLang.value === 'en' ? 'am' : 'en';
};

const navItems = [
  { name: 'Dashboard', nameAm: 'ዳሽቦርድ', path: '/', icon: '📊' },
  { name: 'Live Queue Screen', nameAm: 'የቀጥታ ሰልፍ', path: '/queue-board', icon: '📢' },
  { name: 'Reception & Check-in', nameAm: 'ሪሴፕሽን', path: '/reception', icon: '🎟️' },
  { name: 'Nurse Triage', nameAm: 'የነርስ ልየታ', path: '/triage', icon: '🩺' },
  { name: 'Doctor Consultation', nameAm: 'የሀኪም ክፍል', path: '/doctor', icon: '👨‍⚕️' },
  { name: 'Central Pharmacy', nameAm: 'ፋርማሲ', path: '/pharmacy', icon: '💊' },
  { name: 'Lab & Radiology', nameAm: 'ላቦራቶሪ / ራዲዮሎጂ', path: '/lab', icon: '🔬' },
  { name: 'Inpatient Wards', nameAm: 'ማረፊያ ክፍሎች', path: '/inpatient', icon: '🛏️' },
  { name: 'Cashier & Billing', nameAm: 'ካሼር እና ክፍያ', path: '/billing', icon: '🧾' },
  { name: 'MedReminder', nameAm: 'የመድኃኒት ማስታወሻ', path: '/medreminder', icon: '⏰' },
  { name: 'AI Navigator', nameAm: 'AI ጤና ረዳት', path: '/chatbot', icon: '🤖' },
];
</script>
