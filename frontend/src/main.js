import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import './index.css';

// Views
import DashboardView from './views/DashboardView.vue';
import QueueBoardView from './views/QueueBoardView.vue';
import ReceptionView from './views/ReceptionView.vue';
import TriageView from './views/TriageView.vue';
import DoctorView from './views/DoctorView.vue';
import PharmacyView from './views/PharmacyView.vue';
import LabRadiologyView from './views/LabRadiologyView.vue';
import InpatientView from './views/InpatientView.vue';
import BillingView from './views/BillingView.vue';
import MedReminderView from './views/MedReminderView.vue';
import ChatbotView from './views/ChatbotView.vue';

const routes = [
  { path: '/', name: 'dashboard', component: DashboardView },
  { path: '/queue-board', name: 'queue-board', component: QueueBoardView },
  { path: '/reception', name: 'reception', component: ReceptionView },
  { path: '/triage', name: 'triage', component: TriageView },
  { path: '/doctor', name: 'doctor', component: DoctorView },
  { path: '/pharmacy', name: 'pharmacy', component: PharmacyView },
  { path: '/lab', name: 'lab', component: LabRadiologyView },
  { path: '/inpatient', name: 'inpatient', component: InpatientView },
  { path: '/billing', name: 'billing', component: BillingView },
  { path: '/medreminder', name: 'medreminder', component: MedReminderView },
  { path: '/chatbot', name: 'chatbot', component: ChatbotView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

const app = createApp(App);
app.use(router);
app.mount('#app');
