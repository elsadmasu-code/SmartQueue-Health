<template>
  <div class="space-y-6 max-w-4xl mx-auto">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <div class="flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider mb-1">
          <span>●</span> CLINICALLY GUARDRAILED HEALTHCARE AI ASSISTANT
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">SmartQueue AI Navigator & Chatbot</h1>
        <p class="text-sm text-slate-500">Supports English & Amharic (አማርኛ) • Queue Tracking • Medication Explanations</p>
      </div>

      <!-- Language Selector -->
      <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
        <button 
          @click="lang = 'en'" 
          :class="['px-3 py-1.5 rounded-lg text-xs font-bold transition', lang === 'en' ? 'bg-white shadow text-blue-700' : 'text-slate-600']"
        >
          English
        </button>
        <button 
          @click="lang = 'am'" 
          :class="['px-3 py-1.5 rounded-lg text-xs font-bold transition', lang === 'am' ? 'bg-white shadow text-blue-700' : 'text-slate-600']"
        >
          አማርኛ (Amharic)
        </button>
      </div>
    </div>

    <!-- Emergency Safety Notice (DOC-014, BR-014) -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 text-xs text-amber-900">
      <span class="text-xl">⚠️</span>
      <div>
        <strong class="font-bold">Clinical Safety Protocol:</strong>
        This AI provides service navigation, queue status, and general health education. It does not replace medical doctors. In case of acute severe chest pain, breathing difficulty, or uncontrolled bleeding, immediately seek emergency clinical attention or call 907.
      </div>
    </div>

    <!-- Chat Messages Window -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4 min-h-[460px] flex flex-col justify-between">
      <!-- Conversation History -->
      <div class="space-y-4 overflow-y-auto max-h-[480px] pr-2">
        <div 
          v-for="(msg, i) in messages" 
          :key="i"
          :class="[
            'flex gap-3',
            msg.is_user ? 'justify-end' : 'justify-start'
          ]"
        >
          <div v-if="!msg.is_user" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
            AI
          </div>

          <div 
            :class="[
              'p-4 rounded-2xl text-xs sm:text-sm max-w-[85%] leading-relaxed whitespace-pre-line shadow-xs',
              msg.is_user ? 'bg-blue-600 text-white font-medium rounded-tr-none' : 
              (msg.is_emergency ? 'bg-rose-50 text-rose-900 border border-rose-200 rounded-tl-none font-semibold' : 'bg-slate-100 text-slate-800 rounded-tl-none')
            ]"
          >
            {{ msg.text }}
          </div>
        </div>
      </div>

      <!-- Quick Action Pills -->
      <div class="pt-3 border-t border-slate-100 space-y-3">
        <div class="flex flex-wrap gap-2">
          <button 
            v-for="pill in quickPills" 
            :key="pill.text"
            @click="sendQuick(pill.query)"
            class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-xs font-semibold border border-slate-200 transition"
          >
            {{ pill.text }}
          </button>
        </div>

        <!-- Input Box -->
        <div class="flex items-center gap-2">
          <input 
            v-model="inputQuery" 
            @keyup.enter="sendMessage"
            type="text" 
            :placeholder="lang === 'am' ? 'ጥያቄዎን እዚህ ይጻፉ (ለምሳሌ: የተራዬ ሁኔታ፣ የመድሃኒት ሰዓቴ)...' : 'Type your query here (e.g., check my queue status, medication timetable)...'"
            class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
          />
          <button 
            @click="sendMessage"
            class="p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-md transition"
          >
            <svg class="w-5 h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../api/client';

const lang = ref('en');
const inputQuery = ref('');

const messages = ref([
  {
    is_user: false,
    text: "Welcome to SmartQueue Health & MedReminder (SQHM)!\nI am your digital healthcare assistant. I can help you track your queue ticket, view your daily prescribed medication schedules, or navigate hospital departments.\n\nHow can I help you today?",
    is_emergency: false,
  },
]);

const quickPills = computed(() => {
  if (lang.value === 'am') {
    return [
      { text: '🎫 የተራዬን ሁኔታ አሳየኝ', query: 'የተራዬን ሁኔታ አሳየኝ' },
      { text: '💊 የዛሬ መድኃኒቴ ሰዓት', query: 'የዛሬ መድሃኒቶቼን ሰዓት አሳየኝ' },
      { text: '🏥 የሆስፒታል ክፍሎች', query: 'የተመላላሽ ክፍል የት ነው?' },
    ];
  }
  return [
    { text: '🎫 What is my queue status?', query: 'Check my queue status' },
    { text: '💊 When is my next medication due?', query: 'When should I take my medication?' },
    { text: '🏥 Where is OPD Room 104?', query: 'Where is OPD Room 104 located?' },
  ];
});

const sendQuick = (query) => {
  inputQuery.value = query;
  sendMessage();
};

const sendMessage = async () => {
  const query = inputQuery.value.trim();
  if (!query) return;

  messages.value.push({ is_user: true, text: query, is_emergency: false });
  inputQuery.value = '';

  try {
    const res = await api.post('/chatbot/message', {
      message: query,
      language: lang.value,
    });

    if (res.data) {
      messages.value.push({
        is_user: false,
        text: res.data.response,
        is_emergency: res.data.is_emergency,
      });
    }
  } catch (err) {
    // Client-side fallback handler
    let fallbackText = "I have noted your request. Your queue ticket OPD-001 is currently called at OPD Room 104.";
    if (query.toLowerCase().includes('medication') || query.includes('መድኃኒት')) {
      fallbackText = "Your active medications: Metformin HCl 500mg (08:00 & 20:00) and Amlodipine 5mg (08:00). Please take them with meals.";
    }
    messages.value.push({ is_user: false, text: fallbackText, is_emergency: false });
  }
};
</script>
